<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailGudang;
use App\Models\DetailstockApotik;
use App\Models\DetailSupplier;
use App\Models\Gudang;
use App\Models\Karyawan;
use App\Models\PoAuditTrail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockApotik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // Only PO Internal that has been approved by Kepala Gudang
        if ($po->tipe_po !== 'internal' || $po->status !== 'selesai') {
            return redirect()->route('po.show', $id_po)
                ->with('error', 'PO ini tidak memerlukan konfirmasi penerimaan');
        }

        return view('po.confirm-receipt', compact('po'));
    }

    /**
     * Confirm receipt of goods and update stock apotik
     */
    public function confirmReceipt(Request $request, $id_po)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
            'items' => 'required|array|min:1',
            'items.*.id_po_item' => 'required|uuid',  // ← UBAH DARI id_item
            'items.*.qty_diterima' => 'required|integer|min:0',
            'items.*.kondisi' => 'required|in:baik,rusak,kadaluarsa',
            'items.*.catatan' => 'nullable|string',
            'catatan_penerima' => 'nullable|string',
        ], [
            'items.*.id_po_item.required' => 'ID item tidak ditemukan. Mohon refresh halaman.',  // ← UBAH
            'items.*.id_po_item.uuid' => 'Format ID item tidak valid.',  // ← UBAH
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // Verifikasi PIN
        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'PIN tidak valid'], 403);
            }
            return back()->withErrors(['pin' => 'PIN tidak valid'])->withInput();
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::with('items')->findOrFail($id_po);

            // Validate PO status
            if ($po->tipe_po !== 'internal' || $po->status !== 'selesai') {
                throw new \Exception('PO ini tidak dapat dikonfirmasi');
            }

            $dataBefore = $po->toArray();

            // Get gudang
            $gudang = Gudang::first();
            if (!$gudang) {
                throw new \Exception('Gudang tidak ditemukan');
            }

            // Create or get stock apotik transaction
            $stockApotik = StockApotik::create([
                'id' => (string) Str::uuid(),
                'gudang_id' => $gudang->id,
                'kode_transaksi' => 'APO-CONF-' . date('YmdHis') . '-' . substr($po->no_po, -3),
                'tanggal_penerimaan' => now(),
                'keterangan' => 'Konfirmasi Penerimaan PO: ' . $po->no_po . ($request->catatan_penerima ? ' - ' . $request->catatan_penerima : ''),
            ]);

            $totalDiterima = 0;
            $totalRusak = 0;
            $itemsProcessed = [];

            foreach ($request->items as $itemData) {
                $poItem = PurchaseOrderItem::findOrFail($itemData['id_po_item']);
                $produk = DetailSupplier::find($poItem->id_produk);

                if (!$produk) {
                    throw new \Exception("Produk dengan ID {$poItem->id_produk} tidak ditemukan");
                }

                $qtyDiterima = (int) $itemData['qty_diterima'];
                $kondisi = $itemData['kondisi'];
                $catatan = $itemData['catatan'] ?? null;

                // Get detail gudang for batch info
                $detailGudang = DetailGudang::where('barang_id', $poItem->id_produk)
                    ->where('gudang_id', $gudang->id)
                    ->first();

                // Update PO Item with actual received quantity
                $poItem->update([
                    'qty_diterima' => $qtyDiterima,
                    'kondisi_barang' => $kondisi,
                    'catatan_penerimaan' => $catatan,
                ]);

                // Process based on condition
                if ($kondisi === 'baik' && $qtyDiterima > 0) {
                    // Check if product already exists in apotik
                    $existingDetail = DetailstockApotik::where('obat_id', $poItem->id_produk)
                        ->where('stock_apotik_id', $stockApotik->id)
                        ->first();

                    if ($existingDetail) {
                        // Update existing stock - ADD to current stock
                        $existingDetail->increment('stock_apotik', $qtyDiterima);

                        $itemsProcessed[] = [
                            'action' => 'updated',
                            'product' => $produk->nama,
                            'qty' => $qtyDiterima,
                            'previous_stock' => $existingDetail->stock_apotik - $qtyDiterima,
                            'new_stock' => $existingDetail->stock_apotik,
                        ];
                    } else {
                        // Create new stock entry
                        $newDetail = DetailstockApotik::create([
                            'id' => (string) Str::uuid(),
                            'stock_apotik_id' => $stockApotik->id,
                            'obat_id' => $poItem->id_produk,
                            'no_batch' => $detailGudang->no_batch ?? 'BATCH-' . date('Ymd'),
                            'stock_apotik' => $qtyDiterima,
                            'min_persediaan' => $produk->min_persediaan ?? 0,
                            'tanggal_kadaluarsa' => $detailGudang->tanggal_kadaluarsa ?? null,
                        ]);

                        $itemsProcessed[] = [
                            'action' => 'created',
                            'product' => $produk->nama,
                            'qty' => $qtyDiterima,
                            'new_stock' => $qtyDiterima,
                        ];
                    }

                    $totalDiterima += $qtyDiterima;
                } elseif (in_array($kondisi, ['rusak', 'kadaluarsa'])) {
                    // Record as retur
                    $existingDetail = DetailstockApotik::where('obat_id', $poItem->id_produk)
                        ->where('stock_apotik_id', $stockApotik->id)
                        ->first();

                    if ($existingDetail) {
                        $existingDetail->increment('retur', $qtyDiterima);
                    } else {
                        DetailstockApotik::create([
                            'id' => (string) Str::uuid(),
                            'stock_apotik_id' => $stockApotik->id,
                            'obat_id' => $poItem->id_produk,
                            'no_batch' => $detailGudang->no_batch ?? 'BATCH-' . date('Ymd'),
                            'stock_apotik' => 0,
                            'retur' => $qtyDiterima,
                            'min_persediaan' => $produk->min_persediaan ?? 0,
                            'tanggal_kadaluarsa' => $detailGudang->tanggal_kadaluarsa ?? null,
                        ]);
                    }

                    $totalRusak += $qtyDiterima;

                    $itemsProcessed[] = [
                        'action' => 'retur',
                        'product' => $produk->nama,
                        'qty' => $qtyDiterima,
                        'reason' => $kondisi,
                    ];
                }
            }

            // Update PO status
            $po->update([
                'status' => 'diterima',
                'tanggal_diterima' => now(),
                'id_penerima' => Auth::user()->id_karyawan,
                'catatan_penerima' => $request->catatan_penerima,
            ]);

            // Audit Trail
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'konfirmasi_penerimaan',
                'deskripsi_aksi' => "Konfirmasi penerimaan barang - Total diterima: {$totalDiterima}, Total rusak/kadaluarsa: {$totalRusak}",
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->fresh()->toArray(),
            ]);

            DB::commit();

            $message = "Konfirmasi penerimaan berhasil! Total {$totalDiterima} item masuk ke apotik";
            if ($totalRusak > 0) {
                $message .= ", {$totalRusak} item ditandai sebagai retur";
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'data' => $po->fresh(),
                    'items_processed' => $itemsProcessed,
                ], 200);
            }

            return redirect()->route('po.show', $po->id_po)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Gagal konfirmasi: ' . $e->getMessage()], 500);
            }

            return back()->withErrors(['error' => 'Gagal konfirmasi: ' . $e->getMessage()])->withInput();
        }
    }
}
