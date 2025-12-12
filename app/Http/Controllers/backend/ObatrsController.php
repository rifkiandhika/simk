<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreObatRsRequest;
use App\Http\Requests\UpdateObatRsRequest;
use App\Models\DetailobatRs;
use App\Models\ObatRs;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ObatrsController extends Controller
{
    public function index()
    {
        $obats = ObatRs::withCount('detailObats')->get();
        return view('obat_rs.index', compact('obats'));
    }

    public function create()
    {
        return view('obat_rs.create');
    }

    public function store(StoreObatRsRequest $request)
    {
        $validated = $request->validated();

        $obat = ObatRs::create([
            'id_obat_rs' => Str::uuid(),
            'nama_obat' => $validated['nama_obat'],
            'nama_obat_internasional' => $validated['nama_obat_internasional'],
        ]);

        foreach ($validated['id_obat_master'] as $i => $idMaster) {
            DetailObatRs::create([
                'id_detail_obat_rs' => Str::uuid(),
                'id_obat_rs' => $obat->id_obat_rs,
                'id_obat_master' => $idMaster,
                'kode_obat_rs' => $validated['kode_obat_rs'][$i],
                'nama_obat_rs' => $validated['nama_obat_rs'][$i],
                'stok_minimal' => $validated['stok_minimal'][$i] ?? 0,
                'stok_maksimal' => $validated['stok_maksimal'][$i] ?? 0,
                'lokasi_penyimpanan' => $validated['lokasi_penyimpanan'][$i] ?? null,
                'catatan_khusus' => $validated['catatan_khusus'][$i] ?? null,
                'status_aktif' => $validated['status_aktif'][$i],
                'created_by' => auth()->user()->id_karyawan ?? null,
            ]);
        }

        return redirect()->route('obatrs.index')->with('success', 'Data obat RS berhasil disimpan!');
    }

    public function edit($id)
    {
        $obat = ObatRs::with('detailObats')->findOrFail($id);
        return view('obat_rs.edit', compact('obat'));
    }

    public function update(UpdateObatRsRequest $request, $id)
    {
        $validated = $request->validated();
        $obat = ObatRs::findOrFail($id);

        $obat->update([
            'nama_obat' => $validated['nama_obat'],
            'nama_obat_internasional' => $validated['nama_obat_internasional'],
        ]);

        $existingDetailIds = $obat->detailObats()->pluck('id_detail_obat_rs')->toArray();
        $updatedDetailIds = [];

        foreach ($validated['id_obat_master'] as $i => $idMaster) {
            $detailId = $validated['id_detail_obat_rs'][$i] ?? null;

            $detail = DetailObatRs::updateOrCreate(
                [
                    'id_detail_obat_rs' => $detailId,
                ],
                [
                    'id_obat_rs' => $obat->id_obat_rs,
                    'id_obat_master' => $idMaster,
                    'kode_obat_rs' => $validated['kode_obat_rs'][$i],
                    'nama_obat_rs' => $validated['nama_obat_rs'][$i],
                    'stok_minimal' => $validated['stok_minimal'][$i] ?? 0,
                    'stok_maksimal' => $validated['stok_maksimal'][$i] ?? 0,
                    'lokasi_penyimpanan' => $validated['lokasi_penyimpanan'][$i] ?? null,
                    'catatan_khusus' => $validated['catatan_khusus'][$i] ?? null,
                    'status_aktif' => $validated['status_aktif'][$i],
                    'created_by' => auth()->user()->id_karyawan ?? null,
                ]
            );

            $updatedDetailIds[] = $detail->id_detail_obat_rs;
        }

        $toDelete = array_diff($existingDetailIds, $updatedDetailIds);
        if (!empty($toDelete)) {
            DetailObatRs::whereIn('id_detail_obat_rs', $toDelete)->delete();
        }

        return redirect()
            ->route('obatrs.index')
            ->with('success', 'Data obat RS berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $obat = ObatRs::findOrFail($id);
        $obat->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }
}
