<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class SatuanController extends Controller
{
    public function index()
    {
        $satuans = Satuan::orderBy('nama_satuan')->get();
        return view('satuan.index', compact('satuans'));
    }

    /**
     * Menampilkan form tambah satuan.
     */
    public function create()
    {
        $satuan = new Satuan();
        return view('satuan.create', compact('satuan'));
    }

    /**
     * Menyimpan satuan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:100|unique:satuans,nama_satuan',
            'deskripsi'   => 'nullable|string|max:255',
            'status'      => 'required|in:Aktif,Nonaktif',
        ]);

        try {
            Satuan::create([
                'id'           => Str::uuid(),
                'nama_satuan'  => $request->nama_satuan,
                'deskripsi'    => $request->deskripsi,
                'status'       => $request->status,
            ]);

            Alert::success('success', 'Satuan berhasil ditambahkan.');
            return redirect()->route('satuans.index');
        } catch (\Throwable $th) {

            Alert::success('error', 'Gagal menambahkan satuan.');
            return redirect()->back();
        }
    }

    /**
     * Menampilkan form edit.
     */
    public function edit($id)
    {
        $satuan = Satuan::findOrFail($id);
        return view('satuan.edit', compact('satuan'));
    }

    /**
     * Mengupdate data satuan.
     */
    public function update(Request $request, $id)
    {
        $satuan = Satuan::findOrFail($id);

        $request->validate([
            'nama_satuan' => 'required|string|max:100|unique:satuans,nama_satuan,' . $satuan->id,
            'deskripsi'   => 'nullable|string|max:255',
            'status'      => 'required|in:Aktif,Nonaktif',
        ]);

        try {
            $satuan->update([
                'nama_satuan' => $request->nama_satuan,
                'deskripsi'   => $request->deskripsi,
                'status'      => $request->status,
            ]);

            Alert::success('success', 'Satuan berhasil diperbarui.');
            return redirect()->route('satuans.index');
        } catch (\Throwable $th) {

            Alert::danger('error', 'Gagal memperbarui satuan.');
            return redirect()->back();
        }
    }

    /**
     * Menghapus satuan.
     */
    public function destroy($id)
    {
        try {
            $satuan = Satuan::findOrFail($id);
            $satuan->delete();

            Alert::success('success', 'Satuan berhasil dihapus.');
            return redirect()->route('satuans.index');
        } catch (\Throwable $th) {
            Alert::danger('danger', 'Gagal menghapus data satuan.');
            return redirect()->route('satuans.index');
        }
    }
}
