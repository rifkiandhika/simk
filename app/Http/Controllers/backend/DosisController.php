<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Dosis;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class DosisController extends Controller
{
    public function index()
    {
        $dosis = Dosis::orderBy('created_at', 'desc')->get();
        
        return view('dosis.index', compact('dosis'));
    }

    public function create()
    {
        return view('dosis.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jumlah' => 'required|string|max:100',
            'frekuensi' => 'required|string|max:100',
            'durasi' => 'nullable|string|max:100',
            'rute' => 'nullable|string|max:100',
        ], [
            'jumlah.required' => 'Jumlah dosis wajib diisi',
            'frekuensi.required' => 'Frekuensi wajib diisi',
        ]);

        $validated['id'] = Str::uuid();

        Dosis::create($validated);

        Alert::success('success','Data dosis berhasil ditambahkan!');
        return redirect()->route('dosis.index');
    }

    public function edit(Dosis $dosis)
    {
        return view('dosis.edit', compact('dosis'));
    }

    public function update(Request $request, Dosis $dosis)
    {
        $validated = $request->validate([
            'jumlah' => 'required|string|max:100',
            'frekuensi' => 'required|string|max:100',
            'durasi' => 'nullable|string|max:100',
            'rute' => 'nullable|string|max:100',
        ], [
            'jumlah.required' => 'Jumlah dosis wajib diisi',
            'frekuensi.required' => 'Frekuensi wajib diisi',
        ]);

        $dosis->update($validated);

        Alert::success('success', 'Data dosis berhasil diperbarui!');
        return redirect()->route('dosis.index');
    }

    public function destroy(Dosis $dosis)
    {
        try {
            $dosis->delete();
            return redirect()->route('dosis.index')
                ->with('success', 'Data dosis berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('dosis.index')
                ->with('error', 'Gagal menghapus data dosis!');
        }
    }
}
