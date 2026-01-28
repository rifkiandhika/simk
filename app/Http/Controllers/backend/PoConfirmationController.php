<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailGudang;
use App\Models\DetailobatRs;
use App\Models\DetailstockApotik;
use App\Models\DetailSupplier;
use App\Models\Gudang;
use App\Models\HistoryGudang;
use App\Models\Karyawan;
use App\Models\PoAuditTrail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockApotik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PoConfirmationController extends Controller
{
    public function showConfirmation($id_po)
    {
        $po = PurchaseOrder::with([
            'items.produk',
            'karyawanPemohon',
            'kepalaGudang',
        ])->findOrFail($id_po);

        // ✅ Only PO Internal with status 'dikirim' (barang siap diterima)
        if ($po->tipe_po !== 'internal' || $po->status !== 'dikirim') {
            return redirect()->route('po.show', $id_po)
                ->with('error', 'PO ini tidak memerlukan konfirmasi penerimaan');
        }

        Log::info('Show Confirmation Page:', [
            'po_id' => $id_po,
            'no_po' => $po->no_po,
            'tipe' => $po->tipe_po,
            'status' => $po->status,
            'items_count' => $po->items->count()
        ]);

        return view('po.confirm-receipt', compact('po'));
    }

    /**
     * Confirm receipt of goods and update stock apotik (PO INTERNAL ONLY)
     */
    public function confirmReceipt(Request $request, $id_po)
    {
        Log::info('=== START CONFIRM RECEIPT ===', [
            'po_id' => $id_po,
            'user_id' => Auth::user()->id_karyawan,
            'request_data' => $request->except('pin')
        ]);

        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
            'items' => 'required|array|min:1',
            'items.*.id_po_item' => 'required|uuid',
            'items.*.qty_diterima' => 'required|integer|min:0',
            'items.*.kondisi' => 'required|in:baik,rusak,kadaluarsa',
            'items.*.catatan' => 'nullable|string',
            'catatan_penerima' => 'nullable|string',
        ], [
            'items.*.id_po_item.required' => 'ID item tidak ditemukan. Mohon refresh halaman.',
            'items.*.id_po_item.uuid' => 'Format ID item tidak valid.',
        ]);

        if ($validator->fails()) {
            Log::error('Validation Failed:', $validator->errors()->toArray());
            
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 422);
        }

        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            Log::warning('Invalid PIN', ['user_id' => Auth::user()->id_karyawan]);
            
            return response()->json([
                'success' => false,
                'error' => 'PIN tidak valid'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::with('items')->findOrFail($id_po);

            Log::info('PO Loaded:', [
                'po_id' => $po->id_po,
                'no_po' => $po->no_po,
                'tipe_po' => $po->tipe_po,
                'status' => $po->status,
                'items_count' => $po->items->count()
            ]);

            if ($po->tipe_po !== 'internal') {
                throw new \Exception('Hanya PO Internal yang dapat dikonfirmasi melalui halaman ini');
            }

            if ($po->status !== 'dikirim') {
                throw new \Exception('PO ini belum disetujui atau sudah dikonfirmasi sebelumnya. Status: ' . $po->status);
            }

            // ✅ VALIDASI: no_gr harus sudah ada (dari approval Kepala Gudang)
            if (!$po->no_gr) {
                throw new \Exception('Nomor GR belum tersedia. Silakan minta Kepala Gudang untuk approve PO terlebih dahulu.');
            }

            $dataBefore = $po->toArray();
            // ✅ TIDAK GENERATE no_gr lagi, gunakan yang sudah ada
            $noGr = $po->no_gr;

            $gudang = Gudang::first();
            if (!$gudang) {
                throw new \Exception('Gudang tidak ditemukan di sistem');
            }

            Log::info('Gudang Found:', ['gudang_id' => $gudang->id, 'nama' => $gudang->nama_gudang ?? 'N/A']);

            $totalDiterima = 0;
            $totalRusak = 0;
            $itemsProcessed = [];

            foreach ($request->items as $itemData) {
                Log::info('Processing Item:', $itemData);

                $poItem = PurchaseOrderItem::findOrFail($itemData['id_po_item']);

                Log::info('PO Item Found:', [
                    'id_po_item' => $poItem->id_po_item,
                    'id_produk' => $poItem->id_produk,
                    'nama_produk' => $poItem->nama_produk,
                    'qty_diminta' => $poItem->qty_diminta
                ]);

                $produk = DetailobatRs::find($poItem->id_produk);

                if (!$produk) {
                    Log::error('Produk Not Found', ['id_produk' => $poItem->id_produk]);
                    throw new \Exception("Produk dengan ID {$poItem->id_produk} tidak ditemukan di master data");
                }

                $qtyDiterima = (int) $itemData['qty_diterima'];
                $kondisi = $itemData['kondisi'];
                $catatan = $itemData['catatan'] ?? null;

                if ($qtyDiterima == 0) {
                    Log::info('Skipping item with qty = 0', ['id_po_item' => $poItem->id_po_item]);
                    continue;
                }

                $detailGudang = DetailGudang::where('barang_id', $poItem->id_produk)
                    ->where('gudang_id', $gudang->id)
                    ->where('stock_gudang', '>', 0)
                    ->orderBy('tanggal_kadaluarsa', 'asc')
                    ->first();

                if (!$detailGudang) {
                    Log::error('Detail Gudang Not Found', [
                        'barang_id' => $poItem->id_produk,
                        'gudang_id' => $gudang->id
                    ]);
                    throw new \Exception("Produk {$produk->nama} tidak ditemukan di gudang atau stock habis");
                }

                if ($detailGudang->stock_gudang < $qtyDiterima) {
                    Log::error('Insufficient Stock', [
                        'available' => $detailGudang->stock_gudang,
                        'requested' => $qtyDiterima
                    ]);
                    throw new \Exception("Stock {$produk->nama} (Batch: {$detailGudang->no_batch}) tidak mencukupi. Tersedia: {$detailGudang->stock_gudang}, Diminta: {$qtyDiterima}");
                }

                $poItem->update([
                    'qty_diterima' => $qtyDiterima,
                    'qty_disetujui' => $qtyDiterima,
                    'kondisi_barang' => $kondisi,
                    'catatan_penerimaan' => $catatan,
                    'batch_number' => $detailGudang->no_batch,
                    'tanggal_kadaluarsa' => $detailGudang->tanggal_kadaluarsa,
                ]);

                $stockBefore = $detailGudang->stock_gudang;
                $detailGudang->decrement('stock_gudang', $qtyDiterima);
                $detailGudang->refresh();

                Log::info('Stock Gudang Updated:', [
                    'stock_before' => $stockBefore,
                    'decrement' => $qtyDiterima,
                    'stock_after' => $detailGudang->stock_gudang
                ]);

                HistoryGudang::create([
                    'gudang_id' => $gudang->id,
                    'supplier_id' => null,
                    'barang_id' => $poItem->id_produk,
                    'no_batch' => $detailGudang->no_batch,
                    'jumlah' => $qtyDiterima,
                    'waktu_proses' => now(),
                    'status' => 'pengiriman',
                    'referensi_type' => 'po_internal',
                    'referensi_id' => $po->id_po,
                    'no_referensi' => $noGr, // ✅ Gunakan no_gr yang sudah ada
                    'keterangan' => "Pengiriman barang ke Apotik - PO Internal: {$po->no_po}, GR: {$noGr}, Unit: {$po->unit_pemohon}, Kondisi: {$kondisi}",
                ]);

                if ($kondisi === 'baik') {
                    Log::info('Processing BAIK item...');
                    $this->addToStockApotik($gudang, $poItem, $detailGudang, $qtyDiterima, $produk, $po);
                    $totalDiterima += $qtyDiterima;

                    $itemsProcessed[] = [
                        'action' => 'received',
                        'product' => $produk->nama,
                        'batch' => $detailGudang->no_batch,
                        'qty' => $qtyDiterima,
                        'kondisi' => 'baik',
                    ];
                } elseif (in_array($kondisi, ['rusak', 'kadaluarsa'])) {
                    Log::info('Processing RUSAK/KADALUARSA item...');
                    $this->addToRetur($gudang, $poItem, $detailGudang, $qtyDiterima, $produk, $po);
                    $totalRusak += $qtyDiterima;

                    $itemsProcessed[] = [
                        'action' => 'retur',
                        'product' => $produk->nama,
                        'batch' => $detailGudang->no_batch,
                        'qty' => $qtyDiterima,
                        'kondisi' => $kondisi,
                    ];
                }
            }

            // ✅ TIDAK update no_gr lagi, karena sudah ada
            $po->update([
                'status' => 'selesai',
                'tanggal_diterima' => now(),
                'id_penerima' => Auth::user()->id_karyawan,
                'catatan_penerima' => $request->catatan_penerima,
            ]);

            Log::info('PO Status Updated to SELESAI');

            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'konfirmasi_penerimaan',
                'deskripsi_aksi' => "Konfirmasi penerimaan barang dengan GR: {$noGr} - Diterima: {$totalDiterima}, Retur: {$totalRusak}",
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->fresh()->toArray(),
            ]);

            DB::commit();

            Log::info('=== CONFIRM RECEIPT SUCCESS ===', [
                'po_id' => $po->id_po,
                'no_gr' => $noGr,
                'total_diterima' => $totalDiterima,
                'total_rusak' => $totalRusak,
                'items_processed' => count($itemsProcessed)
            ]);

            $message = "✓ Konfirmasi penerimaan berhasil dengan nomor GR: {$noGr}!";
            if ($totalDiterima > 0) {
                $message .= " {$totalDiterima} unit masuk ke stock apotik.";
            }
            if ($totalRusak > 0) {
                $message .= " {$totalRusak} unit ditandai sebagai retur.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $po->fresh()->load('items'),
                'items_processed' => $itemsProcessed,
                'no_gr' => $noGr,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== CONFIRM RECEIPT ERROR ===', [
                'po_id' => $id_po,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal konfirmasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add item to stock apotik (barang baik)
     */
    private function addToStockApotik($gudang, $poItem, $detailGudang, $qty, $produk, $po)
    {
        Log::info('Adding to Stock Apotik...', [
            'produk' => $produk->nama,
            'batch' => $detailGudang->no_batch,
            'qty' => $qty
        ]);

        // Cari stock_apotik header
        $stockApotik = StockApotik::where('gudang_id', $gudang->id)
            ->whereDate('tanggal_penerimaan', now()->toDateString())
            ->where('keterangan', 'like', '%PO Internal%')
            ->first();

        if (!$stockApotik) {
            $stockApotik = StockApotik::create([
                'id' => (string) Str::uuid(),
                'gudang_id' => $gudang->id,
                'kode_transaksi' => 'APO-INT-' . date('YmdHis'),
                'tanggal_penerimaan' => now(),
                'keterangan' => 'Transfer dari Gudang - PO Internal: ' . $po->no_po . ', GR: ' . $po->no_gr,
            ]);

            Log::info('StockApotik Header Created', ['id' => $stockApotik->id]);
        } else {
            Log::info('Using Existing StockApotik Header', ['id' => $stockApotik->id]);
        }

        // Cek existing detail
        $existingDetail = DetailstockApotik::where('detail_obat_rs_id', $poItem->id_produk)
            ->where('no_batch', $detailGudang->no_batch)
            ->first();

        if ($existingDetail) {
            $stockBefore = $existingDetail->stock_apotik;
            $existingDetail->increment('stock_apotik', $qty);
            $existingDetail->refresh();

            Log::info('Stock Apotik UPDATED (Increment)', [
                'id' => $existingDetail->id,
                'stock_before' => $stockBefore,
                'increment' => $qty,
                'stock_after' => $existingDetail->stock_apotik
            ]);
        } else {
            $newDetail = DetailstockApotik::create([
                'id' => (string) Str::uuid(),
                'stock_apotik_id' => $stockApotik->id,
                'detail_obat_rs_id' => $poItem->id_produk,
                'no_batch' => $detailGudang->no_batch,
                'stock_apotik' => $qty,
                'min_persediaan' => $produk->min_persediaan ?? 0,
                'tanggal_kadaluarsa' => $detailGudang->tanggal_kadaluarsa,
            ]);

            Log::info('Stock Apotik CREATED', [
                'id' => $newDetail->id,
                'stock_apotik' => $qty,
                'batch' => $detailGudang->no_batch
            ]);
        }
    }

    /**
     * Add item to retur
     */
    private function addToRetur($gudang, $poItem, $detailGudang, $qty, $produk, $po)
    {
        Log::info('Adding to Retur...', [
            'produk' => $produk->nama,
            'batch' => $detailGudang->no_batch,
            'qty' => $qty
        ]);

        $stockApotik = StockApotik::where('gudang_id', $gudang->id)
            ->whereDate('tanggal_penerimaan', now()->toDateString())
            ->where('keterangan', 'like', '%PO Internal%')
            ->first();

        if (!$stockApotik) {
            $stockApotik = StockApotik::create([
                'id' => (string) Str::uuid(),
                'gudang_id' => $gudang->id,
                'kode_transaksi' => 'APO-INT-' . date('YmdHis'),
                'tanggal_penerimaan' => now(),
                'keterangan' => 'Transfer dari Gudang - PO Internal: ' . $po->no_po . ', GR: ' . $po->no_gr,
            ]);

            Log::info('StockApotik Header Created for Retur', ['id' => $stockApotik->id]);
        }

        $existingRetur = DetailstockApotik::where('detail_obat_rs_id', $poItem->id_produk)
            ->where('stock_apotik_id', $stockApotik->id)
            ->where('no_batch', $detailGudang->no_batch)
            ->first();

        if ($existingRetur) {
            $returBefore = $existingRetur->retur ?? 0;
            $existingRetur->increment('retur', $qty);
            $existingRetur->refresh();

            Log::info('Retur UPDATED (Increment)', [
                'id' => $existingRetur->id,
                'retur_before' => $returBefore,
                'increment' => $qty,
                'retur_after' => $existingRetur->retur
            ]);
        } else {
            $newRetur = DetailstockApotik::create([
                'id' => (string) Str::uuid(),
                'stock_apotik_id' => $stockApotik->id,
                'detail_obat_rs_id' => $poItem->id_produk,
                'no_batch' => $detailGudang->no_batch,
                'stock_apotik' => 0,
                'retur' => $qty,
                'min_persediaan' => $produk->min_persediaan ?? 0,
                'tanggal_kadaluarsa' => $detailGudang->tanggal_kadaluarsa,
            ]);

            Log::info('Retur CREATED', [
                'id' => $newRetur->id,
                'retur' => $qty,
                'batch' => $detailGudang->no_batch
            ]);
        }
    }
}