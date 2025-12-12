<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\ObatMaster;
use Illuminate\Http\Request;

class ObatMasterController extends Controller
{
    public function index()
    {
        $obats = ObatMaster::orderBy('nama_obat', 'asc')->get();
        return view('obat_master.index', compact('obats'));
    }

    /**
     * Tampilkan form tambah obat.
     */
    public function create()
    {
        return view('obat_master.create');
    }

    /**
     * Simpan data obat baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kfa_code' => 'required|string|max:50|unique:obat_masters,kfa_code',
            'nama_obat' => 'required|string|max:200',
            'nama_generik' => 'nullable|string|max:200',
            'bentuk_sediaan' => 'nullable|string|max:100',
            'kekuatan' => 'nullable|string|max:50',
            'satuan_kekuatan' => 'nullable|string|max:20',
            'kemasan' => 'nullable|string|max:50',
            'isi_kemasan' => 'nullable|string|max:50',
            'manufacturer' => 'nullable|string|max:200',
            'nie' => 'nullable|string|max:100',
            'komposisi' => 'nullable|string',
            'indikasi' => 'nullable|string',
            'kontraindikasi' => 'nullable|string',
            'efek_samping' => 'nullable|string',
            'peringatan' => 'nullable|string',
            'dosis' => 'nullable|string',
            'kategori' => 'nullable|in:Generik,Paten,OTC',
            'golongan' => 'nullable|in:Bebas,Bebas Terbatas,Keras,Narkotika,Psikotropika',
            'data_api' => 'nullable|json',
            'status' => 'required|in:Aktif,Nonaktif',
            'last_sync' => 'nullable|date',
        ]);

        ObatMaster::create($validated);

        return redirect()->route('obat-masters.index')->with('success', 'Data obat berhasil disimpan.');
    }

    /**
     * Tampilkan form edit obat.
     */
    public function edit($id)
    {
        $obat = ObatMaster::findOrFail($id);
        return view('obat_master.edit', compact('obat'));
    }

    /**
     * Update data obat.
     */
    public function update(Request $request, $id)
    {
        $obat = ObatMaster::findOrFail($id);

        $validated = $request->validate([
            'kfa_code' => 'required|string|max:50|unique:obat_masters,kfa_code,' . $obat->id_obat_master . ',id_obat_master',
            'nama_obat' => 'required|string|max:200',
            'nama_generik' => 'nullable|string|max:200',
            'bentuk_sediaan' => 'nullable|string|max:100',
            'kekuatan' => 'nullable|string|max:50',
            'satuan_kekuatan' => 'nullable|string|max:20',
            'kemasan' => 'nullable|string|max:50',
            'isi_kemasan' => 'nullable|string|max:50',
            'manufacturer' => 'nullable|string|max:200',
            'nie' => 'nullable|string|max:100',
            'komposisi' => 'nullable|string',
            'indikasi' => 'nullable|string',
            'kontraindikasi' => 'nullable|string',
            'efek_samping' => 'nullable|string',
            'peringatan' => 'nullable|string',
            'dosis' => 'nullable|string',
            'kategori' => 'nullable|in:Generik,Paten,OTC',
            'golongan' => 'nullable|in:Bebas,Bebas Terbatas,Keras,Narkotika,Psikotropika',
            'data_api' => 'nullable|json',
            'status' => 'required|in:Aktif,Nonaktif',
            'last_sync' => 'nullable|date',
        ]);

        $obat->update($validated);

        return redirect()->route('obat-masters.index')->with('success', 'Data obat berhasil diperbarui.');
    }

    /**
     * Hapus data obat.
     */
    public function destroy($id)
    {
        $obat = ObatMaster::findOrFail($id);
        $obat->delete();

        return redirect()->route('obat-masters.index')->with('success', 'Data obat berhasil dihapus.');
    }

    /**
     * Tampilkan detail obat (opsional, jika kamu ingin).
     */
    public function show($id)
    {
        $obat = ObatMaster::findOrFail($id);
        return view('obat.show', compact('obat'));
    }
}
