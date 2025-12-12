<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Asuransi;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AsuransiController extends Controller
{
    public function index()
    {
        $asuransis = Asuransi::latest()->get();

        // Statistics
        $totalAktif = $asuransis->where('status', 'Aktif')->count();
        $totalNonaktif = $asuransis->where('status', 'Nonaktif')->count();
        $kontrakAktif = $asuransis->where('status', 'Aktif')
            ->whereNotNull('tanggal_kontrak_mulai')
            ->where('tanggal_kontrak_selesai', '>=', now())
            ->count();

        return view('asuransi.index', compact('asuransis', 'totalAktif', 'totalNonaktif', 'kontrakAktif'));
    }

    public function create()
    {
        return view('asuransi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_asuransi' => 'required|string|max:100',
            'tipe' => 'nullable|string|max:50',
            'no_kontrak' => 'nullable|string|max:255',
            'tanggal_kontrak_mulai' => 'nullable|date',
            'tanggal_kontrak_selesai' => 'nullable|date|after_or_equal:tanggal_kontrak_mulai',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        try {
            Asuransi::create([
                'id' => Str::uuid(),
                'nama_asuransi' => $validated['nama_asuransi'],
                'tipe' => $validated['tipe'],
                'no_kontrak' => $validated['no_kontrak'],
                'tanggal_kontrak_mulai' => $validated['tanggal_kontrak_mulai'],
                'tanggal_kontrak_selesai' => $validated['tanggal_kontrak_selesai'],
                'alamat' => $validated['alamat'],
                'no_telp' => $validated['no_telp'],
                'email' => $validated['email'],
                'status' => $validated['status'],
            ]);

            Alert::success('Berhasil', 'Data asuransi berhasil ditambahkan');
            return redirect()->route('asuransis.index');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $asuransi = Asuransi::findOrFail($id);
        return view('asuransi.show', compact('asuransi'));
    }

    public function edit($id)
    {
        $asuransi = Asuransi::findOrFail($id);
        return view('asuransi.edit', compact('asuransi'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_asuransi' => 'required|string|max:100',
            'tipe' => 'nullable|string|max:50',
            'no_kontrak' => 'nullable|string|max:255',
            'tanggal_kontrak_mulai' => 'nullable|date',
            'tanggal_kontrak_selesai' => 'nullable|date|after_or_equal:tanggal_kontrak_mulai',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        try {
            $asuransi = Asuransi::findOrFail($id);
            $asuransi->update($validated);

            Alert::success('Berhasil', 'Data asuransi berhasil diperbarui');
            return redirect()->route('asuransis.index');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $asuransi = Asuransi::findOrFail($id);
            $asuransi->delete();

            Alert::info('Berhasil', 'Data asuransi berhasil dihapus!');
            return redirect()->route('asuransis.index');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
