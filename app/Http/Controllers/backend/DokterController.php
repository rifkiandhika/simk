<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class DokterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dokters = Dokter::with('ruangans')->get();
        return view('dokters.index', compact('dokters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ruangans = Ruangan::where('status', 1)
            ->orderBy('nama_ruangan')
            ->get();

        return view('dokters.create', compact('ruangans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_dokter'  => 'required|string|max:50|unique:dokters,kode_dokter',
            'nama_dokter'  => 'required|string|max:255',
            'spesialisasi' => 'nullable|string|max:255',
            'no_str'       => 'nullable|string|max:255',
            'no_sip'       => 'nullable|string|max:255',
            'status'       => 'required',
            'ruangans'     => 'nullable|array',
            'ruangans.*'   => 'exists:ruangans,id',
        ]);

        try {
            // 🔥 PISAHKAN DATA PIVOT
            $ruangans = $validated['ruangans'] ?? [];
            unset($validated['ruangans']);

            // ✅ SIMPAN KE TABEL dokters
            $dokter = Dokter::create($validated);

            // ✅ SIMPAN KE TABEL PIVOT
            if (!empty($ruangans)) {
                $dokter->ruangans()->attach($ruangans);
            }

            Alert::success('success', 'Data dokter berhasil ditambahkan!');
            return redirect()
                ->route('dokters.index');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data dokter: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dokter $dokter)
    {
        $ruangans = Ruangan::where('status', 1)
            ->orderBy('nama_ruangan')
            ->get();

        $dokter->load('ruangans');

        return view('dokters.edit', compact('dokter', 'ruangans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dokter $dokter)
    {
        $validated = $request->validate([
            'kode_dokter'  => 'required|string|max:50|unique:dokters,kode_dokter,' . $dokter->id_dokter,
            'nama_dokter'  => 'required|string|max:255',
            'spesialisasi' => 'nullable|string|max:255',
            'no_str'       => 'nullable|string|max:255',
            'no_sip'       => 'nullable|string|max:255',
            'status'       => 'required',
            'ruangans'     => 'nullable|array',
            'ruangans.*'   => 'exists:ruangans,id',
        ]);

        try {
            // 🔥 PISAHKAN DATA PIVOT
            $ruangans = $validated['ruangans'] ?? [];
            unset($validated['ruangans']);

            // ✅ UPDATE DATA dokters
            $dokter->update($validated);

            // ✅ SYNC DATA PIVOT
            $dokter->ruangans()->sync($ruangans);

            Alert::success('success', 'Data dokter berhasil diperbarui!');
            return redirect()
                ->route('dokters.index');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data dokter: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dokter $dokter)
    {
        try {
            $dokter->delete();

            Alert::success('success', 'Data dokter berhasil dihapus!');
            return redirect()
                ->route('dokters.index');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus data dokter: ' . $e->getMessage());
        }
    }
}
