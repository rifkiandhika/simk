<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Reagen;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class ReagenController extends Controller
{
    public function index()
    {
        $reagens = Reagen::latest()->get();

        // Statistik sederhana
        $totalAktif = $reagens->where('status', 'Aktif')->count();
        $totalNonaktif = $reagens->where('status', 'Nonaktif')->count();
        $withHazard = $reagens->whereNotNull('bahaya_keselamatan')->where('bahaya_keselamatan', '!=', '')->count();
        $withPrice = $reagens->whereNotNull('harga_beli')->where('harga_beli', '>', 0)->count();

        return view('reagen.index', compact('reagens', 'totalAktif', 'totalNonaktif', 'withHazard', 'withPrice'));
    }

    public function create()
    {
        $reagen = new Reagen;
        $satuans = Satuan::where('status', 'Aktif')->orderBy('nama_satuan')->get();
        return view('reagen.create', compact('reagen', 'satuans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_reagensia' => 'required|string|max:50|unique:reagensias,kode_reagensia',
            'nama_reagensia' => 'required|string|max:200',
            'merk' => 'nullable|string|max:100',
            'no_katalog' => 'nullable|string|max:100',
            'komposisi' => 'nullable|string',
            'satuan' => 'required|string|max:50',
            'volume_kemasan' => 'nullable|string|max:50',
            'suhu_penyimpanan_min' => 'nullable|numeric|min:-273.15|max:1000',
            'suhu_penyimpanan_max' => 'nullable|numeric|min:-273.15|max:1000|gte:suhu_penyimpanan_min',
            'kondisi_penyimpanan' => 'nullable|string',
            'stabilitas_hari' => 'nullable|integer|min:0',
            'prosedur_penggunaan' => 'nullable|string',
            'bahaya_keselamatan' => 'nullable|string',
            'stok_minimal' => 'required|integer|min:0',
            'lokasi_penyimpanan' => 'nullable|string|max:100',
            'status' => 'required|in:Aktif,Nonaktif',

            // kolom harga (sekarang di tabel reagensias)
            'harga_beli' => 'nullable|min:0',
            'harga_per_test' => 'nullable|min:0',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        try {
            DB::beginTransaction();

            Reagen::create([
                'id' => Str::uuid(),
                'kode_reagensia' => strtoupper($validated['kode_reagensia']),
                'nama_reagensia' => $validated['nama_reagensia'],
                'merk' => $validated['merk'],
                'no_katalog' => $validated['no_katalog'],
                'komposisi' => $validated['komposisi'],
                'satuan' => $validated['satuan'],
                'volume_kemasan' => $validated['volume_kemasan'],
                'suhu_penyimpanan_min' => $validated['suhu_penyimpanan_min'],
                'suhu_penyimpanan_max' => $validated['suhu_penyimpanan_max'],
                'kondisi_penyimpanan' => $validated['kondisi_penyimpanan'],
                'stabilitas_hari' => $validated['stabilitas_hari'],
                'prosedur_penggunaan' => $validated['prosedur_penggunaan'],
                'bahaya_keselamatan' => $validated['bahaya_keselamatan'],
                'stok_minimal' => $validated['stok_minimal'],
                'lokasi_penyimpanan' => $validated['lokasi_penyimpanan'],
                'status' => $validated['status'],
                'harga_beli' => $validated['harga_beli'] ?? null,
                'harga_per_test' => $validated['harga_per_test'] ?? null,
                'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
                'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
                'created_by' => Auth::user()->id_karyawan ?? null,
            ]);

            DB::commit();
            Alert::success('Berhasil', 'Data reagensia berhasil ditambahkan');
            return redirect()->route('reagens.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $reagen = Reagen::findOrFail($id);
        return view('reagen.show', compact('reagen'));
    }

    public function edit($id)
    {
        $reagen = Reagen::findOrFail($id);
        return view('reagen.edit', compact('reagen'));
    }

    public function update(Request $request, $id)
    {
        $reagen = Reagen::findOrFail($id);

        $validated = $request->validate([
            'kode_reagensia' => 'required|string|max:50|unique:reagensias,kode_reagensia,' . $id,
            'nama_reagensia' => 'required|string|max:200',
            'merk' => 'nullable|string|max:100',
            'no_katalog' => 'nullable|string|max:100',
            'komposisi' => 'nullable|string',
            'satuan' => 'required|string|max:50',
            'volume_kemasan' => 'nullable|string|max:50',
            'suhu_penyimpanan_min' => 'nullable|numeric|min:-273.15|max:1000',
            'suhu_penyimpanan_max' => 'nullable|numeric|min:-273.15|max:1000|gte:suhu_penyimpanan_min',
            'kondisi_penyimpanan' => 'nullable|string',
            'stabilitas_hari' => 'nullable|integer|min:0',
            'prosedur_penggunaan' => 'nullable|string',
            'bahaya_keselamatan' => 'nullable|string',
            'stok_minimal' => 'required|integer|min:0',
            'lokasi_penyimpanan' => 'nullable|string|max:100',
            'status' => 'required|in:Aktif,Nonaktif',

            // kolom harga
            'harga_beli' => 'nullablemin:0',
            'harga_per_test' => 'nullable|min:0',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        try {
            DB::beginTransaction();

            $reagen->update([
                'kode_reagensia' => strtoupper($validated['kode_reagensia']),
                'nama_reagensia' => $validated['nama_reagensia'],
                'merk' => $validated['merk'],
                'no_katalog' => $validated['no_katalog'],
                'komposisi' => $validated['komposisi'],
                'satuan' => $validated['satuan'],
                'volume_kemasan' => $validated['volume_kemasan'],
                'suhu_penyimpanan_min' => $validated['suhu_penyimpanan_min'],
                'suhu_penyimpanan_max' => $validated['suhu_penyimpanan_max'],
                'kondisi_penyimpanan' => $validated['kondisi_penyimpanan'],
                'stabilitas_hari' => $validated['stabilitas_hari'],
                'prosedur_penggunaan' => $validated['prosedur_penggunaan'],
                'bahaya_keselamatan' => $validated['bahaya_keselamatan'],
                'stok_minimal' => $validated['stok_minimal'],
                'lokasi_penyimpanan' => $validated['lokasi_penyimpanan'],
                'status' => $validated['status'],
                'harga_beli' => $validated['harga_beli'] ?? $reagen->harga_beli,
                'harga_per_test' => $validated['harga_per_test'] ?? $reagen->harga_per_test,
                'tanggal_mulai' => $validated['tanggal_mulai'] ?? $reagen->tanggal_mulai,
                'tanggal_selesai' => $validated['tanggal_selesai'] ?? $reagen->tanggal_selesai,
                'updated_by' => Auth::user()->id_karyawan ?? null,
            ]);

            DB::commit();
            Alert::success('Berhasil', 'Data reagensia berhasil diperbarui');
            return redirect()->route('reagens.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $reagen = Reagen::findOrFail($id);
            $reagen->delete();

            DB::commit();

            Alert::info('Berhasil', 'Data reagensia berhasil dihapus');
            return redirect()->route('reagens.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
