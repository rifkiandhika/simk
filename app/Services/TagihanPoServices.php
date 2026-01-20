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

            // ✅ Hitung subtotal berdasarkan qty_diterima (hanya barang kondisi baik)
            $subtotalDiterima = 0;

            foreach ($tagihan->items as $tagihanItem) {
                $poItem = $po->items->firstWhere('id_po_item', $tagihanItem->id_po_item);

                if ($poItem) {
                    // Hitung qty yang kondisi baik saja dari batches
                    $qtyBaik = 0;
                    foreach ($poItem->batches as $batch) {
                        if ($batch->kondisi === 'baik') {
                            $qtyBaik += $batch->qty_diterima;
                        }
                    }

                    $qtyDiterima = $poItem->qty_diterima ?? 0;
                    $subtotal = $qtyBaik * $tagihanItem->harga_satuan;

                    $tagihanItem->update([
                        'qty_diterima' => $qtyDiterima,
                        'qty_ditagihkan' => $qtyBaik, // Hanya yang kondisi baik
                        'subtotal' => $subtotal,
                        'batch_number' => $poItem->batch_number,
                        'tanggal_kadaluarsa' => $poItem->tanggal_kadaluarsa,
                    ]);

                    $subtotalDiterima += $subtotal;
                }
            }

            // ✅ HITUNG PAJAK PROPORSIONAL - Sama seperti di PoexConfirmationController
            $pajakProporsional = 0;
            $pajakAwal = $po->pajak ?? 0;
            $totalHargaAwal = $po->total_harga ?? 0;

            if ($totalHargaAwal > 0 && $pajakAwal > 0) {
                // Pajak proporsional = (subtotal_diterima / total_harga_awal) × pajak_awal
                $pajakProporsional = ($subtotalDiterima / $totalHargaAwal) * $pajakAwal;
            }

            // ✅ Grand total = subtotal diterima + pajak proporsional
            $grandTotal = $subtotalDiterima + $pajakProporsional;

            // Update tagihan header
            $tagihan->update([
                'status' => 'menunggu_pembayaran',
                'total_tagihan' => $subtotalDiterima, // Subtotal tanpa pajak
                'pajak' => $pajakProporsional, // ✅ Pajak proporsional, bukan pajak awal!
                'grand_total' => $grandTotal, // Subtotal + pajak proporsional
                'sisa_tagihan' => $grandTotal - $tagihan->total_dibayar,
                'tanggal_tagihan' => now(),
                'tanggal_jatuh_tempo' => now()->addDays((int) $tagihan->tenor_hari),
            ]);

            DB::commit();

            Log::info('Tagihan updated after receipt with proportional tax', [
                'tagihan_id' => $tagihan->id_tagihan,
                'subtotal_diterima' => $subtotalDiterima,
                'pajak_awal' => $pajakAwal,
                'pajak_proporsional' => $pajakProporsional,
                'grand_total' => $grandTotal,
                'po_total_diterima' => $po->total_diterima ?? 0,
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
