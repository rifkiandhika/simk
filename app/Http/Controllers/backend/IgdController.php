<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Igd;
use App\Models\Pasien;
use App\Models\Triase;
use App\Http\Requests\IgdStoreRequest;
use App\Http\Requests\IgdUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class IgdController extends Controller
{
    public function index(Request $request)
    {
        $query = Igd::with(['pasien', 'dokter', 'perawat', 'triase'])
            ->orderBy('waktu_datang', 'desc');

            
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('tanggal_mulai')) {
            $query->whereDate('waktu_datang', '>=', $request->tanggal_mulai);
        }
        if ($request->has('tanggal_akhir')) {
            $query->whereDate('waktu_datang', '<=', $request->tanggal_akhir);
        }

        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_rm', 'like', "%{$search}%")
                  ->orWhere('no_igd', 'like', "%{$search}%")
                  ->orWhereHas('pasien', function($q) use ($search) {
                      $q->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        $igdList = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $igdList
        ]);
    }

    
    public function store(IgdStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            
            $noIgd = $this->generateNoIgd();

            
            $pasien = Pasien::where('no_rm', $request->no_rm)->firstOrFail();

            $igd = Igd::create([
                'no_igd' => $noIgd,
                'no_rm' => $request->no_rm,
                'id_pasien' => $pasien->id_pasien,
                'waktu_datang' => $request->waktu_datang ?? now(),
                'cara_datang' => $request->cara_datang,
                'keluhan_utama' => $request->keluhan_utama,
                'tingkat_kesadaran' => $request->tingkat_kesadaran ?? 'CM',
                'status_triase' => $request->status_triase,
                'tindakan_awal' => $request->tindakan_awal,
                'status' => 'Dalam Perawatan',
                'dokter_jaga' => $request->dokter_jaga,
                'perawat_jaga' => $request->perawat_jaga,
                'created_by' => Auth::id()
            ]);

            
            if ($request->has('triase')) {
                Triase::create([
                    'id_igd' => $igd->id_igd,
                    'prioritas' => $request->triase['prioritas'],
                    'keluhan' => $request->triase['keluhan'] ?? null,
                    'vital_sign_ringkas' => $request->triase['vital_sign_ringkas'] ?? null,
                    'petugas' => Auth::id()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data IGD berhasil disimpan',
                'data' => $igd->load(['pasien', 'dokter', 'perawat', 'triase'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data IGD',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function show($id)
    {
        $igd = Igd::with([
            'pasien',
            'dokter',
            'perawat',
            'triase',
            'vitalSigns',
            'pemeriksaans',
            'tindakans',
            'reseps',
            'laboratorium',
            'radiologi'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $igd
        ]);
    }

    
    public function update(IgdUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $igd = Igd::findOrFail($id);

            $igd->update([
                'keluhan_utama' => $request->keluhan_utama ?? $igd->keluhan_utama,
                'tingkat_kesadaran' => $request->tingkat_kesadaran ?? $igd->tingkat_kesadaran,
                'status_triase' => $request->status_triase ?? $igd->status_triase,
                'tindakan_awal' => $request->tindakan_awal ?? $igd->tindakan_awal,
                'status' => $request->status ?? $igd->status,
                'waktu_keluar' => $request->waktu_keluar,
                'dokter_jaga' => $request->dokter_jaga ?? $igd->dokter_jaga,
                'perawat_jaga' => $request->perawat_jaga ?? $igd->perawat_jaga,
            ]);

            
            if ($request->has('triase')) {
                Triase::updateOrCreate(
                    ['id_igd' => $igd->id_igd],
                    [
                        'prioritas' => $request->triase['prioritas'],
                        'keluhan' => $request->triase['keluhan'] ?? null,
                        'vital_sign_ringkas' => $request->triase['vital_sign_ringkas'] ?? null,
                        'petugas' => Auth::id()
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data IGD berhasil diupdate',
                'data' => $igd->load(['pasien', 'dokter', 'perawat', 'triase'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data IGD',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pulang,Rawat Inap,Rujuk,Meninggal',
            'catatan' => 'nullable|string'
        ]);

        try {
            $igd = Igd::findOrFail($id);
            
            $igd->update([
                'status' => $request->status,
                'waktu_keluar' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Status pasien berhasil diubah menjadi {$request->status}",
                'data' => $igd
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status pasien',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function statistics()
    {
        $today = now()->toDateString();

        $stats = [
            'total_pasien_hari_ini' => Igd::whereDate('waktu_datang', $today)->count(),
            'dalam_perawatan' => Igd::where('status', 'Dalam Perawatan')->count(),
            'total_triase_merah' => Igd::where('status_triase', 'Merah')
                ->where('status', 'Dalam Perawatan')->count(),
            'total_triase_kuning' => Igd::where('status_triase', 'Kuning')
                ->where('status', 'Dalam Perawatan')->count(),
            'total_triase_hijau' => Igd::where('status_triase', 'Hijau')
                ->where('status', 'Dalam Perawatan')->count(),
            'total_rawat_inap' => Igd::whereDate('waktu_keluar', $today)
                ->where('status', 'Rawat Inap')->count(),
            'total_pulang' => Igd::whereDate('waktu_keluar', $today)
                ->where('status', 'Pulang')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    
    public function searchPasien(Request $request)
    {
        $request->validate([
            'no_rm' => 'required|string'
        ]);

        $pasien = Pasien::where('no_rm', $request->no_rm)
            ->with('asuransi')
            ->first();

        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Pasien tidak ditemukan'
            ], 404);
        }

        
        $riwayatIgd = Igd::where('id_pasien', $pasien->id_pasien)
            ->orderBy('waktu_datang', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'pasien' => $pasien,
                'riwayat_igd' => $riwayatIgd
            ]
        ]);
    }

    
    private function generateNoIgd()
    {
        $today = now()->format('Ymd');
        $prefix = "IGD-{$today}-";
        
        $lastIgd = Igd::where('no_igd', 'like', "{$prefix}%")
            ->orderBy('no_igd', 'desc')
            ->first();

        if ($lastIgd) {
            $lastNumber = intval(substr($lastIgd->no_igd, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber;
    }

    
    public function destroy($id)
    {
        try {
            $igd = Igd::findOrFail($id);
            
            // Check if can be deleted
            if ($igd->status != 'Dalam Perawatan') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya data dengan status "Dalam Perawatan" yang dapat dihapus'
                ], 400);
            }

            $igd->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data IGD berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data IGD',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
