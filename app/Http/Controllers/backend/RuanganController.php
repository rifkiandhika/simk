<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class RuanganController extends Controller
{
    public function index()
    {
        $ruangans = Ruangan::orderBy('kode_ruangan', 'asc')->get();
        return view('ruangans.index', compact('ruangans'));
    }

    public function create()
    {
        return view('ruangans.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_ruangan' => 'required|string|max:50|unique:ruangans,kode_ruangan',
            'nama_ruangan' => 'required|string|max:255',
            'jenis' => 'required|in:rawat_jalan,rawat_inap,igd,penunjang',
            'kapasitas' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ], [
            'kode_ruangan.required' => 'Kode ruangan wajib diisi',
            'kode_ruangan.unique' => 'Kode ruangan sudah digunakan',
            'nama_ruangan.required' => 'Nama ruangan wajib diisi',
            'jenis.required' => 'Jenis ruangan wajib dipilih',
            'jenis.in' => 'Jenis ruangan tidak valid',
            'kapasitas.required' => 'Kapasitas wajib diisi',
            'kapasitas.min' => 'Kapasitas minimal 0',
            'status.required' => 'Status wajib dipilih',
        ]);

        $validated['id'] = Str::uuid();
        $validated['kode_ruangan'] = strtoupper($validated['kode_ruangan']);

        Ruangan::create($validated);

        Alert::success('success', 'Data ruangan berhasil ditambahkan');
        return redirect()->route('ruangans.index');
    }

    public function edit($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        return view('ruangans.form', compact('ruangan'));
    }

    public function update(Request $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);

        $validated = $request->validate([
            'kode_ruangan' => 'required|string|max:50|unique:ruangans,kode_ruangan,' . $id,
            'nama_ruangan' => 'required|string|max:255',
            'jenis' => 'required|in:rawat_jalan,rawat_inap,igd,penunjang',
            'kapasitas' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ], [
            'kode_ruangan.required' => 'Kode ruangan wajib diisi',
            'kode_ruangan.unique' => 'Kode ruangan sudah digunakan',
            'nama_ruangan.required' => 'Nama ruangan wajib diisi',
            'jenis.required' => 'Jenis ruangan wajib dipilih',
            'jenis.in' => 'Jenis ruangan tidak valid',
            'kapasitas.required' => 'Kapasitas wajib diisi',
            'kapasitas.min' => 'Kapasitas minimal 0',
            'status.required' => 'Status wajib dipilih',
        ]);

        $validated['kode_ruangan'] = strtoupper($validated['kode_ruangan']);

        $ruangan->update($validated);

        Alert::success('success', 'Data ruangan berhasil diperbarui');
        return redirect()->route('ruangans.index');
    }

    public function destroy($id)
    {
        try {
            $ruangan = Ruangan::findOrFail($id);
            $ruangan->delete();

            return redirect()->route('ruangans.index')
                             ->with('success', 'Data ruangan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('ruangans.index')
                             ->with('error', 'Gagal menghapus data ruangan');
        }
    }
}
