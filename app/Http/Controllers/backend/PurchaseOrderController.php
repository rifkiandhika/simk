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

        if (!in_array($po->status, ['draft', 'ditolak', 'selesai'])) {
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
                // ✅ PO INTERNAL: Generate no_gr dan set status ke 'dikirim'
                $noGR = PurchaseOrder::generateNoGR();

                // ✅ Status = 'dikirim' (bukan 'diterima')
                $po->update([
                    'status' => 'dikirim',
                    'no_gr' => $noGR,
                ]);

                $deskripsi = "Kepala Gudang menyetujui PO Internal dengan nomor GR: {$noGR} - Barang siap dikirim dari Gudang ke Apotik";

                Log::info('PO Internal Approved with GR', [
                    'po_id' => $po->id_po,
                    'no_po' => $po->no_po,
                    'no_gr' => $noGR,
                    'status' => 'dikirim'
                ]);
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

            $message = $po->tipe_po === 'internal' 
                ? "Approval Gudang berhasil. Nomor GR: {$po->no_gr}. Barang siap dikirim ke Apotik."
                : 'Approval Gudang berhasil';

            return response()->json([
                'message' => $message,
                'data' => $po->fresh()
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Approve Kepala Gudang Error', [
                'po_id' => $id_po,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

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

        
        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            return response()->json(['error' => 'PIN tidak valid'], 403);
        }

        DB::beginTransaction();
        try {
            $po = PurchaseOrder::with('shippingActivities')->findOrFail($id_po);

            
            if (!in_array($po->status, ['disetujui'])) {
                return response()->json([
                    'error' => 'PO tidak dapat ditandai sebagai diterima. Status saat ini: ' . $po->status
                ], 400);
            }

            $dataBefore = $po->toArray();

            
            $noGR = PurchaseOrder::generateNoGR();

            
            $po->update([
                'status' => 'diterima',
                'no_gr' => $noGR,  
            ]);

            
            $shippingActivity = ShippingActivity::create([
                'id_po' => $po->id_po,
                'status_shipping' => 'diterima',
                'deskripsi_aktivitas' => 'Barang dari supplier telah diterima di gudang - GR: ' . $noGR,
                'catatan' => $request->catatan,
                'tanggal_aktivitas' => now(),
                'id_karyawan_input' => Auth::user()->id_karyawan,
            ]);

            
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => Auth::user()->id_karyawan,
                'pin_karyawan' => $request->pin,
                'aksi' => 'terima_barang',
                'deskripsi_aksi' => 'Menandai barang dari supplier sudah diterima dengan nomor GR: ' . $noGR . 
                                ($request->catatan ? ' - Catatan: ' . $request->catatan : ''),
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->fresh()->toArray(),
            ]);

            DB::commit();

            Log::info('PO Marked as Received', [
                'po_id' => $po->id_po,
                'no_po' => $po->no_po,
                'no_gr' => $noGR, 
                'by_user' => Auth::user()->id_karyawan,
                'shipping_activity_id' => $shippingActivity->id
            ]);

            return response()->json([
                'message' => 'Barang dari supplier berhasil ditandai sebagai diterima dengan nomor GR: ' . $noGR . '. Silakan lakukan konfirmasi penerimaan untuk update stok gudang.',
                'data' => [
                    'po' => $po->fresh()->load(['shippingActivities', 'items']),
                    'no_gr' => $noGR  
                ]
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

    /**
     * Add item to stock apotik
     */
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

       public function uploadProof(Request $request, $id_po)
        {
            try {
                Log::info('Upload Proof Request', [
                    'po_id' => $id_po,
                    'user_id' => Auth::id(),
                    'has_invoice' => $request->hasFile('bukti_invoice'),
                    'has_barang' => $request->hasFile('bukti_barang'),
                ]);

                // Validasi - minimal satu file harus ada
                $validator = Validator::make($request->all(), [
                    'bukti_invoice' => [
                        'nullable',
                        'file',
                        'mimes:jpeg,jpg,png,pdf',
                        'max:5120',
                    ],
                    'bukti_barang' => [
                        'nullable',
                        'file',
                        'mimes:jpeg,jpg,png,pdf',
                        'max:5120',
                    ],
                    'pin' => [
                        'required',
                        'string',
                        'size:6',
                        'regex:/^[0-9]{6}$/'
                    ]
                ], [
                    'bukti_invoice.file' => 'Bukti invoice harus berupa file yang valid',
                    'bukti_invoice.mimes' => 'Format bukti invoice harus JPEG, JPG, PNG, atau PDF',
                    'bukti_invoice.max' => 'Ukuran bukti invoice maksimal 5MB',
                    'bukti_barang.file' => 'Bukti barang harus berupa file yang valid',
                    'bukti_barang.mimes' => 'Format bukti barang harus JPEG, JPG, PNG, atau PDF',
                    'bukti_barang.max' => 'Ukuran bukti barang maksimal 5MB',
                    'pin.required' => 'PIN harus diisi',
                    'pin.size' => 'PIN harus 6 digit',
                    'pin.regex' => 'PIN harus berupa 6 digit angka',
                ]);

                if ($validator->fails()) {
                    Log::warning('Validation failed for proof upload', [
                        'errors' => $validator->errors()->toArray(),
                        'po_id' => $id_po
                    ]);
                    
                    return response()->json([
                        'error' => $validator->errors()->first(),
                        'errors' => $validator->errors()
                    ], 422);
                }

                // Minimal satu file harus ada
                if (!$request->hasFile('bukti_invoice') && !$request->hasFile('bukti_barang')) {
                    return response()->json([
                        'error' => 'Minimal satu file (Invoice atau Barang) harus diupload'
                    ], 422);
                }

                // Verifikasi PIN
                $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
                    ->where('pin', $request->pin)
                    ->first();

                if (!$karyawan) {
                    Log::warning('Invalid PIN for proof upload', [
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
                    $uploadedFiles = [];
                    $deskripsiAksi = [];

                    // Upload Bukti Invoice
                    if ($request->hasFile('bukti_invoice')) {
                        // Hapus file lama jika ada
                        if ($po->bukti_invoice && Storage::disk('public')->exists($po->bukti_invoice)) {
                            Storage::disk('public')->delete($po->bukti_invoice);
                            Log::info('Old invoice proof deleted', ['old_file' => $po->bukti_invoice]);
                        }

                        $file = $request->file('bukti_invoice');
                        $invoiceNo = preg_replace('/[^A-Za-z0-9\-_]/', '_', $po->no_invoice);
                        $extension = $file->getClientOriginalExtension();
                        $fileName = 'invoice_' . $invoiceNo . '_' . time() . '.' . $extension;
                        
                        Storage::disk('public')->makeDirectory('invoices');
                        $filePath = $file->storeAs('invoices', $fileName, 'public');

                        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
                            throw new \Exception('Gagal menyimpan bukti invoice');
                        }

                        $po->bukti_invoice = $filePath;
                        $po->tanggal_upload_bukti = now();
                        $po->id_karyawan_upload_bukti = Auth::user()->id_karyawan;
                        
                        $uploadedFiles[] = 'invoice: ' . $fileName;
                        $deskripsiAksi[] = 'Upload bukti invoice: ' . $fileName;
                    }

                    // Upload Bukti Barang
                    if ($request->hasFile('bukti_barang')) {
                        // Hapus file lama jika ada
                        if ($po->bukti_barang && Storage::disk('public')->exists($po->bukti_barang)) {
                            Storage::disk('public')->delete($po->bukti_barang);
                            Log::info('Old barang proof deleted', ['old_file' => $po->bukti_barang]);
                        }

                        $file = $request->file('bukti_barang');
                        $invoiceNo = preg_replace('/[^A-Za-z0-9\-_]/', '_', $po->no_invoice);
                        $extension = $file->getClientOriginalExtension();
                        $fileName = 'barang_' . $invoiceNo . '_' . time() . '.' . $extension;
                        
                        Storage::disk('public')->makeDirectory('barang');
                        $filePath = $file->storeAs('barang', $fileName, 'public');

                        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
                            throw new \Exception('Gagal menyimpan bukti barang');
                        }

                        $po->bukti_barang = $filePath;
                        $po->tanggal_upload_bukti_barang = now();
                        $po->id_karyawan_upload_bukti_barang = Auth::user()->id_karyawan;
                        
                        $uploadedFiles[] = 'barang: ' . $fileName;
                        $deskripsiAksi[] = 'Upload bukti barang: ' . $fileName;
                    }

                    // Save PO
                    $po->save();

                    // Audit Trail
                    PoAuditTrail::create([
                        'id_po' => $po->id_po,
                        'id_karyawan' => Auth::user()->id_karyawan,
                        'pin_karyawan' => $request->pin,
                        'aksi' => 'upload_bukti',
                        'deskripsi_aksi' => implode('; ', $deskripsiAksi),
                        'data_sebelum' => json_encode($dataBefore),
                        'data_sesudah' => json_encode($po->fresh()->toArray()),
                    ]);

                    DB::commit();

                    Log::info('Proof Uploaded Successfully', [
                        'po_id' => $po->id_po,
                        'files' => $uploadedFiles,
                        'uploaded_by' => Auth::user()->id_karyawan
                    ]);

                    $message = count($uploadedFiles) > 1 
                        ? 'Bukti invoice dan barang berhasil diupload'
                        : 'Bukti ' . (isset($uploadedFiles[0]) && strpos($uploadedFiles[0], 'invoice') !== false ? 'invoice' : 'barang') . ' berhasil diupload';

                    return response()->json([
                        'message' => $message,
                        'data' => [
                            'uploaded_files' => $uploadedFiles
                        ]
                    ], 200);

                } catch (\Exception $e) {
                    DB::rollBack();
                    
                    // Cleanup uploaded files on error
                    if (isset($po->bukti_invoice) && Storage::disk('public')->exists($po->bukti_invoice)) {
                        Storage::disk('public')->delete($po->bukti_invoice);
                    }
                    if (isset($po->bukti_barang) && Storage::disk('public')->exists($po->bukti_barang)) {
                        Storage::disk('public')->delete($po->bukti_barang);
                    }
                    
                    Log::error('Transaction failed during proof upload', [
                        'po_id' => $id_po,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    throw $e;
                }

            } catch (\Exception $e) {
                Log::error('Upload Proof Error', [
                    'po_id' => $id_po,
                    'user_id' => Auth::user()->id_karyawan ?? null,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                return response()->json([
                    'error' => 'Gagal upload bukti: ' . $e->getMessage()
                ], 500);
            }
        }

        public function deleteProof(Request $request, $id_po)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'pin' => 'required|string|size:6|regex:/^[0-9]{6}$/',
                    'type' => 'required|in:invoice,barang'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'error' => $validator->errors()->first()
                    ], 422);
                }

                // Verify PIN
                $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
                    ->where('pin', $request->pin)
                    ->first();

                if (!$karyawan) {
                    return response()->json([
                        'error' => 'PIN tidak valid'
                    ], 403);
                }

                $po = PurchaseOrder::find($id_po);
                if (!$po) {
                    return response()->json([
                        'error' => 'Purchase Order tidak ditemukan'
                    ], 404);
                }

                DB::beginTransaction();
                try {
                    $dataBefore = $po->toArray();
                    $type = $request->type;
                    
                    if ($type === 'invoice') {
                        if (!$po->bukti_invoice) {
                            return response()->json([
                                'error' => 'Bukti invoice tidak ditemukan'
                            ], 404);
                        }

                        if (Storage::disk('public')->exists($po->bukti_invoice)) {
                            Storage::disk('public')->delete($po->bukti_invoice);
                        }

                        $po->bukti_invoice = null;
                        $po->tanggal_upload_bukti = null;
                        $po->id_karyawan_upload_bukti = null;
                        $message = 'Bukti invoice berhasil dihapus';
                        $aksi = 'delete_bukti';
                        
                    } else {
                        if (!$po->bukti_barang) {
                            return response()->json([
                                'error' => 'Bukti barang tidak ditemukan'
                            ], 404);
                        }

                        if (Storage::disk('public')->exists($po->bukti_barang)) {
                            Storage::disk('public')->delete($po->bukti_barang);
                        }

                        $po->bukti_barang = null;
                        $po->tanggal_upload_bukti_barang = null;
                        $po->id_karyawan_upload_bukti_barang = null;
                        $message = 'Bukti barang berhasil dihapus';
                        $aksi = 'delete_bukti_barang';
                    }

                    $po->save();

                    PoAuditTrail::create([
                        'id_po' => $po->id_po,
                        'id_karyawan' => Auth::user()->id_karyawan,
                        'pin_karyawan' => $request->pin,
                        'aksi' => $aksi,
                        'deskripsi_aksi' => $message,
                        'data_sebelum' => json_encode($dataBefore),
                        'data_sesudah' => json_encode($po->fresh()->toArray()),
                    ]);

                    DB::commit();

                    return response()->json([
                        'message' => $message
                    ], 200);

                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }

            } catch (\Exception $e) {
                Log::error('Delete Proof Error', [
                    'po_id' => $id_po,
                    'type' => $request->type ?? null,
                    'error' => $e->getMessage()
                ]);

                return response()->json([
                    'error' => 'Gagal menghapus bukti: ' . $e->getMessage()
                ], 500);
            }
        }

    
    public function getCompleted(Request $request)
    {
        try {
            $query = PurchaseOrder::with(['supplier', 'items'])
                ->whereIn('status', ['completed', 'diterima', 'selesai'])
                ->orderBy('tanggal_permintaan', 'desc');

            // Optional: Filter by supplier
            if ($request->has('supplier_id')) {
                $query->where('id_supplier', $request->supplier_id);
            }

            // Optional: Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('no_po', 'like', "%{$search}%")
                      ->orWhere('kode_po', 'like', "%{$search}%");
                });
            }

            // Limit untuk performance
            $purchaseOrders = $query->limit(100)->get();

            return response()->json([
                'success' => true,
                'data' => $purchaseOrders->map(function($po) {
                    return [
                        'id' => $po->id,
                        'id_po' => $po->id_po ?? $po->id,
                        'no_po' => $po->no_po,
                        'kode_po' => $po->kode_po ?? $po->no_po,
                        'tanggal_permintaan' => $po->tanggal_permintaan,
                        'tanggal_pengiriman' => $po->tanggal_pengiriman ?? null,
                        'status' => $po->status,
                        'supplier' => [
                            'id' => $po->supplier->id ?? null,
                            'nama' => $po->supplier->nama ?? 'N/A',
                        ],
                        'total_items' => $po->items->count(),
                    ];
                }),
                'message' => 'Data berhasil diambil'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting completed POs: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get PO items dengan batches
     * Endpoint: GET /api/purchase-orders/{id}/items
     */
    public function getItems($id)
    {
        try {
            $po = PurchaseOrder::with([
                'items.produk',
                'items.batches'
            ])->findOrFail($id);

            $items = $po->purchaseOrderItems->map(function($item) {
                return [
                    'id_po_item' => $item->id_po_item ?? $item->id,
                    'id_produk' => $item->id_produk,
                    'nama_produk' => $item->nama_produk,
                    'qty_dipesan' => $item->qty_dipesan ?? $item->qty,
                    'qty_diterima' => $item->qty_diterima ?? $item->qty,
                    'harga_satuan' => $item->harga_satuan ?? 0,
                    'satuan' => $item->satuan ?? 'pcs',
                    'batches' => $item->batches ? $item->batches->map(function($batch) {
                        return [
                            'batch_number' => $batch->batch_number,
                            'tanggal_kadaluarsa' => $batch->tanggal_kadaluarsa,
                            'qty_diterima' => $batch->qty_diterima ?? $batch->qty,
                            'kondisi' => $batch->kondisi ?? 'baik',
                        ];
                    }) : [],
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $items,
                'message' => 'Items berhasil diambil'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting PO items: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil items: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
