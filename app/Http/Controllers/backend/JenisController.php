<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Jenis;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class JenisController extends Controller
{
    public function index()
    {
        $jenis = Jenis::orderBy('nama_jenis')->get();
        return view('jenis.index', compact('jenis'));
    }

    /**
     * Menampilkan form tambah jenis.
     */
    public function create()
    {
        $jenis = new Jenis();
        return view('jenis.create', compact('jenis'));
    }

    /**
     * Menyimpan data jenis baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_jenis' => 'required|string|max:100|unique:jenis,nama_jenis',
            'deskripsi'  => 'nullable|string|max:255',
            'status'     => 'required|in:Aktif,Nonaktif',
        ]);

        try {
            Jenis::create([
                'id'          => Str::uuid(),
                'nama_jenis'  => $request->nama_jenis,
                'deskripsi'   => $request->deskripsi,
                'status'      => $request->status,
            ]);

            Alert::success('success', 'Jenis berhasil ditambahkan.');
            return redirect()->route('jenis.index');
        } catch (\Throwable $th) {
            Alert::error('error', 'Gagal menambahkan jenis.');
            return redirect()->back();
        }
    }

    /**
     * Menampilkan form edit jenis.
     */
    public function edit($id)
    {
        $jenis = Jenis::findOrFail($id);
        return view('jenis.edit', compact('jenis'));
    }

    /**
     * Mengupdate data jenis.
     */
    public function update(Request $request, $id)
    {
        $jenis = Jenis::findOrFail($id);

        $request->validate([
            'nama_jenis' => 'required|string|max:100|unique:jenis,nama_jenis,' . $jenis->id,
            'deskripsi'  => 'nullable|string|max:255',
            'status'     => 'required|in:Aktif,Nonaktif',
        ]);

        try {
            $jenis->update([
                'nama_jenis' => $request->nama_jenis,
                'deskripsi'  => $request->deskripsi,
                'status'     => $request->status,
            ]);

            Alert::success('success', 'Jenis berhasil diperbarui.');
            return redirect()->route('jenis.index');
        } catch (\Throwable $th) {
            Alert::error('error', 'Gagal memperbarui jenis.');
            return redirect()->back();
        }
    }

    /**
     * Menghapus data jenis.
     */
    public function destroy($id)
    {
        try {
            $jenis = Jenis::findOrFail($id);
            $jenis->delete();

            Alert::success('success', 'Jenis berhasil dihapus.');
            return redirect()->route('jenis.index');
        } catch (\Throwable $th) {
            Alert::error('error', 'Gagal menghapus data jenis.');
            return redirect()->route('jenis.index');
        }
    }
}
