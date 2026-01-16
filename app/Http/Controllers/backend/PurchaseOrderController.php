<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DetailGudang;
use App\Models\DetailobatRs;
use App\Models\DetailstockApotik;
use App\Models\DetailSupplier;
use App\Models\Gudang;
use App\Models\Karyawan;
use App\Models\ObatRs;
use App\Models\PoAuditTrail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ShippingActivity;
use App\Models\StockApotik;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\TagihanPoServices;
use Illuminate\Support\Facades\Storage;

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

        if ($type === 'internal') {
            $gudang = Gudang::first();
            if (!$gudang) {
                return back()->with('error', 'Gudang tidak ditemukan');
            }

            $detailGudangs = DetailGudang::with(['barangObat', 'barangSupplier'])
                ->where('gudang_id', $gudang->id)
                ->where('stock_gudang', '>', 0)
                ->get();

            $produkList = [];

            foreach ($detailGudangs as $detail) {
                $nama = null;
                $merk = null;
                $satuan = 'pcs';
                $hargaBeli = 0;

                // ✅ Tidak perlu set jenis untuk internal, karena tidak divalidasi

                if ($detail->barang_type === 'Obat' && $detail->barangObat) {
                    $nama = $detail->barangObat->nama_obat_rs;
                    $hargaBeli = $detail->barangObat->harga_beli ?? 0;
                } elseif (in_array($detail->barang_type, ['Alkes', 'Reagensia', 'Lainnya']) && $detail->barangSupplier) {
                    $nama = $detail->barangSupplier->nama;
                    $merk = $detail->barangSupplier->merk;
                    $satuan = $detail->barangSupplier->satuan ?? 'pcs';
                    $hargaBeli = $detail->barangSupplier->harga_beli ?? 0;
                } else {
                    if ($detail->barangObat) {
                        $nama = $detail->barangObat->nama_obat_rs;
                        $hargaBeli = $detail->barangObat->harga_beli ?? 0;
                    } elseif ($detail->barangSupplier) {
                        $nama = $detail->barangSupplier->nama;
                        $merk = $detail->barangSupplier->merk;
                        $satuan = $detail->barangSupplier->satuan ?? 'pcs';
                        $hargaBeli = $detail->barangSupplier->harga_beli ?? 0;
                    }
                }

                if (!$nama) continue;

                $produkList[] = [
                    'id' => $detail->barang_id,
                    'detail_gudang_id' => $detail->id,
                    'barang_type' => $detail->barang_type,
                    'nama' => $nama,
                    // ✅ Jenis tidak diperlukan untuk internal
                    'merk' => $merk ?? '',
                    'satuan' => $satuan,
                    'harga_beli' => $hargaBeli,
                    'stock_gudang' => $detail->stock_gudang,
                    'no_batch' => $detail->no_batch ?? '-',
                    'tanggal_kadaluarsa' => $detail->tanggal_kadaluarsa
                        ? \Carbon\Carbon::parse($detail->tanggal_kadaluarsa)->format('d/m/Y')
                        : '-',
                ];
            }
        } else {
            // PO EKSTERNAL: Tetap butuh jenis
            $produkList = DetailSupplier::with(['supplier', 'obats', 'alkes', 'reagensia'])
                ->get()
                ->map(function ($detail) {
                    $nama = null;
                    $productId = null;

                    if ($detail->jenis === 'obat') {
                        $productId = $detail->detail_obat_rs_id;
                        $nama = $detail->obats->nama_obat_rs ?? null;
                    } elseif ($detail->jenis === 'alkes') {
                        $productId = $detail->product_id;
                        $nama = $detail->alkes->nama_alkes ?? $detail->nama;
                    } elseif ($detail->jenis === 'reagensia') {
                        $productId = $detail->product_id;
                        $nama = $detail->reagensia->nama_reagensia ?? $detail->nama;
                    } else {
                        $productId = $detail->product_id;
                        $nama = $detail->nama;
                    }

                    return [
                        'id' => $productId,
                        'detail_supplier_id' => $detail->id,
                        'supplier_id' => $detail->supplier_id,
                        'nama' => $nama,
                        'jenis' => $detail->jenis, // ✅ Penting untuk eksternal
                        'merk' => $detail->merk ?? '',
                        'satuan' => $detail->satuan ?? 'pcs',
                        'harga_beli' => $detail->harga_beli ?? 0,
                        'supplier_name' => $detail->supplier->nama_supplier ?? '-',
                    ];
                })
                ->filter(fn($item) => !empty($item['nama']) && !empty($item['id']));
        }

        return view('po.create', compact('type', 'suppliers', 'produkList'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'tipe_po' => 'required|in:internal,eksternal',
            'id_unit_pemohon' => 'required',
            'unit_pemohon' => 'required|in:apotik,gudang',
            'catatan_pemohon' => 'nullable|string',
            'id_supplier' => 'required_if:tipe_po,eksternal|nullable|uuid',
            'pajak' => 'nullable|numeric',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|uuid',
            'items.*.qty_diminta' => 'required|integer|min:1',
            'items.*.jenis' => 'required_if:tipe_po,eksternal|in:obat,alkes,reagensia,lainnya',
        ]);

        DB::beginTransaction();
        try {
            $items = $request->items;
            $total = 0;

            /** ===============================
             * HITUNG TOTAL (EKSTERNAL SAJA)
             * =============================== */
            if ($request->tipe_po === 'eksternal') {
                foreach ($items as $item) {
                    // ✅ Cari DetailSupplier berdasarkan jenis
                    if ($item['jenis'] === 'obat') {
                        $detailSupplier = DetailSupplier::where('detail_obat_rs_id', $item['id_produk'])->first();
                    } else {
                        $detailSupplier = DetailSupplier::where('product_id', $item['id_produk'])->first();
                    }

                    if (!$detailSupplier) {
                        throw new \Exception('Produk tidak ditemukan di detail supplier');
                    }

                    $total += $detailSupplier->harga_beli * $item['qty_diminta'];
                }
            }

            $pajak = $request->tipe_po === 'eksternal' ? ($request->pajak ?? 0) : 0;
            $grandTotal = $total + $pajak;

            /** ===============================
             * CREATE PO
             * =============================== */
            $po = PurchaseOrder::create([
                'tipe_po' => $request->tipe_po,
                'status' => 'draft',
                'id_unit_pemohon' => $request->id_unit_pemohon,
                'unit_pemohon' => $request->unit_pemohon,
                'id_karyawan_pemohon' => Auth::user()->id_karyawan,
                'tanggal_permintaan' => now(),
                'catatan_pemohon' => $request->catatan_pemohon,
                'unit_tujuan' => $request->tipe_po === 'internal' ? 'gudang' : 'supplier',
                'id_supplier' => $request->tipe_po === 'eksternal' ? $request->id_supplier : null,
                'total_harga' => $total,
                'pajak' => $pajak,
                'grand_total' => $grandTotal,
                'tanggal_jatuh_tempo' => now()->addDays(30),
            ]);

            /** ===============================
             * CREATE PO ITEMS
             * =============================== */
            foreach ($items as $item) {
                if ($request->tipe_po === 'eksternal') {
                    // ✅ Cari DetailSupplier dan load relasi yang tepat
                    if ($item['jenis'] === 'obat') {
                        $detailSupplier = DetailSupplier::where('detail_obat_rs_id', $item['id_produk'])
                            ->with('obats')
                            ->first();
                        $namaProduk = $detailSupplier->obats->nama_obat_rs ?? $detailSupplier->nama;
                    } else {
                        $detailSupplier = DetailSupplier::where('product_id', $item['id_produk'])
                            ->with(['alkes', 'reagensia'])
                            ->first();

                        if ($item['jenis'] === 'alkes') {
                            $namaProduk = $detailSupplier->alkes->nama_alkes ?? $detailSupplier->nama;
                        } elseif ($item['jenis'] === 'reagensia') {
                            $namaProduk = $detailSupplier->reagensia->nama_reagensia ?? $detailSupplier->nama;
                        } else {
                            $namaProduk = $detailSupplier->nama;
                        }
                    }

                    if (!$detailSupplier) {
                        throw new \Exception('Detail supplier tidak ditemukan');
                    }

                    // ✅ Simpan dengan id_produk yang benar (detail_obat_rs_id atau product_id)
                    PurchaseOrderItem::create([
                        'id_po' => $po->id_po,
                        'id_produk' => $item['id_produk'], // ✅ Sudah benar dari frontend
                        'nama_produk' => $namaProduk,
                        'qty_diminta' => $item['qty_diminta'],
                        'harga_satuan' => $detailSupplier->harga_beli,
                        'subtotal' => $detailSupplier->harga_beli * $item['qty_diminta'],
                    ]);

                    // Update stock PO di DetailSupplier
                    $detailSupplier->increment('stock_po', $item['qty_diminta']);
                } else {
                    // INTERNAL
                    $produk = DetailobatRs::find($item['id_produk']);

                    PurchaseOrderItem::create([
                        'id_po' => $po->id_po,
                        'id_produk' => $item['id_produk'],
                        'nama_produk' => $produk->nama_obat_rs,
                        'qty_diminta' => $item['qty_diminta'],
                        'harga_satuan' => 0,
                        'subtotal' => 0,
                    ]);
                }
            }

            /** ===============================
             * AUDIT TRAIL
             * =============================== */
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'buat_po',
                'deskripsi_aksi' => 'Membuat PO ' . ucfirst($request->tipe_po),
                'data_sesudah' => $po->toArray(),
            ]);

            /** ===============================
             * TAGIHAN EKSTERNAL
             * =============================== */
            if ($request->tipe_po === 'eksternal') {
                $tagihanService = new TagihanPoServices();
                $tagihanService->createTagihanFromPO($po);
            }

            DB::commit();

            return redirect()
                ->route('po.show', $po->id_po)
                ->with('success', 'Purchase Order berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Gagal membuat PO: ' . $e->getMessage()
            ])->withInput();
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

        if (!in_array($po->status, ['draft', 'ditolak'])) {
            return redirect()->route('po.show', $id_po)
                ->with('error', 'Hanya PO dengan status draft atau ditolak yang dapat diedit');
        }

        $suppliers = Supplier::where('status', 'Aktif')->get();

        if ($po->tipe_po === 'internal') {
            // ... kode internal sama seperti di method create ...
            $gudang = Gudang::first();
            if (!$gudang) {
                return back()->with('error', 'Gudang tidak ditemukan');
            }

            $detailGudangs = DetailGudang::with(['barangObat', 'barangSupplier'])
                ->where('gudang_id', $gudang->id)
                ->where('stock_gudang', '>', 0)
                ->get();

            $produkList = [];

            foreach ($detailGudangs as $detail) {
                $nama = null;
                $merk = null;
                $satuan = 'pcs';
                $hargaBeli = 0;
                $jenis = null;

                if ($detail->barang_type === 'DetailObatRs' && $detail->barangObat) {
                    $nama = $detail->barangObat->nama_obat_rs;
                    $hargaBeli = $detail->barangObat->harga_beli ?? 0;
                    $jenis = 'Obat';
                } elseif ($detail->barang_type === 'DetailSupplier' && $detail->barangSupplier) {
                    $nama = $detail->barangSupplier->nama;
                    $merk = $detail->barangSupplier->merk;
                    $satuan = $detail->barangSupplier->satuan ?? 'pcs';
                    $hargaBeli = $detail->barangSupplier->harga_beli ?? 0;
                    $jenis = $detail->barangSupplier->jenis;
                }

                if (!$nama) continue;

                $produkList[] = [
                    'id' => $detail->barang_id,
                    'detail_gudang_id' => $detail->id,
                    'barang_type' => $detail->barang_type,
                    'nama' => $nama,
                    'jenis' => $jenis,
                    'merk' => $merk ?? '',
                    'satuan' => $satuan,
                    'harga_beli' => $hargaBeli,
                    'stock_gudang' => $detail->stock_gudang,
                    'no_batch' => $detail->no_batch ?? '-',
                    'tanggal_kadaluarsa' => $detail->tanggal_kadaluarsa
                        ? \Carbon\Carbon::parse($detail->tanggal_kadaluarsa)->format('d/m/Y')
                        : '-',
                ];
            }
        } else {
            // PO EKSTERNAL
            // ✅ Obat: detail_obat_rs_id | Non-Obat: product_id
            $produkList = DetailSupplier::with(['supplier', 'obats', 'alkes', 'reagensia'])
                ->where('supplier_id', $po->id_supplier)
                ->get()
                ->map(function ($detail) {
                    $nama = null;
                    $productId = null;

                    if ($detail->jenis === 'obat') {
                        $productId = $detail->detail_obat_rs_id;
                        $nama = $detail->obats->nama_obat_rs ?? null;
                    } elseif ($detail->jenis === 'alkes') {
                        $productId = $detail->product_id;
                        $nama = $detail->alkes->nama_alkes ?? $detail->nama;
                    } elseif ($detail->jenis === 'reagensia') {
                        $productId = $detail->product_id;
                        $nama = $detail->reagensia->nama_reagensia ?? $detail->nama;
                    } else {
                        $productId = $detail->product_id;
                        $nama = $detail->nama;
                    }

                    return [
                        'id' => $productId,
                        'detail_supplier_id' => $detail->id,
                        'supplier_id' => $detail->supplier_id,
                        'nama' => $nama,
                        'jenis' => $detail->jenis,
                        'merk' => $detail->merk ?? '',
                        'satuan' => $detail->satuan ?? 'pcs',
                        'harga_beli' => $detail->harga_beli ?? 0,
                    ];
                })
                ->filter(fn($item) => !empty($item['nama']) && !empty($item['id']));
        }

        return view('po.edit', compact('po', 'suppliers', 'produkList'));
    }

    public function update(Request $request, $id_po)
    {
        $po = PurchaseOrder::findOrFail($id_po);

        if (!in_array($po->status, ['draft', 'ditolak'])) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Hanya PO draft atau ditolak yang dapat diedit'], 400);
            }
            return back()->with('error', 'Hanya PO dengan status draft atau ditolak yang dapat diedit');
        }

        $validated = $request->validate([
            'catatan_pemohon' => 'nullable|string',
            'pin' => 'required|size:6',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|uuid',
            'items.*.qty_diminta' => 'required|integer|min:1',
            'items.*.jenis' => 'required_if:tipe_po,eksternal|in:obat,alkes,reagensia,lainnya',
        ]);

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
            $statusSebelum = $po->status;

            // Delete old items & reset stock_po
            foreach ($po->items as $oldItem) {
                if ($po->tipe_po === 'eksternal') {
                    // ✅ Cari DetailSupplier yang tepat untuk decrement stock_po
                    $detailSupplier = DetailSupplier::where(function ($q) use ($oldItem) {
                        $q->where('detail_obat_rs_id', $oldItem->id_produk)
                            ->orWhere('product_id', $oldItem->id_produk);
                    })->first();

                    if ($detailSupplier) {
                        $detailSupplier->decrement('stock_po', $oldItem->qty_diminta);
                    }
                }
            }
            $po->items()->delete();

            // Create new items
            $total = 0;
            foreach ($request->items as $item) {
                // ✅ Cari DetailSupplier berdasarkan jenis produk
                if ($item['jenis'] === 'obat') {
                    $detailSupplier = DetailSupplier::where('detail_obat_rs_id', $item['id_produk'])
                        ->with('obats')
                        ->first();
                    $namaProduk = $detailSupplier->obats->nama_obat_rs ?? $detailSupplier->nama;
                } else {
                    $detailSupplier = DetailSupplier::where('product_id', $item['id_produk'])
                        ->with(['alkes', 'reagensia'])
                        ->first();

                    if ($item['jenis'] === 'alkes') {
                        $namaProduk = $detailSupplier->alkes->nama_alkes ?? $detailSupplier->nama;
                    } elseif ($item['jenis'] === 'reagensia') {
                        $namaProduk = $detailSupplier->reagensia->nama_reagensia ?? $detailSupplier->nama;
                    } else {
                        $namaProduk = $detailSupplier->nama;
                    }
                }

                if (!$detailSupplier) {
                    throw new \Exception('Produk tidak ditemukan');
                }

                PurchaseOrderItem::create([
                    'id_po' => $po->id_po,
                    'id_produk' => $item['id_produk'],
                    'nama_produk' => $namaProduk,
                    'qty_diminta' => $item['qty_diminta'],
                    'harga_satuan' => $detailSupplier->harga_beli,
                    'subtotal' => $detailSupplier->harga_beli * $item['qty_diminta'],
                ]);

                $total += $detailSupplier->harga_beli * $item['qty_diminta'];

                // Update stock_po untuk eksternal
                if ($po->tipe_po === 'eksternal') {
                    $detailSupplier->increment('stock_po', $item['qty_diminta']);
                }
            }

            // Update PO
            $updateData = [
                'catatan_pemohon' => $request->catatan_pemohon,
                'total_harga' => $total,
                'grand_total' => $total + $po->pajak,
            ];

            if ($statusSebelum === 'ditolak') {
                $updateData['status'] = 'draft';
            }

            $po->update($updateData);

            $deskripsiAksi = 'Mengupdate PO';
            if ($statusSebelum === 'ditolak') {
                $deskripsiAksi .= ' (status berubah dari ditolak menjadi draft)';
            }

            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'edit_po',
                'deskripsi_aksi' => $deskripsiAksi,
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->fresh()->toArray(),
            ]);

            DB::commit();

            $successMessage = 'Purchase Order berhasil diupdate';
            if ($statusSebelum === 'ditolak') {
                $successMessage .= ' dan status berubah menjadi draft';
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $successMessage,
                    'data' => $po->load('items')
                ], 200);
            }

            return redirect()->route('po.show', $po->id_po)
                ->with('success', $successMessage);
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
                    // ✅ Cari DetailSupplier yang tepat
                    $detailSupplier = DetailSupplier::where(function ($q) use ($item) {
                        $q->where('detail_obat_rs_id', $item->id_produk)
                            ->orWhere('product_id', $item->id_produk);
                    })->first();

                    if ($detailSupplier) {
                        $detailSupplier->decrement('stock_po', $item->qty_diminta);
                    }
                }
            }

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
        DB::beginTransaction();

        try {
            $po = PurchaseOrder::findOrFail($id_po);
            $dataBefore = $po->toArray();

            // Validasi status (opsional tapi sangat disarankan)
            if (!in_array($po->status, ['draft', 'ditolak'])) {
                return response()->json([
                    'error' => 'PO tidak dapat disubmit pada status saat ini'
                ], 403);
            }

            // Status berikutnya
            $newStatus = 'menunggu_persetujuan_kepala_gudang';

            $po->update([
                'status' => $newStatus
            ]);

            // Audit Trail
            PoAuditTrail::create([
                'id_po'        => $po->id_po,
                'id_karyawan'  => Auth::user()->id_karyawan,
                'pin_karyawan' => null, // PIN tidak digunakan
                'aksi'         => 'submit_approval',
                'deskripsi_aksi' => 'Mengirim PO untuk persetujuan kepala gudang',
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->toArray(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'PO berhasil diajukan',
                'data'    => $po
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Gagal submit PO: ' . $e->getMessage()
            ], 500);
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
                $po->update(['status' => 'diterima']);

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

    public function markAsReceived(Request $request, $id_po)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
            'catatan' => 'nullable|string',
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
            $po = PurchaseOrder::with('shippingActivities')->findOrFail($id_po);

            // Validasi status PO
            if (!in_array($po->status, ['dikirim_ke_supplier', 'dalam_pengiriman'])) {
                return response()->json([
                    'error' => 'PO tidak dapat ditandai sebagai diterima. Status saat ini: ' . $po->status
                ], 400);
            }

            $dataBefore = $po->toArray();

            // Update status PO ke 'diterima'
            $po->update([
                'status' => 'diterima',
            ]);

            // Create shipping activity record
            $shippingActivity = ShippingActivity::create([
                'id_po' => $po->id_po,
                'status_shipping' => 'diterima',
                'deskripsi_aktivitas' => 'Barang dari supplier telah diterima di gudang',
                'catatan' => $request->catatan,
                'tanggal_aktivitas' => now(),
                'id_karyawan_input' => Auth::user()->id_karyawan,
            ]);

            // Create audit trail
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'terima_barang',
                'deskripsi_aksi' => 'Menandai barang dari supplier sudah diterima' . 
                                ($request->catatan ? ' - Catatan: ' . $request->catatan : ''),
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->fresh()->toArray(),
            ]);

            DB::commit();

            Log::info('PO Marked as Received', [
                'po_id' => $po->id_po,
                'no_po' => $po->no_po,
                'by_user' => Auth::user()->id_karyawan,
                'shipping_activity_id' => $shippingActivity->id
            ]);

            return response()->json([
                'message' => 'Barang dari supplier berhasil ditandai sebagai diterima. Silakan lakukan konfirmasi penerimaan untuk update stok gudang.',
                'data' => $po->fresh()->load(['shippingActivities', 'items'])
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Mark as Received Error', [
                'po_id' => $id_po,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Gagal menandai barang sebagai diterima: ' . $e->getMessage()
            ], 500);
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

    public function showConfirmation($id_po)
    {
        $po = PurchaseOrder::with([
            'items.produk',
            'karyawanPemohon',
            'kepalaGudang',
        ])->findOrFail($id_po);

        // Route ke method yang sesuai berdasarkan tipe PO
        if ($po->tipe_po === 'internal') {
            return $this->showInternalConfirmation($po);
        } else {
            return $this->showExternalConfirmation($po);
        }
    }

    /**
     * Show confirmation page for INTERNAL PO (Gudang → Apotik)
     * View: confirm-receipt.blade.php
     */
    private function showInternalConfirmation($po)
    {
        // Validasi: Hanya PO Internal yang sudah disetujui Kepala Gudang
        if ($po->status !== 'selesai') {
            return redirect()->route('po.show', $po->id_po)
                ->with('error', 'PO ini belum disetujui atau sudah dikonfirmasi sebelumnya');
        }

        Log::info('Show INTERNAL Confirmation Page:', [
            'po_id' => $po->id_po,
            'no_po' => $po->no_po,
            'status' => $po->status,
            'items_count' => $po->items->count()
        ]);

        return view('po.confirm-receipt', compact('po'));
    }

    /**
     * Show confirmation page for EXTERNAL PO (Supplier → Gudang)
     * View: confirmation.blade.php (atau nama file Anda untuk external)
     */
    private function showExternalConfirmation($po)
    {
        if (!$po->needsReceiptConfirmation()) {
            return redirect()->route('po.show', $po->id_po)
                ->with('error', 'PO ini tidak memerlukan konfirmasi penerimaan saat ini');
        }

        Log::info('Show EXTERNAL Confirmation Page:', [
            'po_id' => $po->id_po,
            'no_po' => $po->no_po,
            'status' => $po->status,
            'items_count' => $po->items->count()
        ]);

        // GANTI dengan nama view yang sesuai untuk external
        return view('po.confirmation', compact('po'));
    }

    /**
     * Process confirmation - ROUTER untuk memilih handler yang tepat
     */
    public function confirmReceipt(Request $request, $id_po)
    {
        $po = PurchaseOrder::findOrFail($id_po);

        Log::info('=== CONFIRM RECEIPT CALLED ===', [
            'po_id' => $id_po,
            'tipe_po' => $po->tipe_po,
            'status' => $po->status
        ]);

        // Route ke handler yang sesuai berdasarkan tipe PO
        if ($po->tipe_po === 'internal') {
            return $this->confirmInternalReceipt($request, $po);
        } else {
            return $this->confirmExternalReceipt($request, $po);
        }
    }

    /**
     * Confirm receipt for INTERNAL PO (Gudang → Apotik)
     * - Kurangi stock gudang
     * - Tambah stock apotik
     */
    private function confirmInternalReceipt(Request $request, $po)
    {
        Log::info('=== START CONFIRM INTERNAL RECEIPT ===', [
            'po_id' => $po->id_po,
            'user_id' => Auth::user()->id_karyawan
        ]);

        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
            'items' => 'required|array|min:1',
            'items.*.id_po_item' => 'required|uuid',
            'items.*.qty_diterima' => 'required|integer|min:0',
            'items.*.kondisi' => 'required|in:baik,rusak,kadaluarsa',
            'items.*.catatan' => 'nullable|string',
            'catatan_penerima' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation Failed:', $validator->errors()->toArray());

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
            Log::warning('Invalid PIN', ['user_id' => Auth::user()->id_karyawan]);

            if ($request->wantsJson()) {
                return response()->json(['error' => 'PIN tidak valid'], 403);
            }
            return back()->withErrors(['pin' => 'PIN tidak valid'])->withInput();
        }

        DB::beginTransaction();
        try {
            // Validate PO status
            if ($po->status !== 'selesai') {
                throw new \Exception('PO ini belum disetujui atau sudah dikonfirmasi. Status: ' . $po->status);
            }

            $dataBefore = $po->toArray();

            // Get gudang
            $gudang = Gudang::first();
            if (!$gudang) {
                throw new \Exception('Gudang tidak ditemukan di sistem');
            }

            $totalDiterima = 0;
            $totalRusak = 0;
            $itemsProcessed = [];

            foreach ($request->items as $itemData) {
                $poItem = PurchaseOrderItem::findOrFail($itemData['id_po_item']);
                $produk = DetailSupplier::find($poItem->id_produk);

                if (!$produk) {
                    throw new \Exception("Produk ID {$poItem->id_produk} tidak ditemukan");
                }

                $qtyDiterima = (int) $itemData['qty_diterima'];
                $kondisi = $itemData['kondisi'];
                $catatan = $itemData['catatan'] ?? null;

                if ($qtyDiterima == 0) {
                    continue;
                }

                // Cari detail gudang
                $detailGudang = DetailGudang::where('barang_id', $poItem->id_produk)
                    ->where('gudang_id', $gudang->id)
                    ->where('stock_gudang', '>', 0)
                    ->orderBy('tanggal_kadaluarsa', 'asc')
                    ->first();

                if (!$detailGudang) {
                    throw new \Exception("Produk {$produk->nama} tidak tersedia di gudang");
                }

                if ($detailGudang->stock_gudang < $qtyDiterima) {
                    throw new \Exception("Stock {$produk->nama} tidak mencukupi");
                }

                // Update PO Item
                $poItem->update([
                    'qty_diterima' => $qtyDiterima,
                    'qty_disetujui' => $qtyDiterima,
                    'kondisi_barang' => $kondisi,
                    'catatan_penerimaan' => $catatan,
                    'batch_number' => $detailGudang->no_batch,
                    'tanggal_kadaluarsa' => $detailGudang->tanggal_kadaluarsa,
                ]);

                // KURANGI STOCK GUDANG
                $detailGudang->decrement('stock_gudang', $qtyDiterima);

                // Proses berdasarkan kondisi
                if ($kondisi === 'baik') {
                    $this->addToStockApotik($gudang, $poItem, $detailGudang, $qtyDiterima, $produk, $po);
                    $totalDiterima += $qtyDiterima;
                } else {
                    $this->addToRetur($gudang, $poItem, $detailGudang, $qtyDiterima, $produk, $po);
                    $totalRusak += $qtyDiterima;
                }

                $itemsProcessed[] = [
                    'product' => $produk->nama,
                    'batch' => $detailGudang->no_batch,
                    'qty' => $qtyDiterima,
                    'kondisi' => $kondisi,
                ];
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
                'deskripsi_aksi' => "Konfirmasi penerimaan internal - Diterima: {$totalDiterima}, Retur: {$totalRusak}",
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->fresh()->toArray(),
            ]);

            DB::commit();

            Log::info('=== INTERNAL RECEIPT CONFIRMED ===', [
                'po_id' => $po->id_po,
                'total_diterima' => $totalDiterima,
                'total_rusak' => $totalRusak
            ]);

            $message = "✓ Konfirmasi penerimaan berhasil! {$totalDiterima} unit masuk ke apotik.";
            if ($totalRusak > 0) {
                $message .= " {$totalRusak} unit ditandai sebagai retur.";
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'data' => $po->fresh()->load('items'),
                    'items_processed' => $itemsProcessed,
                ], 200);
            }

            return redirect()->route('po.show', $po->id_po)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== INTERNAL RECEIPT ERROR ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Gagal: ' . $e->getMessage()], 500);
            }

            return back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()])->withInput();
        }
    }

    private function confirmExternalReceipt(Request $request, $po)
    {
        Log::info('=== START CONFIRM EXTERNAL RECEIPT ===', [
            'po_id' => $po->id_po
        ]);

        // TODO: Implement external confirmation logic
        // Ini untuk PO dari supplier ke gudang

        return back()->with('error', 'Fitur konfirmasi PO External belum diimplementasikan');
    }

    /**
     * Add item to stock apotik
     */
    private function addToStockApotik($gudang, $poItem, $detailGudang, $qty, $produk, $po)
    {
        Log::info('Adding to Stock Apotik', [
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
                'keterangan' => 'Transfer dari Gudang - PO Internal: ' . $po->no_po,
            ]);
        }

        $existingDetail = DetailstockApotik::where('obat_id', $poItem->id_produk)
            ->where('no_batch', $detailGudang->no_batch)
            ->first();

        if ($existingDetail) {
            $existingDetail->increment('stock_apotik', $qty);
            Log::info('Stock Apotik UPDATED', ['new_stock' => $existingDetail->stock_apotik]);
        } else {
            DetailstockApotik::create([
                'id' => (string) Str::uuid(),
                'stock_apotik_id' => $stockApotik->id,
                'obat_id' => $poItem->id_produk,
                'no_batch' => $detailGudang->no_batch,
                'stock_apotik' => $qty,
                'min_persediaan' => $produk->min_persediaan ?? 0,
                'tanggal_kadaluarsa' => $detailGudang->tanggal_kadaluarsa,
            ]);
            Log::info('Stock Apotik CREATED', ['stock' => $qty]);
        }
    }

    /**
     * Add item to retur
     */
    private function addToRetur($gudang, $poItem, $detailGudang, $qty, $produk, $po)
    {
        Log::info('Adding to Retur', [
            'produk' => $produk->nama,
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
                'keterangan' => 'Transfer dari Gudang - PO Internal: ' . $po->no_po,
            ]);
        }

        $existingRetur = DetailstockApotik::where('obat_id', $poItem->id_produk)
            ->where('stock_apotik_id', $stockApotik->id)
            ->where('no_batch', $detailGudang->no_batch)
            ->first();

        if ($existingRetur) {
            $existingRetur->increment('retur', $qty);
        } else {
            DetailstockApotik::create([
                'id' => (string) Str::uuid(),
                'stock_apotik_id' => $stockApotik->id,
                'obat_id' => $poItem->id_produk,
                'no_batch' => $detailGudang->no_batch,
                'stock_apotik' => 0,
                'retur' => $qty,
                'min_persediaan' => $produk->min_persediaan ?? 0,
                'tanggal_kadaluarsa' => $detailGudang->tanggal_kadaluarsa,
            ]);
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

    // Tambahkan method ini di PurchaseOrderController.php

       public function uploadInvoiceProof(Request $request, $id_po)
        {
            try {
                // Log request untuk debugging
                Log::info('Upload Invoice Proof Request', [
                    'po_id' => $id_po,
                    'user_id' => Auth::id(),
                    'has_file' => $request->hasFile('bukti_invoice'),
                    'file_size' => $request->file('bukti_invoice') ? $request->file('bukti_invoice')->getSize() : null,
                ]);

                // Validasi input dengan pesan error yang lebih jelas
                $validator = Validator::make($request->all(), [
                    'bukti_invoice' => [
                        'required',
                        'file',
                        'mimes:jpeg,jpg,png,pdf',
                        'max:5120', // 5MB in kilobytes
                    ],
                    'pin' => [
                        'required',
                        'string',
                        'size:6',
                        'regex:/^[0-9]{6}$/'
                    ]
                ], [
                    'bukti_invoice.required' => 'File bukti invoice harus diupload',
                    'bukti_invoice.file' => 'File harus berupa file yang valid',
                    'bukti_invoice.mimes' => 'Format file harus JPEG, JPG, PNG, atau PDF',
                    'bukti_invoice.max' => 'Ukuran file maksimal 5MB',
                    'pin.required' => 'PIN harus diisi',
                    'pin.size' => 'PIN harus 6 digit',
                    'pin.regex' => 'PIN harus berupa 6 digit angka',
                ]);

                if ($validator->fails()) {
                    Log::warning('Validation failed for invoice upload', [
                        'errors' => $validator->errors()->toArray(),
                        'po_id' => $id_po
                    ]);
                    
                    return response()->json([
                        'error' => $validator->errors()->first(),
                        'errors' => $validator->errors()
                    ], 422);
                }

                // Verifikasi PIN
                $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
                    ->where('pin', $request->pin)
                    ->first();

                if (!$karyawan) {
                    Log::warning('Invalid PIN for invoice upload', [
                        'user_id' => Auth::user()->id_karyawan,
                        'po_id' => $id_po
                    ]);
                    
                    return response()->json([
                        'error' => 'PIN yang Anda masukkan tidak valid'
                    ], 403);
                }

                // Cari PO
                $po = PurchaseOrder::find($id_po);
                
                if (!$po) {
                    Log::error('PO not found', ['po_id' => $id_po]);
                    return response()->json([
                        'error' => 'Purchase Order tidak ditemukan'
                    ], 404);
                }

                // Validasi: Hanya PO yang sudah ada invoice yang bisa upload bukti
                if (!$po->hasInvoice()) {
                    return response()->json([
                        'error' => 'Invoice belum diinput. Silakan input invoice terlebih dahulu.'
                    ], 400);
                }

                DB::beginTransaction();
                try {
                    $dataBefore = $po->toArray();

                    // Hapus file lama jika ada
                    if ($po->bukti_invoice && Storage::disk('public')->exists($po->bukti_invoice)) {
                        Storage::disk('public')->delete($po->bukti_invoice);
                        Log::info('Old invoice proof deleted', ['old_file' => $po->bukti_invoice]);
                    }

                    // Upload file baru dengan error handling
                    $file = $request->file('bukti_invoice');
                    
                    // Sanitize filename
                    $invoiceNo = preg_replace('/[^A-Za-z0-9\-_]/', '_', $po->no_invoice);
                    $extension = $file->getClientOriginalExtension();
                    $fileName = 'invoice_' . $invoiceNo . '_' . time() . '.' . $extension;
                    
                    // Pastikan direktori exists
                    Storage::disk('public')->makeDirectory('invoices');
                    
                    // Store file
                    $filePath = $file->storeAs('invoices', $fileName, 'public');

                    if (!$filePath) {
                        throw new \Exception('Gagal menyimpan file ke storage');
                    }

                    // Verify file exists after upload
                    if (!Storage::disk('public')->exists($filePath)) {
                        throw new \Exception('File gagal tersimpan di storage');
                    }

                    // Update PO
                    $po->update([
                        'bukti_invoice' => $filePath,
                        'tanggal_upload_bukti' => now(),
                        'id_karyawan_upload_bukti' => Auth::user()->id_karyawan
                    ]);

                    // Audit Trail
                    PoAuditTrail::create([
                        'id_po' => $po->id_po,
                        'id_karyawan' => Auth::user()->id_karyawan,
                        'pin_karyawan' => $request->pin,
                        'aksi' => 'upload_bukti_invoice',
                        'deskripsi_aksi' => 'Upload bukti invoice: ' . $fileName,
                        'data_sebelum' => json_encode($dataBefore),
                        'data_sesudah' => json_encode($po->fresh()->toArray()),
                    ]);

                    DB::commit();

                    Log::info('Invoice Proof Uploaded Successfully', [
                        'po_id' => $po->id_po,
                        'no_invoice' => $po->no_invoice,
                        'file_path' => $filePath,
                        'uploaded_by' => Auth::user()->id_karyawan
                    ]);

                    return response()->json([
                        'message' => 'Bukti invoice berhasil diupload',
                        'data' => [
                            'file_path' => $filePath,
                            'file_url' => asset('storage/' . $filePath),
                            'file_name' => $fileName
                        ]
                    ], 200);

                } catch (\Exception $e) {
                    DB::rollBack();
                    
                    // Hapus file yang sudah terupload jika ada error
                    if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                    
                    Log::error('Transaction failed during invoice upload', [
                        'po_id' => $id_po,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    throw $e;
                }

            } catch (\Exception $e) {
                Log::error('Upload Invoice Proof Error', [
                    'po_id' => $id_po,
                    'user_id' => Auth::user()->id_karyawan ?? null,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'error' => 'Gagal upload bukti invoice: ' . $e->getMessage()
                ], 500);
            }
        }

    public function deleteInvoiceProof(Request $request, $id_po)
    {
        try {
            // Validasi PIN
            $validator = Validator::make($request->all(), [
                'pin' => 'required|digits:6'
            ], [
                'pin.required' => 'PIN harus diisi',
                'pin.digits' => 'PIN harus 6 digit angka'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verifikasi PIN
            $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
                ->where('pin', $request->pin)
                ->first();

            if (!$karyawan) {
                Log::warning('Invalid PIN for delete invoice proof', [
                    'user_id' => Auth::user()->id_karyawan,
                    'po_id' => $id_po
                ]);
                
                return response()->json(['error' => 'PIN tidak valid'], 403);
            }

            DB::beginTransaction();
            try {
                $po = PurchaseOrder::findOrFail($id_po);

                if (!$po->bukti_invoice) {
                    return response()->json([
                        'error' => 'Tidak ada bukti invoice yang dapat dihapus'
                    ], 400);
                }

                $dataBefore = $po->toArray();
                $oldFilePath = $po->bukti_invoice;

                // Hapus file dari storage
                if (Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                    Log::info('Invoice proof file deleted', ['file' => $oldFilePath]);
                }

                // Update PO
                $po->update([
                    'bukti_invoice' => null,
                    'tanggal_upload_bukti' => null,
                    'id_karyawan_upload_bukti' => null
                ]);

                // Audit Trail
                PoAuditTrail::create([
                    'id_po' => $po->id_po,
                    'id_karyawan' => Auth::user()->id_karyawan,
                    'pin_karyawan' => $request->pin,
                    'aksi' => 'hapus_bukti_invoice',
                    'deskripsi_aksi' => 'Menghapus bukti invoice: ' . basename($oldFilePath),
                    'data_sebelum' => json_encode($dataBefore),
                    'data_sesudah' => json_encode($po->fresh()->toArray()),
                ]);

                DB::commit();

                Log::info('Invoice Proof Deleted Successfully', [
                    'po_id' => $po->id_po,
                    'file_deleted' => $oldFilePath,
                    'deleted_by' => Auth::user()->id_karyawan
                ]);

                return response()->json([
                    'message' => 'Bukti invoice berhasil dihapus'
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('PO not found for delete invoice proof', [
                'po_id' => $id_po,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Purchase Order tidak ditemukan'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Delete Invoice Proof Error', [
                'po_id' => $id_po,
                'user_id' => Auth::user()->id_karyawan ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Gagal menghapus bukti invoice. Silakan coba lagi.'
            ], 500);
        }
    }
}
