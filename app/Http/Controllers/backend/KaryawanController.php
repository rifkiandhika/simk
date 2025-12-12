<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawans = Karyawan::all();
        return view('karyawan.index', compact('karyawans'));
    }

    public function create()
    {
        return view('karyawan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:karyawans,nip',
            'nama_lengkap' => 'required',
            'pin' => 'required|digits:6|unique:karyawans,pin',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        $data['id_karyawan'] = Str::uuid();

        if ($request->hasFile('foto')) {
            $filename = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('foto_karyawan'), $filename);
            $data['foto'] = $filename;
        }

        Karyawan::create($data);

        Alert::success('success', 'Karyawan berhasil ditambahkan.');
        return redirect()->route('karyawans.index');
    }

    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'nip' => 'required|unique:karyawans,nip,' . $karyawan->id_karyawan . ',id_karyawan',
            'nama_lengkap' => 'required',
            'pin' => 'required|digits:6|unique:karyawans,pin,' . $karyawan->id_karyawan . ',id_karyawan',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {

            // hapus foto lama
            if ($karyawan->foto && file_exists(public_path('foto_karyawan/' . $karyawan->foto))) {
                unlink(public_path('foto_karyawan/' . $karyawan->foto));
            }

            $filename = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('foto_karyawan'), $filename);
            $data['foto'] = $filename;
        }

        $karyawan->update($data);


        Alert::info('success', 'Karyawan berhasil diupdate.');
        return redirect()->route('karyawans.index');
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        if ($karyawan->foto && file_exists(public_path('foto_karyawan/' . $karyawan->foto))) {
            unlink(public_path('foto_karyawan/' . $karyawan->foto));
        }

        $karyawan->delete();

        Alert::info('success', 'Karyawan berhasil dihapus.');
        return back();
    }
}
