<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\TagihanPo;
use App\Models\TagihanPoItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagihanPoServices
{
    /**
     * Auto-create tagihan saat PO External dibuat (status: draft)
     */
    public function createTagihanFromPO(PurchaseOrder $po)
    {
        // Hanya untuk PO External
        if ($po->tipe_po !== 'eksternal') {
            Log::info('Skip create tagihan - PO bukan eksternal', ['po_id' => $po->id_po]);
            return null;
        }

        // Cek apakah sudah ada tagihan
        if ($po->hasTagihan()) {
            Log::info('Skip create tagihan - Already exists', ['po_id' => $po->id_po]);
            return $po->tagihan;
        }

        DB::beginTransaction();
        try {
            // Create tagihan header (status: draft)
            $tagihan = TagihanPo::create([
                'id_po' => $po->id_po,
                'id_supplier' => $po->id_supplier,
                'status' => 'draft',
                'total_tagihan' => $po->total_harga,
                'pajak' => $po->pajak,
                'grand_total' => $po->grand_total,
                'total_dibayar' => 0,
                'sisa_tagihan' => $po->grand_total,
                'tenor_hari' => 30, // Default 30 hari
                'id_karyawan_buat' => $po->id_karyawan_pemohon,
                'catatan' => 'Auto-generated dari PO: ' . $po->no_po,
            ]);

            // Create tagihan items
            foreach ($po->items as $item) {
                TagihanPoItem::create([
                    'id_tagihan' => $tagihan->id_tagihan,
                    'id_po_item' => $item->id_po_item,
                    'id_produk' => $item->id_produk,
                    'nama_produk' => $item->nama_produk,
                    'qty_diminta' => $item->qty_diminta,
                    'qty_diterima' => 0, // Belum diterima
                    'qty_ditagihkan' => 0, // Belum ada yang ditagihkan
                    'harga_satuan' => $item->harga_satuan,
                    'subtotal' => 0, // Akan dihitung saat barang diterima
                    'batch_number' => $item->batch_number,
                    'tanggal_kadaluarsa' => $item->tanggal_kadaluarsa,
                ]);
            }

            DB::commit();

            Log::info('Tagihan created successfully', [
                'tagihan_id' => $tagihan->id_tagihan,
                'no_tagihan' => $tagihan->no_tagihan,
                'po_id' => $po->id_po
            ]);

            return $tagihan;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create tagihan', [
                'po_id' => $po->id_po,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update tagihan saat barang diterima
     * Hitung ulang berdasarkan qty yang DITERIMA
     */
    public function updateTagihanAfterReceipt(PurchaseOrder $po)
    {
        if ($po->tipe_po !== 'eksternal') {
            return null;
        }

        if (!$po->hasTagihan()) {
            Log::warning('Tagihan not found for PO', ['po_id' => $po->id_po]);
            return null;
        }

        DB::beginTransaction();
        try {
            $tagihan = $po->tagihan;

            // Recalculate total berdasarkan qty_diterima
            $totalTagihan = 0;

            foreach ($tagihan->items as $tagihanItem) {
                $poItem = $po->items->firstWhere('id_po_item', $tagihanItem->id_po_item);

                if ($poItem) {
                    $qtyDiterima = $poItem->qty_diterima ?? 0;
                    $subtotal = $qtyDiterima * $tagihanItem->harga_satuan;

                    $tagihanItem->update([
                        'qty_diterima' => $qtyDiterima,
                        'qty_ditagihkan' => $qtyDiterima,
                        'subtotal' => $subtotal,
                        'batch_number' => $poItem->batch_number,
                        'tanggal_kadaluarsa' => $poItem->tanggal_kadaluarsa,
                    ]);

                    $totalTagihan += $subtotal;
                }
            }

            // Update tagihan header
            $grandTotal = $totalTagihan + $tagihan->pajak;

            $tagihan->update([
                'status' => 'menunggu_pembayaran',
                'total_tagihan' => $totalTagihan,
                'grand_total' => $grandTotal,
                'sisa_tagihan' => $grandTotal - $tagihan->total_dibayar,
                'tanggal_tagihan' => now(),
                'tanggal_jatuh_tempo' => now()->addDays((int) $tagihan->tenor_hari),
            ]);

            DB::commit();

            Log::info('Tagihan updated after receipt', [
                'tagihan_id' => $tagihan->id_tagihan,
                'total_tagihan' => $totalTagihan,
                'grand_total' => $grandTotal
            ]);

            return $tagihan;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update tagihan after receipt', [
                'po_id' => $po->id_po,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Cancel tagihan (jika PO dibatalkan/ditolak)
     */
    public function cancelTagihan(PurchaseOrder $po)
    {
        if (!$po->hasTagihan()) {
            return null;
        }

        $tagihan = $po->tagihan;

        // Hanya bisa cancel jika belum ada pembayaran
        if ($tagihan->total_dibayar > 0) {
            throw new \Exception('Tagihan tidak dapat dibatalkan karena sudah ada pembayaran');
        }

        $tagihan->update(['status' => 'dibatalkan']);

        Log::info('Tagihan cancelled', [
            'tagihan_id' => $tagihan->id_tagihan,
            'po_id' => $po->id_po
        ]);

        return $tagihan;
    }
}
