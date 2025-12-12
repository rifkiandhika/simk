<?php

namespace App\Http\Controllers\Backend;

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
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['items', 'karyawanPemohon', 'supplier']);

        // Filter by role
        if ($request->filled('role')) {
            $query->where('unit_pemohon', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tipe_po
        if ($request->filled('tipe_po')) {
            $query->where('tipe_po', $request->tipe_po);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_po', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($sq) use ($search) {
                        $sq->where('nama_supplier', 'like', "%{$search}%");
                    });
            });
        }

        $purchaseOrders = $query->latest()->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($purchaseOrders, 200);
        }

        return view('po.index', compact('purchaseOrders'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'internal');

        $suppliers = Supplier::where('status', 'Aktif')->get();
        $produkList = DetailSupplier::with('supplier')->get();

        return view('po.create', compact('type', 'suppliers', 'produkList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipe_po' => 'required|in:internal,eksternal',
            'id_unit_pemohon' => 'required',
            'unit_pemohon' => 'required|in:apotik,gudang',
            'catatan_pemohon' => 'nullable|string',
            'id_supplier' => 'required_if:tipe_po,eksternal|nullable|uuid',
            'pin' => 'required|size:6',
            'pajak' => 'nullable|numeric',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|uuid',
            'items.*.qty_diminta' => 'required|integer|min:1',
        ]);

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
            // Hitung total
            $total = 0;
            foreach ($request->items as $item) {
                $produk = DetailSupplier::find($item['id_produk']);
                $total += $produk->harga_beli * $item['qty_diminta'];
            }

            $pajak = $request->pajak ?? 0;
            $grandTotal = $total + $pajak;

            // Create PO
            $po = PurchaseOrder::create([
                'tipe_po' => $request->tipe_po,
                'status' => 'draft',
                'id_unit_pemohon' => $request->id_unit_pemohon,
                'unit_pemohon' => $request->unit_pemohon,
                'id_karyawan_pemohon' => Auth::user()->id_karyawan,
                'tanggal_permintaan' => now(),
                'catatan_pemohon' => $request->catatan_pemohon,
                'unit_tujuan' => $request->tipe_po === 'internal' ? 'gudang' : 'supplier',
                'id_supplier' => $request->id_supplier,
                'total_harga' => $total,
                'pajak' => $pajak,
                'grand_total' => $grandTotal,
            ]);

            // Create items
            foreach ($request->items as $item) {
                $produk = DetailSupplier::find($item['id_produk']);

                PurchaseOrderItem::create([
                    'id_po' => $po->id_po,
                    'id_produk' => $item['id_produk'],
                    'nama_produk' => $produk->nama,
                    'qty_diminta' => $item['qty_diminta'],
                    'harga_satuan' => $produk->harga_beli,
                    'subtotal' => $produk->harga_beli * $item['qty_diminta'],
                ]);

                // Update stock_po untuk PO eksternal
                if ($request->tipe_po === 'eksternal') {
                    $produk->increment('stock_po', $item['qty_diminta']);
                }
            }

            // Audit Trail
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'buat_po',
                'deskripsi_aksi' => 'Membuat PO ' . ucfirst($request->tipe_po),
                'data_sesudah' => $po->toArray(),
            ]);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'PO berhasil dibuat',
                    'data' => $po->load('items')
                ], 201);
            }

            return redirect()->route('po.show', $po->id_po)
                ->with('success', 'Purchase Order berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Gagal membuat PO: ' . $e->getMessage()], 500);
            }

            return back()->withErrors(['error' => 'Gagal membuat PO: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id_po)
    {
        $po = PurchaseOrder::with([
            'items.produk',
            'karyawanPemohon',
            'kepalaGudang',
            'kasir',
            'supplier',
            'shippingActivities.karyawan',
            'auditTrails.karyawan'
        ])->findOrFail($id_po);

        if (request()->wantsJson()) {
            return response()->json($po, 200);
        }

        return view('po.show', compact('po'));
    }

    public function edit($id_po)
    {
        $po = PurchaseOrder::with('items')->findOrFail($id_po);

        if ($po->status !== 'draft') {
            return redirect()->route('po.show', $id_po)
                ->with('error', 'Hanya PO dengan status draft yang dapat diedit');
        }

        $suppliers = Supplier::where('status', 'Aktif')->get();
        $produkList = DetailSupplier::with('supplier')->get();

        return view('po.edit', compact('po', 'suppliers', 'produkList'));
    }

    public function update(Request $request, $id_po)
    {
        $po = PurchaseOrder::findOrFail($id_po);

        if ($po->status !== 'draft') {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Hanya PO draft yang dapat diedit'], 400);
            }
            return back()->with('error', 'Hanya PO dengan status draft yang dapat diedit');
        }

        $validated = $request->validate([
            'catatan_pemohon' => 'nullable|string',
            'pin' => 'required|size:6',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|uuid',
            'items.*.qty_diminta' => 'required|integer|min:1',
        ]);

        // Verifikasi PIN
        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'PIN tidak valid'], 403);
            }
            return back()->withErrors(['pin' => 'PIN tidak valid']);
        }

        DB::beginTransaction();
        try {
            $dataBefore = $po->toArray();

            // Delete old items & reset stock_po
            foreach ($po->items as $oldItem) {
                if ($po->tipe_po === 'eksternal') {
                    $produk = DetailSupplier::find($oldItem->id_produk);
                    $produk->decrement('stock_po', $oldItem->qty_diminta);
                }
            }
            $po->items()->delete();

            // Create new items
            $total = 0;
            foreach ($request->items as $item) {
                $produk = DetailSupplier::find($item['id_produk']);

                PurchaseOrderItem::create([
                    'id_po' => $po->id_po,
                    'id_produk' => $item['id_produk'],
                    'nama_produk' => $produk->nama,
                    'qty_diminta' => $item['qty_diminta'],
                    'harga_satuan' => $produk->harga_beli,
                    'subtotal' => $produk->harga_beli * $item['qty_diminta'],
                ]);

                $total += $produk->harga_beli * $item['qty_diminta'];

                // Update stock_po untuk eksternal
                if ($po->tipe_po === 'eksternal') {
                    $produk->increment('stock_po', $item['qty_diminta']);
                }
            }

            // Update PO
            $po->update([
                'catatan_pemohon' => $request->catatan_pemohon,
                'total_harga' => $total,
                'grand_total' => $total + $po->pajak,
            ]);

            // Audit Trail
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'edit_po',
                'deskripsi_aksi' => 'Mengupdate PO',
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->fresh()->toArray(),
            ]);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'PO berhasil diupdate',
                    'data' => $po->load('items')
                ], 200);
            }

            return redirect()->route('po.show', $po->id_po)
                ->with('success', 'Purchase Order berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Gagal update PO: ' . $e->getMessage()], 500);
            }

            return back()->withErrors(['error' => 'Gagal update PO: ' . $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id_po)
    {
        $request->validate([
            'pin' => 'required|size:6'
        ]);

        // Verifikasi PIN
        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json(['success' => false, 'message' => 'PIN tidak valid'], 403);
        }

        $po = PurchaseOrder::findOrFail($id_po);

        if (!in_array($po->status, ['draft', 'ditolak'])) {
            return response()->json(['success' => false, 'message' => 'PO tidak dapat dihapus'], 400);
        }

        DB::beginTransaction();
        try {
            // Reset stock_po jika eksternal
            if ($po->tipe_po === 'eksternal') {
                foreach ($po->items as $item) {
                    $produk = DetailSupplier::find($item->id_produk);
                    $produk->decrement('stock_po', $item->qty_diminta);
                }
            }

            // Audit Trail
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'hapus_po',
                'deskripsi_aksi' => 'Menghapus PO',
                'data_sebelum' => $po->toArray(),
            ]);

            $po->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'PO berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus PO'], 500);
        }
    }

    public function submit(Request $request, $id_po)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verifikasi PIN
        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json(['error' => 'PIN tidak valid'], 403);
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::findOrFail($id_po);
            $dataBefore = $po->toArray();

            // Status berikutnya selalu menunggu approval kepala gudang
            $newStatus = 'menunggu_persetujuan_kepala_gudang';

            $po->update(['status' => $newStatus]);

            // Audit Trail
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'submit_approval',
                'deskripsi_aksi' => 'Mengirim PO untuk persetujuan',
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->toArray(),
            ]);

            DB::commit();
            return response()->json(['message' => 'PO berhasil diajukan', 'data' => $po], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal submit PO: ' . $e->getMessage()], 500);
        }
    }

    /**
     * APPROVE BY KEPALA GUDANG
     * - PO Internal: Langsung selesai dan transfer stok gudang → apotik
     * - PO Eksternal: Lanjut ke approval kasir
     */
    public function approveKepalaGudang(Request $request, $id_po)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
            'status_approval' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json(['error' => 'PIN tidak valid'], 403);
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::with('items')->findOrFail($id_po);
            $dataBefore = $po->toArray();

            // Update approval kepala gudang
            $po->update([
                'id_kepala_gudang_approval' => Auth::user()->id_karyawan,
                'tanggal_approval_kepala_gudang' => now(),
                'catatan_kepala_gudang' => $request->catatan,
                'status_approval_kepala_gudang' => $request->status_approval,
            ]);

            if ($request->status_approval === 'ditolak') {
                // Jika ditolak, ubah status ke ditolak
                $po->update(['status' => 'ditolak']);

                // Audit Trail
                PoAuditTrail::create([
                    'id_po' => $po->id_po,
                    'id_karyawan' => Auth::user()->id_karyawan,
                    'pin_karyawan' => $request->pin,
                    'aksi' => 'reject_kepala_gudang',
                    'deskripsi_aksi' => 'Kepala Gudang menolak PO',
                    'data_sebelum' => $dataBefore,
                    'data_sesudah' => $po->toArray(),
                ]);

                DB::commit();
                return response()->json(['message' => 'PO ditolak oleh Kepala Gudang', 'data' => $po], 200);
            }

            // Jika disetujui
            if ($po->tipe_po === 'internal') {
                // PO INTERNAL: Ubah status ke 'selesai' 
                // TAPI tidak langsung transfer stok, menunggu konfirmasi dari apotik
                $po->update(['status' => 'selesai']);

                $deskripsi = 'Kepala Gudang menyetujui PO Internal - Menunggu konfirmasi penerimaan dari Apotik';
            } else {
                // PO EKSTERNAL: Lanjut ke kasir
                $po->update(['status' => 'menunggu_persetujuan_kasir']);
                $deskripsi = 'Kepala Gudang menyetujui PO Eksternal - Menunggu approval Kasir';
            }

            // Audit Trail
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'approve_kepala_gudang',
                'deskripsi_aksi' => $deskripsi,
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->fresh()->toArray(),
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Approval Kepala Gudang berhasil',
                'data' => $po->fresh()
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal approve: ' . $e->getMessage()], 500);
        }
    }

    public function printInvoice($id_po)
    {
        $po = PurchaseOrder::with([
            'items',
            'supplier',
            'karyawanPemohon',
            'karyawanInputInvoice',
            'kepalaGudang',
            'kasir'
        ])->findOrFail($id_po);

        // Pastikan PO sudah ada invoice
        if (!$po->hasInvoice()) {
            abort(404, 'Invoice belum tersedia untuk PO ini');
        }

        return view('po.print-invoice', compact('po'));
    }

    /**
     * TRANSFER STOCK DARI GUDANG KE APOTIK
     * Dipanggil otomatis saat PO Internal disetujui oleh Kepala Gudang
     */
    private function transferStockGudangToApotik(PurchaseOrder $po)
    {
        // Ambil gudang pertama (sesuaikan dengan logic bisnis Anda)
        $gudang = Gudang::first();

        if (!$gudang) {
            throw new \Exception('Gudang tidak ditemukan');
        }

        // Buat transaksi stock apotik
        $stockApotik = StockApotik::create([
            'id' => (string) Str::uuid(),
            'gudang_id' => $gudang->id,
            'kode_transaksi' => 'APO-' . date('YmdHis') . '-' . substr($po->no_po, -3),
            'tanggal_penerimaan' => now(),
            'keterangan' => 'Transfer dari Gudang - PO: ' . $po->no_po,
        ]);

        foreach ($po->items as $item) {
            $produk = DetailSupplier::find($item->id_produk);

            if (!$produk) {
                throw new \Exception("Produk dengan ID {$item->id_produk} tidak ditemukan");
            }

            // Cari stok di gudang berdasarkan barang_id
            $detailGudang = DetailGudang::where('barang_id', $item->id_produk)
                ->where('gudang_id', $gudang->id)
                ->where('stock_gudang', '>=', $item->qty_diminta)
                ->first();

            if (!$detailGudang) {
                throw new \Exception("Stok {$produk->nama} di gudang tidak mencukupi");
            }

            // Kurangi stok gudang
            $detailGudang->decrement('stock_gudang', $item->qty_diminta);

            // Cari atau buat detail stock apotik
            $detailStockApotik = DetailstockApotik::where('obat_id', $item->id_produk)
                ->where('stock_apotik_id', $stockApotik->id)
                ->where('no_batch', $detailGudang->no_batch)
                ->first();

            if ($detailStockApotik) {
                // Update jika sudah ada
                $detailStockApotik->increment('stock_apotik', $item->qty_diminta);
            } else {
                // Buat baru jika belum ada
                DetailstockApotik::create([
                    'id' => (string) Str::uuid(),
                    'stock_apotik_id' => $stockApotik->id,
                    'obat_id' => $item->id_produk,
                    'no_batch' => $detailGudang->no_batch ?? 'BATCH-' . date('Ymd'),
                    'stock_apotik' => $item->qty_diminta,
                    'min_persediaan' => $produk->min_persediaan ?? 0,
                    'tanggal_kadaluarsa' => $detailGudang->tanggal_kadaluarsa,
                ]);
            }

            // Update qty_disetujui dan qty_diterima di PO Item
            $item->update([
                'qty_disetujui' => $item->qty_diminta,
                'qty_diterima' => $item->qty_diminta,
            ]);
        }
    }

    public function approveKasir(Request $request, $id_po)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
            'status_approval' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json(['error' => 'PIN tidak valid'], 403);
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::findOrFail($id_po);
            $dataBefore = $po->toArray();

            $statusPO = $request->status_approval === 'disetujui'
                ? 'disetujui'
                : 'ditolak';

            $po->update([
                'id_kasir_approval' => Auth::user()->id_karyawan,
                'tanggal_approval_kasir' => now(),
                'catatan_kasir' => $request->catatan,
                'status_approval_kasir' => $request->status_approval,
                'status' => $statusPO,
            ]);

            // Audit Trail
            $aksi = $request->status_approval === 'disetujui' ? 'approve_kasir' : 'reject_kasir';
            $deskripsi = $request->status_approval === 'disetujui'
                ? 'Kasir menyetujui PO'
                : 'Kasir menolak PO';

            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => $aksi,
                'deskripsi_aksi' => $deskripsi,
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->toArray(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Approval Kasir berhasil', 'data' => $po], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal approve: ' . $e->getMessage()], 500);
        }
    }

    public function sendToSupplier(Request $request, $id_po)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json(['error' => 'PIN tidak valid'], 403);
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::findOrFail($id_po);

            if ($po->status !== 'disetujui') {
                return response()->json(['error' => 'PO belum disetujui'], 400);
            }

            $dataBefore = $po->toArray();

            $po->update([
                'status' => 'dikirim_ke_supplier',
                'tanggal_dikirim_ke_supplier' => now(),
                'id_karyawan_pengirim' => Auth::user()->id_karyawan,
            ]);

            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'kirim_ke_supplier',
                'deskripsi_aksi' => 'Mengirim PO ke Supplier',
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->toArray(),
            ]);

            DB::commit();
            return response()->json(['message' => 'PO berhasil dikirim ke Supplier', 'data' => $po], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal kirim PO: ' . $e->getMessage()], 500);
        }
    }

    public function print($id_po)
    {
        $po = PurchaseOrder::with(['items', 'karyawanPemohon', 'supplier'])
            ->findOrFail($id_po);

        return view('po.print', compact('po'));
    }

    // Backward compatibility methods
    public function createInternalPO(Request $request)
    {
        $request->merge(['tipe_po' => 'internal']);
        return $this->store($request);
    }

    public function createExternalPO(Request $request)
    {
        $request->merge(['tipe_po' => 'eksternal']);
        return $this->store($request);
    }

    public function submitInternalPO(Request $request, $id_po)
    {
        return $this->submit($request, $id_po);
    }

    public function submitExternalPO(Request $request, $id_po)
    {
        return $this->submit($request, $id_po);
    }

    // ========== API: APPROVE BY KEPALA GUDANG (backward compatibility) ==========
    public function approveByKepalaGudang(Request $request, $id_po)
    {
        return $this->approveKepalaGudang($request, $id_po);
    }

    // ========== API: APPROVE BY KASIR (backward compatibility) ==========
    public function approveByKasir(Request $request, $id_po)
    {
        return $this->approveKasir($request, $id_po);
    }
}
