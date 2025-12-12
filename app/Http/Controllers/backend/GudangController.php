<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailGudang;
use App\Models\Gudang;
use App\Models\HistoryGudang;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class GudangController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar
        $query = Gudang::with(['supplier', 'details']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Pagination
        $gudangs = $query->latest()->paginate(10)->withQueryString();

        // Statistik untuk cards
        $totalGudang = Gudang::count();
        $gudangAktif = Gudang::where('status', 'Aktif')->count();

        // Hitung stok yang menipis (stock_gudang < min_persediaan)
        $stokMenipis = DetailGudang::whereColumn('stock_gudang', '<', 'min_persediaan')->count();

        // Total items di semua gudang
        $totalItems = DetailGudang::sum('stock_gudang');

        // Suppliers untuk filter
        $suppliers = Supplier::orderBy('nama_supplier')->get();

        // Histories untuk modal
        $histories = HistoryGudang::with('barang')
            ->orderByDesc('waktu_proses')
            ->paginate(10);

        return view('gudang.index', compact(
            'gudangs',
            'totalGudang',
            'gudangAktif',
            'stokMenipis',
            'totalItems',
            'suppliers',
            'histories'
        ));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('gudang.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'kode_gudang' => 'required|string|max:50|unique:gudangs,kode_gudang',
            'nama_gudang' => 'required|string|max:100',
            'supplier_id' => 'nullable|uuid|exists:suppliers,id',
            'lokasi' => 'nullable|string|max:200',
            'penanggung_jawab' => 'nullable|string|max:100',
            'status' => 'required|in:Aktif,Nonaktif',
            'keterangan' => 'nullable|string',
            'barang_id' => 'nullable|array',
            'barang_id.*' => 'required|uuid',
            'barang_type' => 'nullable|array',
            'barang_type.*' => 'required|string',
            'no_batch.*' => 'nullable|string|max:50',
            'stock_gudang.*' => 'required|numeric|min:0',
            'min_persediaan.*' => 'required|numeric|min:0',
            'tanggal_masuk.*' => 'nullable|date',
            'lokasi_rak.*' => 'nullable|string|max:50',
            'kondisi.*' => 'required|in:Baik,Rusak,Kadaluarsa',
        ]);

        DB::beginTransaction();

        try {
            $gudang = Gudang::create([
                'id' => Str::uuid(),
                'kode_gudang' => $request->kode_gudang,
                'nama_gudang' => $request->nama_gudang,
                'supplier_id' => $request->supplier_id,
                'lokasi' => $request->lokasi,
                'penanggung_jawab' => $request->penanggung_jawab,
                'keterangan' => $request->keterangan,
                'status' => $request->status,
            ]);

            // Simpan detail barang jika ada
            if ($request->has('barang_id') && is_array($request->barang_id)) {
                foreach ($request->barang_id as $index => $barangId) {
                    DetailGudang::create([
                        'id' => Str::uuid(),
                        'gudang_id' => $gudang->id,
                        'barang_id' => $barangId,
                        'barang_type' => $request->barang_type[$index],
                        'no_batch' => $request->no_batch[$index] ?? null,
                        'stock_gudang' => $request->stock_gudang[$index],
                        'min_persediaan' => $request->min_persediaan[$index],
                        'tanggal_masuk' => $request->tanggal_masuk[$index] ?? null,
                        'lokasi_rak' => $request->lokasi_rak[$index] ?? null,
                        'kondisi' => $request->kondisi[$index],
                    ]);
                }
            }

            DB::commit();
            Alert::success('Berhasil', 'Gudang berhasil ditambahkan!');
            return redirect()->route('gudangs.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function edit(Gudang $gudang)
    {
        $suppliers = Supplier::all();
        $gudang->load('details.barang');
        return view('gudang.edit', compact('gudang', 'suppliers'));
    }

    public function update(Request $request, Gudang $gudang)
    {
        // dd($request->all());
        $request->validate([
            'kode_gudang' => 'required|string|max:50|unique:gudangs,kode_gudang,' . $gudang->id,
            'nama_gudang' => 'required|string|max:100',
            'supplier_id' => 'nullable|uuid|exists:suppliers,id',
            'lokasi' => 'nullable|string|max:200',
            'penanggung_jawab' => 'nullable|string|max:100',
            'status' => 'required|in:Aktif,Nonaktif',
            'keterangan' => 'nullable|string',
            'barang_id' => 'nullable|array',
            'barang_id.*' => 'required|uuid',
            'barang_type' => 'nullable|array',
            'barang_type.*' => 'required|string',
            'no_batch.*' => 'nullable|string|max:50',
            'stock_gudang.*' => 'required|numeric|min:0',
            'min_persediaan.*' => 'required|numeric|min:0',
            'tanggal_masuk.*' => 'nullable|date',
            'lokasi_rak.*' => 'nullable|string|max:50',
            'kondisi.*' => 'required|in:Baik,Rusak,Kadaluarsa',
        ]);

        DB::beginTransaction();

        try {
            $gudang->update([
                'kode_gudang' => $request->kode_gudang,
                'nama_gudang' => $request->nama_gudang,
                'supplier_id' => $request->supplier_id,
                'lokasi' => $request->lokasi,
                'penanggung_jawab' => $request->penanggung_jawab,
                'keterangan' => $request->keterangan,
                'status' => $request->status,
            ]);

            // Hapus detail lama & simpan ulang
            $gudang->details()->delete();

            // Simpan detail barang jika ada
            if ($request->has('barang_id') && is_array($request->barang_id)) {
                foreach ($request->barang_id as $index => $barangId) {
                    DetailGudang::create([
                        'id' => Str::uuid(),
                        'gudang_id' => $gudang->id,
                        'barang_id' => $barangId,
                        'barang_type' => $request->barang_type[$index],
                        'no_batch' => $request->no_batch[$index] ?? null,
                        'stock_gudang' => $request->stock_gudang[$index],
                        'min_persediaan' => $request->min_persediaan[$index],
                        'tanggal_masuk' => $request->tanggal_masuk[$index] ?? null,
                        'lokasi_rak' => $request->lokasi_rak[$index] ?? null,
                        'kondisi' => $request->kondisi[$index],
                    ]);
                }
            }

            DB::commit();
            Alert::info('Berhasil', 'Gudang berhasil diperbarui!');
            return redirect()->route('gudangs.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy(Gudang $gudang)
    {
        try {
            $gudang->delete();
            Alert::success('Berhasil', 'Gudang berhasil dihapus!');
        } catch (\Exception $e) {
            Alert::error('Gagal', 'Tidak dapat menghapus gudang: ' . $e->getMessage());
        }
        return redirect()->route('gudangs.index');
    }

    /**
     * API endpoint untuk mencari barang berdasarkan supplier
     * Mendukung polymorphic relationship (obat, alkes, reagensia)
     */
    public function searchSupplierProducts($supplierId, Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $supplier = Supplier::findOrFail($supplierId);
        $results = [];

        // Cari di tabel obat
        if ($supplier->obats()->exists()) {
            $obats = $supplier->obats()
                ->where(function ($q) use ($query) {
                    $q->where('nama', 'LIKE', "%{$query}%")
                        ->orWhere('jenis', 'LIKE', "%{$query}%");
                })
                ->get()
                ->map(function ($obat) {
                    return [
                        'id' => $obat->id,
                        'nama' => $obat->nama,
                        'jenis' => $obat->jenis ?? '-',
                        'judul' => $obat->kategori ?? 'Obat',
                        'exp_date' => $obat->exp_date,
                        'barang_type' => 'obat',
                    ];
                });
            $results = array_merge($results, $obats->toArray());
        }

        // Cari di tabel alkes
        if ($supplier->alkes()->exists()) {
            $alkes = $supplier->alkes()
                ->where(function ($q) use ($query) {
                    $q->where('nama', 'LIKE', "%{$query}%")
                        ->orWhere('jenis', 'LIKE', "%{$query}%");
                })
                ->get()
                ->map(function ($alat) {
                    return [
                        'id' => $alat->id,
                        'nama' => $alat->nama,
                        'jenis' => $alat->jenis ?? '-',
                        'judul' => $alat->kategori ?? 'Alkes',
                        'exp_date' => $alat->exp_date,
                        'barang_type' => 'alkes',
                    ];
                });
            $results = array_merge($results, $alkes->toArray());
        }

        // Cari di tabel reagensia
        if ($supplier->reagensias()->exists()) {
            $reagensias = $supplier->reagensias()
                ->where(function ($q) use ($query) {
                    $q->where('nama', 'LIKE', "%{$query}%")
                        ->orWhere('jenis', 'LIKE', "%{$query}%");
                })
                ->get()
                ->map(function ($reagensia) {
                    return [
                        'id' => $reagensia->id,
                        'nama' => $reagensia->nama,
                        'jenis' => $reagensia->jenis ?? '-',
                        'judul' => $reagensia->kategori ?? 'Reagensia',
                        'exp_date' => $reagensia->exp_date,
                        'barang_type' => 'reagensia',
                    ];
                });
            $results = array_merge($results, $reagensias->toArray());
        }

        return response()->json($results);
    }

    public function getSupplierDetails($supplierId)
    {
        try {
            // Ambil data barang dari tabel detail_suppliers
            $details = DB::table('detail_suppliers')
                ->where('supplier_id', $supplierId)
                ->select('id', 'judul', 'nama', 'no_batch', 'department_id')
                ->get();

            // Kelompokkan berdasarkan judul
            $grouped = [];
            foreach ($details as $item) {
                $jenis = $item->judul ?? 'Tidak Diketahui';
                $judul = $item->judul ?? 'Barang';
                $grouped[$jenis][$judul][] = [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'jenis' => $jenis,
                    'judul' => $judul,
                    'no_batch' => $item->no_batch,
                ];
            }

            return response()->json($grouped);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Method untuk mendapatkan history gudang
     */
    public function historyGudang(Request $request)
    {
        $query = HistoryGudang::with(['supplier'])
            ->orderBy('waktu_proses', 'desc');

        // Filter berdasarkan supplier jika ada
        if ($request->has('supplier_id') && $request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter berdasarkan status jika ada
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal jika ada
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai) {
            $query->whereDate('waktu_proses', '>=', $request->tanggal_mulai);
        }

        if ($request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $query->whereDate('waktu_proses', '<=', $request->tanggal_akhir);
        }

        $histories = $query->paginate(10);

        return response()->json($histories);
    }


    public function detailsData($id)
    {
        $details = DetailGudang::where('gudang_id', $id)
            ->with('barang')
            ->select('id', 'gudang_id', 'barang_type', 'stock_gudang', 'barang_id');

        return DataTables::of($details)
            ->addIndexColumn()
            ->addColumn('barang_nama', fn($d) => $d->barang->nama ?? '-')
            ->make(true);
    }

    public function getDetailGudangByBarang($barangId, $barangType)
    {
        $details = DetailGudang::where('barang_id', $barangId)
            ->where('barang_type', $barangType)
            ->select('id', 'no_batch', 'stock_gudang', 'lokasi_rak', 'kondisi')
            ->get();

        return response()->json($details);
    }

    public function prosesPenerimaan(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|uuid|exists:suppliers,id',
            'barang_id' => 'required|array',
            'barang_id.*' => 'required|uuid',
            'barang_type' => 'required|array',
            'barang_type.*' => 'required|string|in:obat,alkes,reagensia',
            'no_batch' => 'required|array',
            'no_batch.*' => 'required|string|max:50',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $totalJumlah = 0;
            $barangIds = $request->barang_id;
            $barangTypes = $request->barang_type;
            $noBatches = $request->no_batch;
            $jumlahs = $request->jumlah;

            for ($i = 0; $i < count($barangIds); $i++) {
                $barangId = $barangIds[$i];
                $barangType = $barangTypes[$i];
                $noBatch = $noBatches[$i];
                $jumlah = $jumlahs[$i];
                $totalJumlah += $jumlah;

                $existingDetail = DetailGudang::where('barang_id', $barangId)
                    ->where('barang_type', $barangType)
                    ->where('no_batch', $noBatch)
                    ->first();

                if ($existingDetail) {
                    $existingDetail->stock_gudang += $jumlah;
                    $existingDetail->save();
                } else {
                    $gudang = Gudang::where('supplier_id', $request->supplier_id)->first();

                    if (!$gudang) {
                        throw new \Exception('Gudang tidak ditemukan untuk supplier ini');
                    }

                    DetailGudang::create([
                        'id' => Str::uuid(),
                        'gudang_id' => $gudang->id,
                        'barang_id' => $barangId,
                        'barang_type' => $barangType,
                        'no_batch' => $noBatch,
                        'stock_gudang' => $jumlah,
                        'min_persediaan' => 0,
                        'tanggal_masuk' => now(),
                        'kondisi' => 'Baik',
                    ]);
                }

                // Simpan ke History Gudang (per item)
                HistoryGudang::create([
                    'id' => Str::uuid(),
                    'supplier_id' => $request->supplier_id,
                    'barang_id' => $barangId,
                    'barang_type' => $barangType,
                    'no_batch' => $noBatch,
                    'jumlah' => $jumlah,
                    'waktu_proses' => now(),
                    'status' => 'penerimaan',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penerimaan barang berhasil diproses',
                'data' => [
                    'total_barang' => count($barangIds),
                    'total_jumlah' => $totalJumlah,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses penerimaan barang: ' . $e->getMessage()
            ], 500);
        }
    }
}
