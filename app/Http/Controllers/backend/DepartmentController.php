<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::latest()->get();

        // Statistics
        $totalAktif = $departments->where('status', 'Aktif')->count();
        $totalNonaktif = $departments->where('status', 'Nonaktif')->count();
        $totalMedis = $departments->where('jenis', 'Medis')->count();
        $totalNonMedis = $departments->where('jenis', 'Non-Medis')->count();
        $totalPenunjang = $departments->where('jenis', 'Penunjang')->count();

        return view('department.index', compact(
            'departments',
            'totalAktif',
            'totalNonaktif',
            'totalMedis',
            'totalNonMedis',
            'totalPenunjang'
        ));
    }

    public function create()
    {
        return view('department.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_department' => 'required|string|max:20|unique:department,kode_department',
            'nama_department' => 'required|string|max:100',
            'lokasi' => 'nullable|string|max:255',
            'kepala_department' => 'nullable|string|max:255',
            'jenis' => 'required|in:Medis,Non-Medis,Penunjang',
            'status' => 'required|in:Aktif,Nonaktif',
        ], [
            'kode_department.required' => 'Kode department wajib diisi',
            'kode_department.unique' => 'Kode department sudah digunakan',
            'nama_department.required' => 'Nama department wajib diisi',
            'jenis.required' => 'Jenis department wajib dipilih',
            'status.required' => 'Status department wajib dipilih',
        ]);

        try {
            Department::create([
                'id' => Str::uuid(),
                'kode_department' => strtoupper($validated['kode_department']),
                'nama_department' => $validated['nama_department'],
                'lokasi' => $validated['lokasi'],
                'kepala_department' => $validated['kepala_department'],
                'jenis' => $validated['jenis'],
                'status' => $validated['status'],
            ]);

            Alert::success('Berhasil', 'Data department berhasil ditambahkan');
            return redirect()->route('departments.index');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $department = Department::findOrFail($id);
        return view('department.show', compact('department'));
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        return view('department.edit', compact('department'));
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'kode_department' => 'required|string|max:20|unique:department,kode_department,' . $id,
            'nama_department' => 'required|string|max:100',
            'lokasi' => 'nullable|string|max:255',
            'kepala_department' => 'nullable|string|max:255',
            'jenis' => 'required|in:Medis,Non-Medis,Penunjang',
            'status' => 'required|in:Aktif,Nonaktif',
        ], [
            'kode_department.required' => 'Kode department wajib diisi',
            'kode_department.unique' => 'Kode department sudah digunakan',
            'nama_department.required' => 'Nama department wajib diisi',
            'jenis.required' => 'Jenis department wajib dipilih',
            'status.required' => 'Status department wajib dipilih',
        ]);

        try {
            $department->update([
                'kode_department' => strtoupper($validated['kode_department']),
                'nama_department' => $validated['nama_department'],
                'lokasi' => $validated['lokasi'],
                'kepala_department' => $validated['kepala_department'],
                'jenis' => $validated['jenis'],
                'status' => $validated['status'],
            ]);

            Alert::success('Berhasil', 'Data department berhasil diperbarui');
            return redirect()->route('departments.index');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $department = Department::findOrFail($id);
            $department->delete();

            Alert::info('Berhasil', 'Data department berhasil dihapus!');
            return redirect()->route('departments.index');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
