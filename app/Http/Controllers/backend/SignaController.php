<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Signa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class SignaController extends Controller
{
    public function index()
    {
        $signas = Signa::orderBy('kode_signa', 'asc')->get();
        
        return view('signa.index', compact('signas'));
    }

    public function create()
    {
        return view('signa.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_signa' => 'required|string|max:50|unique:signas,kode_signa',
            'kepanjangan' => 'nullable|string|max:200',
            'deskripsi' => 'required|string|max:500',
        ], [
            'kode_signa.required' => 'Kode signa wajib diisi',
            'kode_signa.unique' => 'Kode signa sudah terdaftar',
            'deskripsi.required' => 'Deskripsi wajib diisi',
        ]);

        $validated['id'] = Str::uuid();
        $validated['kode_signa'] = strtoupper($validated['kode_signa']);

        Signa::create($validated);

        Alert::success('success', 'Data signa berhasil ditambahkan!');
        return redirect()->route('signas.index');
    }

    public function edit(Signa $signa)
    {
        return view('signa.edit', compact('signa'));
    }

    public function update(Request $request, Signa $signa)
    {
        $validated = $request->validate([
            'kode_signa' => 'required|string|max:50|unique:signas,kode_signa,' . $signa->id,
            'kepanjangan' => 'nullable|string|max:200',
            'deskripsi' => 'required|string|max:500',
        ], [
            'kode_signa.required' => 'Kode signa wajib diisi',
            'kode_signa.unique' => 'Kode signa sudah terdaftar',
            'deskripsi.required' => 'Deskripsi wajib diisi',
        ]);

        $validated['kode_signa'] = strtoupper($validated['kode_signa']);

        $signa->update($validated);

        Alert::success('success', 'Data signa berhasil diperbarui!');
        return redirect()->route('signas.index');
    }

    public function destroy(Signa $signa)
    {
        try {
            $signa->delete();
            return redirect()->route('signa.index')
                ->with('success', 'Data signa berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('signa.index')
                ->with('error', 'Gagal menghapus data signa!');
        }
    }
}
