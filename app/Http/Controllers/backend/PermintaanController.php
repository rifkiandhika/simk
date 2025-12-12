<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DetailGudang;
use App\Models\Gudang;
use App\Models\Permintaan;
use App\Models\Supplier;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PermintaanController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::paginate(10);
        $permintaan = Permintaan::paginate(10);
        return view('permintaan.index', compact('permintaan', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::paginate(10);
        $departments = Department::paginate(10);
        return view('permintaan.create', compact('suppliers', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'supplier_id' => 'required',
            'no_requisition' => 'required|string',
            'tanggal'        => 'required|date',
            'department'     => 'required|string',
            'barang_id.*'    => 'required|uuid',
            'pembuat'        => 'required|string',
            'no_batch.*'     => 'required|string',
            'nama.*'         => 'required|string',
            'jenis.*'        => 'required|string',
            'exp_date.*'     => 'required|date',
            'stock_gudang.*'        => 'required|numeric',
            'jumlah_permintaan.*' => 'required|numeric',
        ]);

        $permintaan = Permintaan::create($request->only([
            'supplier_id',
            'no_requisition',
            'tanggal',
            'department',
            'pembuat'
        ]));

        foreach ($request->no_batch as $i => $batch) {
            $permintaan->detailPermintaan()->create([
                'barang_id'          => $request->barang_id[$i],
                'no_batch'           => $batch,
                'nama'               => $request->nama[$i],
                'jenis'              => $request->jenis[$i],
                'exp_date'           => $request->exp_date[$i],
                'stock_gudang'              => $request->stock_gudang[$i],
                'jumlah_permintaan'  => $request->jumlah_permintaan[$i],
            ]);
        }

        Alert::success('Berhasil', 'Permintaan berhasil dibuat!');
        return redirect()->route('permintaans.index');
    }

    public function getSupplierGudangDetails($supplierId)
    {
        // Ambil semua gudang milik supplier ini
        $gudangIds = Gudang::where('supplier_id', $supplierId)->pluck('id');

        // Ambil semua detail barang dari gudang-gudang tersebut
        $details = DetailGudang::whereIn('gudang_id', $gudangIds)
            ->with(['barang:id,nama,jenis,judul,exp_date']) // pastikan relasi ke model Barang ada
            ->get();

        // Kelompokkan berdasarkan jenis → judul (sesuai struktur yang dipakai modal)
        $grouped = [];

        foreach ($details as $detail) {
            $barang = $detail->barang;
            $jenis = $barang->jenis ?? 'Lainnya';
            $judul = $barang->judul ?? 'Tanpa Judul';

            $grouped[$jenis][$judul][] = [
                'id' => $barang->id,
                'no_batch' => $detail->no_batch,
                'nama' => $barang->nama ?? '-',
                'judul' => $barang->judul ?? '-',
                'jenis' => $barang->jenis ?? '-',
                'exp_date' => $barang->exp_date ?? '',
                'stock_gudang' => $detail->stock_gudang ?? 0,
            ];
        }

        return response()->json($grouped);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $permintaans = Permintaan::with('detailPermintaan')->findOrFail($id);
        $suppliers = Supplier::paginate(10);
        $departments = Department::paginate(10);
        return view('permintaan.edit', compact('permintaans', 'suppliers', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required',
            'no_requisition' => 'required|string',
            'tanggal'        => 'required|date',
            'department'     => 'required|array',
            'barang_id.*'    => 'required|uuid',
            'pembuat'        => 'required|string',
            'no_batch.*'     => 'required|string',
            'nama.*'         => 'required|string',
            'jenis.*'        => 'required|string',
            'exp_date.*'     => 'required|date',
            'stock_gudang.*'        => 'required|numeric',
            'jumlah_permintaan.*' => 'required|numeric',
        ]);

        $permintaan = Permintaan::findOrFail($id);
        $permintaan->update($request->only([
            'supplier_id',
            'no_requisition',
            'tanggal',
            'department',
            'pembuat'
        ]));

        // hapus semua detail lama, lalu insert ulang
        $permintaan->detailPermintaan()->delete();

        foreach ($request->no_batch as $i => $batch) {
            $permintaan->detailPermintaan()->create([
                'barang_id'          => $request->barang_id[$i],
                'no_batch'           => $batch,
                'nama'               => $request->nama[$i],
                'jenis'              => $request->jenis[$i],
                'exp_date'           => $request->exp_date[$i],
                'stock_gudang'              => $request->stock_gudang[$i],
                'jumlah_permintaan'  => $request->jumlah_permintaan[$i],
            ]);
        }

        Alert::info('Berhasil', 'Permintaan berhasil diupdate!');
        return redirect()->route('permintaans.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $permintaan = Permintaan::findOrFail($id);
        $permintaan->detailPermintaan()->delete();
        $permintaan->delete();

        Alert::success('Berhasil', 'Permintaan berhasil dihapus!');
        return redirect()->route('permintaans.index');
    }

    public function send(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $permintaan = Permintaan::findOrFail($id);

        $permintaan->update([
            'supplier_id' => $request->supplier_id,
            'status'      => 'Dikirim',
        ]);

        Alert::success('Berhasil', 'Permintaan berhasil dikirim!');
        return redirect()->route('permintaans.index');
    }
}
