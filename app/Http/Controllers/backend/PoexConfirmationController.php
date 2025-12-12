<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailGudang;
use App\Models\DetailSupplier;
use App\Models\Gudang;
use App\Models\Karyawan;
use App\Models\PoAuditTrail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItemBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PoexConfirmationController extends Controller
{
    public function showConfirmation($id_po)
    {
        $po = PurchaseOrder::with([
            'items.produk',
            'items.batches',
            'karyawanPemohon',
            'kepalaGudang',
            'kasir',
            'supplier',
            'shippingActivities'
        ])->findOrFail($id_po);

        // Validasi: Cek apakah PO ini perlu konfirmasi
        if (!$po->needsReceiptConfirmation()) {
            return redirect()->route('po.show', $id_po)
                ->with('error', 'PO ini tidak memerlukan konfirmasi penerimaan atau sudah dikonfirmasi');
        }

        return view('po.confirmation', compact('po'));
    }

    /**
     * Proses konfirmasi penerimaan barang (dengan multiple batch support)
     */
    public function confirmReceipt(Request $request, $id_po)
    {
        $validated = $request->validate([
            'pin' => 'required|size:6',
            'catatan_penerima' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_po_item' => 'required|uuid',
            'items.*.batches' => 'required|array|min:1',
            'items.*.batches.*.batch_number' => 'required|string',
            'items.*.batches.*.tanggal_kadaluarsa' => 'required|date',
            'items.*.batches.*.qty_diterima' => 'required|integer|min:1',
            'items.*.batches.*.kondisi' => 'required|in:baik,rusak,kadaluarsa',
            'items.*.batches.*.catatan' => 'nullable|string',
        ]);

        // Verifikasi PIN
        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return back()->withErrors(['pin' => 'PIN tidak valid'])->withInput();
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::with('items')->findOrFail($id_po);

            // Validasi ulang
            if (!$po->needsReceiptConfirmation()) {
                DB::rollBack();
                return redirect()->route('po.show', $id_po)
                    ->with('error', 'PO ini tidak memerlukan konfirmasi atau sudah dikonfirmasi');
            }

            $dataBefore = $po->toArray();

            // Generate nomor GR
            $noGR = PurchaseOrder::generateNoGR();

            // Process setiap item dengan batch-nya
            foreach ($request->items as $itemData) {
                $item = $po->items->firstWhere('id_po_item', $itemData['id_po_item']);

                if (!$item) {
                    throw new \Exception("Item dengan ID {$itemData['id_po_item']} tidak ditemukan");
                }

                $totalQtyDiterima = 0;

                // Hapus batch lama jika ada (untuk re-confirmation)
                $item->batches()->delete();

                // Simpan setiap batch
                foreach ($itemData['batches'] as $batchData) {
                    PurchaseOrderItemBatch::create([
                        'id_po_item' => $item->id_po_item,
                        'batch_number' => $batchData['batch_number'],
                        'tanggal_kadaluarsa' => $batchData['tanggal_kadaluarsa'],
                        'qty_diterima' => $batchData['qty_diterima'],
                        'kondisi' => $batchData['kondisi'],
                        'catatan' => $batchData['catatan'] ?? null,
                    ]);

                    $totalQtyDiterima += $batchData['qty_diterima'];
                }

                // Update total qty_diterima di item
                $item->update([
                    'qty_diterima' => $totalQtyDiterima,
                ]);
            }

            // Transfer stok ke gudang/apotik
            if ($po->tipe_po === 'internal') {
                // $this->transferStockGudangToApotik($po);
            } else {
                $this->addStockToGudang($po);
            }

            // Update PO dengan nomor GR
            $po->update([
                'no_gr' => $noGR,
                'status' => 'diterima',
                'id_penerima' => Auth::user()->id_karyawan,
                'tanggal_diterima' => now(),
                'catatan_penerima' => $request->catatan_penerima,
            ]);

            // Audit Trail
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'konfirmasi_penerimaan',
                'deskripsi_aksi' => "Mengkonfirmasi penerimaan barang PO {$po->tipe_po} dengan nomor GR: {$noGR}",
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->fresh()->toArray(),
            ]);

            DB::commit();

            return redirect()->route('po.show', $po->id_po)
                ->with('success', "Penerimaan barang berhasil dikonfirmasi dengan nomor GR: {$noGR}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal konfirmasi: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Tambah stok ke gudang (untuk PO Eksternal dari Supplier)
     */
    private function addStockToGudang(PurchaseOrder $po)
    {
        $gudang = Gudang::where('supplier_id', $po->id_supplier)->first();

        if (!$gudang) {
            $gudang = Gudang::where('status', 'Aktif')->first();
        }

        if (!$gudang) {
            throw new \Exception('Gudang tidak ditemukan');
        }

        foreach ($po->items as $item) {
            $produk = DetailSupplier::find($item->id_produk);

            if (!$produk) {
                throw new \Exception("Produk dengan ID {$item->id_produk} tidak ditemukan");
            }

            // Proses setiap batch
            foreach ($item->batches as $batch) {
                if ($batch->qty_diterima <= 0) {
                    continue;
                }

                // Cek apakah sudah ada di gudang dengan batch yang sama
                $detailGudang = DetailGudang::where('gudang_id', $gudang->id)
                    ->where('barang_id', $item->id_produk)
                    ->where('no_batch', $batch->batch_number)
                    ->first();

                if ($detailGudang) {
                    // Update stok yang ada
                    if ($batch->kondisi === 'baik') {
                        $detailGudang->increment('stock_gudang', $batch->qty_diterima);
                    }
                    // Update kondisi jika perlu
                    if ($batch->kondisi !== 'baik') {
                        $detailGudang->update(['kondisi' => ucfirst($batch->kondisi)]);
                    }
                } else {
                    // Buat record baru untuk batch ini
                    DetailGudang::create([
                        'id' => (string) Str::uuid(),
                        'gudang_id' => $gudang->id,
                        'barang_type' => 'obat',
                        'barang_id' => $item->id_produk,
                        'no_batch' => $batch->batch_number,
                        'stock_gudang' => $batch->kondisi === 'baik' ? $batch->qty_diterima : 0,
                        'min_persediaan' => $produk->min_persediaan ?? 0,
                        'tanggal_masuk' => now(),
                        'tanggal_kadaluarsa' => $batch->tanggal_kadaluarsa,
                        'kondisi' => ucfirst($batch->kondisi),
                    ]);
                }
            }

            // Update stock_po di detail_suppliers
            if ($po->tipe_po === 'eksternal' && $item->getTotalQtyBaikFromBatches() > 0) {
                $produk->decrement('stock_po', $item->getTotalQtyBaikFromBatches());
            }

            // Update qty_disetujui jika belum ada
            if (!$item->qty_disetujui) {
                $item->update(['qty_disetujui' => $item->qty_diminta]);
            }
        }
    }

    /**
     * Form input invoice/faktur
     */
    public function showInvoiceForm($id_po)
    {
        $po = PurchaseOrder::with(['supplier', 'items'])->findOrFail($id_po);

        // Validasi: Hanya PO eksternal yang sudah diterima dan belum ada invoice
        if (!$po->needsInvoice()) {
            return redirect()->route('po.show', $id_po)
                ->with('error', 'PO ini tidak memerlukan input invoice atau sudah diinput');
        }

        return view('po.invoice', compact('po'));
    }

    /**
     * Simpan data invoice/faktur
     */
    public function storeInvoice(Request $request, $id_po)
    {
        $validated = $request->validate([
            'pin' => 'required|size:6',
            'no_invoice' => 'required|string|max:100',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_invoice',
            'nomor_faktur_pajak' => 'nullable|string|max:100',
        ]);

        // Verifikasi PIN
        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return back()->withErrors(['pin' => 'PIN tidak valid'])->withInput();
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::findOrFail($id_po);

            if (!$po->needsInvoice()) {
                DB::rollBack();
                return redirect()->route('po.show', $id_po)
                    ->with('error', 'PO ini tidak memerlukan input invoice');
            }

            $dataBefore = $po->toArray();

            // Update invoice data
            $po->update([
                'no_invoice' => $request->no_invoice,
                'tanggal_invoice' => $request->tanggal_invoice,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'nomor_faktur_pajak' => $request->nomor_faktur_pajak,
                'id_karyawan_input_invoice' => Auth::user()->id_karyawan,
                'tanggal_input_invoice' => now(),
            ]);

            // Audit Trail
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'input_invoice',
                'deskripsi_aksi' => "Input invoice: {$request->no_invoice}",
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->fresh()->toArray(),
            ]);

            DB::commit();

            return redirect()->route('po.show', $po->id_po)
                ->with('success', 'Data invoice berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan invoice: ' . $e->getMessage()])->withInput();
        }
    }
}
