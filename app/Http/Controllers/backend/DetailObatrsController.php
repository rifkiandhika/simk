<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailobatRs;
use App\Models\HargaObat;
use App\Models\HargaObatAsuransi;
use App\Models\KFAMappingHistory;
use App\Models\ObatMaster;
use App\Models\ObatRs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DetailObatrsController extends Controller
{
    public function edit($obatId, $detailId)
    {
        $obat = ObatRs::findOrFail($obatId);
        $detail = DetailobatRs::with('obatMaster')
            ->where('id_obat_rs', $obatId)
            ->findOrFail($detailId);

        // Load harga obat
        $hargaObat = HargaObat::where('id_detail_obat_rs', $detailId)
            ->where('aktif', true)
            ->first();

        // Load harga asuransi
        $hargaAsuransi = HargaObatAsuransi::with('asuransi')
            ->where('id_detail_obat_rs', $detailId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('obat_rs.detail', compact('obat', 'detail', 'hargaObat', 'hargaAsuransi'));
    }

    /**
     * Update semua data (detail obat + harga) via AJAX
     */
    public function updateAll(Request $request, $obatId, $detailId)
    {
        // Bersihkan format rupiah untuk semua field harga
        $priceFields = [
            'harga_obat', 
            'harga_khusus', 
            'harga_bpjs', 
            'embalase', 
            'embalase_khusus', 
            'embalase_bpjs',
            'jasa_racik',
            'jasa_racik_khusus',
            'jasa_racik_bpjs'
        ];

        foreach ($priceFields as $field) {
            if ($request->filled($field)) {
                $request->merge([
                    $field => preg_replace('/\D/', '', $request->input($field))
                ]);
            }
        }

        $validated = $request->validate([
            'kode_obat_rs' => 'required|string|max:50|unique:detail_obat_rs,kode_obat_rs,' . $detailId . ',id_detail_obat_rs',
            'nama_obat_rs' => 'nullable|string|min:0',
            'stok_minimal' => 'nullable|integer|min:0',
            'stok_maksimal' => 'nullable|integer|min:0',
            'lokasi_penyimpanan' => 'nullable|string|max:100',
            'catatan_khusus' => 'nullable|string',
            'status_aktif' => 'required|in:Aktif,Nonaktif,Diskontinyu',
            
            // Harga Umum
            'harga_obat' => 'nullable|integer|min:0',
            'embalase' => 'nullable|integer|min:0',
            'jasa_racik' => 'nullable|integer|min:0',
            
            // Harga Khusus/Promo
            'harga_khusus' => 'nullable|integer|min:0',
            'embalase_khusus' => 'nullable|integer|min:0',
            'jasa_racik_khusus' => 'nullable|integer|min:0',
            
            // Harga BPJS
            'harga_bpjs' => 'nullable|integer|min:0',
            'embalase_bpjs' => 'nullable|integer|min:0',
            'jasa_racik_bpjs' => 'nullable|integer|min:0',
            
            'total' => 'nullable|integer|min:0',
            
            // Harga Asuransi
            'harga_asuransi' => 'nullable|array',
            'harga_asuransi.*.id' => 'nullable|uuid',
            'harga_asuransi.*.asuransi_id' => 'required|uuid|exists:asuransis,id',
            'harga_asuransi.*.harga' => 'required|integer|min:0',
            'harga_asuransi.*.tanggal_mulai' => 'nullable|date',
            'harga_asuransi.*.tanggal_selesai' => 'nullable|date|after_or_equal:harga_asuransi.*.tanggal_mulai',
            'harga_asuransi.*.aktif' => 'nullable|boolean',
            'harga_asuransi.*.keterangan' => 'nullable|string',
        ]);

        // Ambil nilai untuk perhitungan total
        // UMUM
        $hargaObat = $validated['harga_obat'] ?? 0;
        $embalase = $validated['embalase'] ?? 0;
        $jasaRacik = $validated['jasa_racik'] ?? 0;
        
        // KHUSUS/PROMO
        $hargaKhusus = $validated['harga_khusus'] ?? 0;
        $embalaseKhusus = $validated['embalase_khusus'] ?? 0;
        $jasaRacikKhusus = $validated['jasa_racik_khusus'] ?? 0;
        
        // BPJS
        $hargaBpjs = $validated['harga_bpjs'] ?? 0;
        $embalaseBpjs = $validated['embalase_bpjs'] ?? 0;
        $jasaRacikBpjs = $validated['jasa_racik_bpjs'] ?? 0;

        // Hitung total untuk masing-masing kategori
        $total = $hargaObat + $embalase + $jasaRacik;
        $totalKhusus = $hargaKhusus + $embalaseKhusus + $jasaRacikKhusus;
        $totalBpjs = $hargaBpjs + $embalaseBpjs + $jasaRacikBpjs;

        DB::beginTransaction();

        try {
            // 1. Update Detail Obat
            $detail = DetailobatRs::where('id_obat_rs', $obatId)
                ->findOrFail($detailId);

            $detail->update([
                'kode_obat_rs' => $validated['kode_obat_rs'],
                'nama_obat_rs' => $validated['nama_obat_rs'],
                'stok_minimal' => $validated['stok_minimal'] ?? 0,
                'stok_maksimal' => $validated['stok_maksimal'] ?? 0,
                'lokasi_penyimpanan' => $validated['lokasi_penyimpanan'],
                'catatan_khusus' => $validated['catatan_khusus'],
                'status_aktif' => $validated['status_aktif'],
                // 'updated_by' => auth()->user()->id_karyawan ?? null,
            ]);

            // 2. Update/Create Harga Obat
            HargaObat::updateOrCreate(
                ['id_detail_obat_rs' => $detailId, 'aktif' => true],
                [
                    // Harga Umum
                    'harga_obat' => $hargaObat,
                    'embalase' => $embalase,
                    'jasa_racik' => $jasaRacik,
                    'total' => $total,
                    
                    // Harga Khusus/Promo
                    'harga_khusus' => $hargaKhusus,
                    'embalase_khusus' => $embalaseKhusus,
                    'jasa_racik_khusus' => $jasaRacikKhusus,
                    'total_khusus' => $totalKhusus,
                    
                    // Harga BPJS
                    'harga_bpjs' => $hargaBpjs,
                    'embalase_bpjs' => $embalaseBpjs,
                    'jasa_racik_bpjs' => $jasaRacikBpjs,
                    'total_bpjs' => $totalBpjs,
                ]
            );

            Log::info('Harga masuk:', [
                'umum' => [
                    'harga' => $hargaObat,
                    'embalase' => $embalase,
                    'jasa_racik' => $jasaRacik,
                    'total' => $total
                ],
                'khusus' => [
                    'harga' => $hargaKhusus,
                    'embalase' => $embalaseKhusus,
                    'jasa_racik' => $jasaRacikKhusus,
                    'total' => $totalKhusus
                ],
                'bpjs' => [
                    'harga' => $hargaBpjs,
                    'embalase' => $embalaseBpjs,
                    'jasa_racik' => $jasaRacikBpjs,
                    'total' => $totalBpjs
                ]
            ]);

            // 3. Update/Create Harga Asuransi
            if (!empty($validated['harga_asuransi'])) {
                foreach ($validated['harga_asuransi'] as $ha) {
                    if (!empty($ha['id'])) {
                        // Update existing
                        HargaObatAsuransi::where('id', $ha['id'])->update([
                            'asuransi_id' => $ha['asuransi_id'],
                            'harga' => $ha['harga'],
                            'tanggal_mulai' => $ha['tanggal_mulai'] ?? null,
                            'tanggal_selesai' => $ha['tanggal_selesai'] ?? null,
                            'aktif' => $ha['aktif'] ?? true,
                            'keterangan' => $ha['keterangan'] ?? null,
                        ]);
                    } else {
                        // Create new
                        HargaObatAsuransi::create([
                            'id' => Str::uuid(),
                            'id_detail_obat_rs' => $detailId,
                            'asuransi_id' => $ha['asuransi_id'],
                            'harga' => $ha['harga'],
                            'tanggal_mulai' => $ha['tanggal_mulai'] ?? null,
                            'tanggal_selesai' => $ha['tanggal_selesai'] ?? null,
                            'aktif' => $ha['aktif'] ?? true,
                            'keterangan' => $ha['keterangan'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => [
                    'total_umum' => $total,
                    'total_khusus' => $totalKhusus,
                    'total_bpjs' => $totalBpjs
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating obat: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus harga asuransi via AJAX
     */
    public function destroyHargaAsuransi($obatId, $detailId, $id)
    {
        try {
            $hargaAsuransi = HargaObatAsuransi::where('id_detail_obat_rs', $detailId)
                ->findOrFail($id);

            $hargaAsuransi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Harga asuransi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus harga asuransi'
            ], 500);
        }
    }

    /**
     * Sync data KFA dari API Satu Sehat
     */
    public function updateKFAMapping(Request $request, $obatId, $detailId)
    {
        Log::info('UpdateKFAMapping called', [
            'obatId' => $obatId,
            'detailId' => $detailId,
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'id_obat_master' => 'required|exists:obat_masters,id_obat_master',
        ]);

        DB::beginTransaction();

        try {
            $detail = DetailobatRs::where('id_obat_rs', $obatId)
                ->findOrFail($detailId);

            $oldKfaId = $detail->id_obat_master;

            // Update pemetaan
            $detail->update([
                'id_obat_master' => $validated['id_obat_master'],
                // 'updated_by' => auth()->user()->id_karyawan ?? null,
            ]);

            // Simpan history pemetaan
            KFAMappingHistory::create([
                'id' => (string) Str::uuid(),
                'id_detail_obat_rs' => $detailId,
                'id_obat_master_old' => $oldKfaId,
                'id_obat_master_new' => $validated['id_obat_master'],
                // 'changed_by' => auth()->user()->id_karyawan ?? null,
                'keterangan' => 'Pemetaan KFA diubah melalui form detail obat'
            ]);

            DB::commit();

            Log::info('KFA mapping updated successfully');

            return response()->json([
                'success' => true,
                'message' => 'Pemetaan KFA berhasil diubah'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating KFA mapping: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah pemetaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync data KFA dari API Satu Sehat
     */
    public function syncKFA($obatId, $detailId, $idObatMaster)
    {
        Log::info('SyncKFA called', [
            'obatId' => $obatId,
            'detailId' => $detailId,
            'idObatMaster' => $idObatMaster
        ]);

        try {
            $obatMaster = ObatMaster::findOrFail($idObatMaster);

            // TODO: Implementasi sync dari API Satu Sehat
            // Contoh implementasi:
            // $apiService = new SatuSehatService();
            // $apiData = $apiService->getObatByKFA($obatMaster->kfa_code);
            // 
            // if ($apiData) {
            //     $obatMaster->update([
            //         'nama_obat' => $apiData['nama_obat'] ?? $obatMaster->nama_obat,
            //         'nama_generik' => $apiData['nama_generik'] ?? $obatMaster->nama_generik,
            //         'bentuk_sediaan' => $apiData['bentuk_sediaan'] ?? $obatMaster->bentuk_sediaan,
            //         'kekuatan' => $apiData['kekuatan'] ?? $obatMaster->kekuatan,
            //         'manufacturer' => $apiData['manufacturer'] ?? $obatMaster->manufacturer,
            //         'data_api' => json_encode($apiData),
            //         'last_sync' => now(),
            //     ]);
            // }

            // Untuk saat ini, simulasi update last_sync
            $obatMaster->update([
                'last_sync' => now(),
            ]);

            Log::info('KFA sync completed successfully');

            return response()->json([
                'success' => true,
                'message' => 'Data KFA berhasil disinkronkan',
                'last_sync' => $obatMaster->last_sync->format('d M Y H:i:s')
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing KFA: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal sinkronisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get history pemetaan KFA
     */
    public function getKFAHistory($obatId, $detailId)
    {
        Log::info('GetKFAHistory called', [
            'obatId' => $obatId,
            'detailId' => $detailId
        ]);

        try {
            $history = KFAMappingHistory::with(['obatMasterOld', 'obatMasterNew', 'user'])
                ->where('id_detail_obat_rs', $detailId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'created_at' => $item->created_at->format('d M Y H:i'),
                        'kfa_code' => $item->obatMasterNew->kfa_code ?? '-',
                        'nama_obat' => $item->obatMasterNew->nama_obat ?? '-',
                        'user_name' => $item->user->nama ?? 'System',
                    ];
                });

            Log::info('KFA history loaded', ['count' => $history->count()]);

            return response()->json([
                'success' => true,
                'data' => $history
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading KFA history: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat history',
                'data' => []
            ], 500);
        }
    }
}
