<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\alkes\AlkesRequest;
use App\Models\Alkes;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AlkesController extends Controller
{
    public function index()
    {
        $alkes = Alkes::with('createdBy')->get();


        $totalAktif = $alkes->where('status', 'Aktif')->count();
        $totalRusak = $alkes->whereIn('status', ['Rusak', 'Dalam Perbaikan'])->count();
        $stokRendah = $alkes->filter(function ($item) {
            return $item->jumlah_stok <= $item->stok_minimal;
        })->count();
        $needKalibrasi = $alkes->filter(function ($item) {
            return $item->tanggal_kalibrasi_berikutnya &&
                $item->tanggal_kalibrasi_berikutnya->isPast();
        })->count();
        $alatMedis = $alkes->where('kategori', 'Alat Medis')->count();
        $alatLab = $alkes->where('kategori', 'Alat Lab')->count();

        return view('alkes.index', compact(
            'alkes',
            'totalAktif',
            'totalRusak',
            'stokRendah',
            'needKalibrasi',
            'alatMedis',
            'alatLab'
        ));
    }

    public function create()
    {
        $alkes = new Alkes();
        return view('alkes.create', compact('alkes'));
    }

    public function store(AlkesRequest $request)
    {
        try {
            DB::beginTransaction();

            Alkes::create([
                'id' => Str::uuid(),
                'kode_alkes' => $request->kode_alkes,
                'nama_alkes' => $request->nama_alkes,
                'merk' => $request->merk,
                'model' => $request->model,
                'spesifikasi' => $request->spesifikasi,
                'satuan' => $request->satuan,
                'kategori' => $request->kategori,
                'tanggal_kalibrasi_terakhir' => $request->tanggal_kalibrasi_terakhir,
                'tanggal_kalibrasi_berikutnya' => $request->tanggal_kalibrasi_berikutnya,
                'maintenance_schedule' => $request->maintenance_schedule,
                'stok_minimal' => $request->stok_minimal,
                'jumlah_stok' => $request->jumlah_stok,
                'no_batch' => $request->no_batch,
                'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
                'kondisi' => $request->kondisi,
                'lokasi_penyimpanan' => $request->lokasi_penyimpanan,
                'harga_beli' => $request->harga_beli,
                'harga_jual_umum' => $request->harga_jual_umum,
                'harga_jual_bpjs' => $request->harga_jual_bpjs,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'catatan' => $request->catatan,
                'status' => $request->status,
                'created_by' => Auth::user()->id_karyawan,
            ]);

            DB::commit();

            Alert::success('Berhasil', 'Data alkes berhasil ditambahkan');
            return redirect()->route('alkes.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $alkes = Alkes::with(['createdBy', 'updatedBy'])->findOrFail($id);
        return view('alkes.show', compact('alkes'));
    }

    public function edit($id)
    {
        $alkes = Alkes::findOrFail($id);
        return view('alkes.edit', compact('alkes'));
    }

    public function update(AlkesRequest $request, $id)
    {
        $alkes = Alkes::findOrFail($id);

        try {
            DB::beginTransaction();

            $alkes->update([
                'kode_alkes' => $request->kode_alkes,
                'nama_alkes' => $request->nama_alkes,
                'merk' => $request->merk,
                'model' => $request->model,
                'spesifikasi' => $request->spesifikasi,
                'satuan' => $request->satuan,
                'kategori' => $request->kategori,
                'tanggal_kalibrasi_terakhir' => $request->tanggal_kalibrasi_terakhir,
                'tanggal_kalibrasi_berikutnya' => $request->tanggal_kalibrasi_berikutnya,
                'maintenance_schedule' => $request->maintenance_schedule,
                'stok_minimal' => $request->stok_minimal,
                'jumlah_stok' => $request->jumlah_stok,
                'no_batch' => $request->no_batch,
                'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
                'kondisi' => $request->kondisi,
                'lokasi_penyimpanan' => $request->lokasi_penyimpanan,
                'harga_beli' => $request->harga_beli,
                'harga_jual_umum' => $request->harga_jual_umum,
                'harga_jual_bpjs' => $request->harga_jual_bpjs,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'catatan' => $request->catatan,
                'status' => $request->status,
                'updated_by' => Auth::user()->id_karyawan,
            ]);

            DB::commit();

            Alert::success('Berhasil', 'Data alkes berhasil diperbarui');
            return redirect()->route('alkes.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $alkes = Alkes::findOrFail($id);
            $alkes->delete();

            DB::commit();

            Alert::info('Berhasil', 'Data alkes berhasil dihapus!');
            return redirect()->route('alkes.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
