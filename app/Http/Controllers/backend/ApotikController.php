<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailResep;
use App\Models\DetailstockApotik;
use App\Models\DetailSupplier;
use App\Models\Pasien;
use App\Models\Resep;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApotikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pasien::with(['asuransi']);

        // Filter berdasarkan tanggal
        $filterDate = $request->get('date', date('Y-m-d'));
        $query->whereDate('tanggal', $filterDate);

        // Filter berdasarkan status (jika diperlukan relasi dengan resep)
        if ($request->filled('status')) {
            $query->whereHas('reseps', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_rm', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Pagination
        $pasiens = $query->orderBy('created_at', 'desc')->paginate(25);

        // Statistik untuk cards
        $today = Carbon::today();
        $totalPasienHariIni = Pasien::whereDate('tanggal', $today)->count();

        // Jika ada relasi dengan resep
        $resepSelesai = Resep::whereDate('created_at', $today)
            ->where('status', 'selesai')
            ->count();

        $resepMenunggu = Resep::whereDate('created_at', $today)
            ->where('status', 'menunggu')
            ->count();

        $resepLuar = Resep::whereDate('created_at', $today)
            ->where('jenis_resep', 'resep_luar')
            ->count();

        return view('apotik.index', compact(
            'pasiens',
            'totalPasienHariIni',
            'resepSelesai',
            'resepMenunggu',
            'resepLuar'
        ));
    }

    /**
     * Get stock obat untuk dropdown
     */
    public function getStockObat()
    {
        try {
            $stockObat = DetailstockApotik::with(['detailSupplier.hargaObat'])
                ->select(
                    'id',
                    'detail_obat_rs_id',
                    'no_batch',
                    'stock_apotik',
                    'retur',
                    DB::raw('(stock_apotik - retur) as stock_tersedia')
                )
                ->whereRaw('(stock_apotik - retur) > 0')
                ->get()
                ->map(function ($item) {
                    return [
                        'id'           => $item->id,
                        'obat_id'      => $item->detail_obat_rs_id,
                        'nama'         => $item->detailSupplier?->nama ?? '-',
                        'judul'        => $item->detailSupplier?->judul ?? '-',
                        'jenis'        => $item->detailSupplier?->jenis ?? '-',
                        'merk'         => $item->detailSupplier?->merk ?? '-',
                        'satuan'       => $item->detailSupplier?->satuan ?? '-',
                        'no_batch'     => $item->no_batch,
                        'stock'        => $item->stock_tersedia,
                        'harga_obat'   => $item->detailSupplier?->hargaObat?->harga_obat ?? 0,
                        'harga_khusus' => $item->detailSupplier?->hargaObat?->harga_khusus ?? 0,
                        'harga_bpjs'   => $item->detailSupplier?->hargaObat?->harga_bpjs ?? 0,
                    ];
                });

            return response()->json([
                'success' => true,
                'data'    => $stockObat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Get data Pasien
    public function getPasienById($id)
    {
        try {
            $pasien = Pasien::with(['asuransi'])
                ->where('id_pasien', $id)
                ->select(
                    'id_pasien',
                    'no_rm',
                    'nama_lengkap',
                    'nik',
                    'jenis_kelamin',
                    'tanggal_lahir',
                    'jenis_pembayaran',
                    'no_bpjs',
                    'asuransi_id',
                    'alamat',
                    'no_telp'
                )
                ->first();

            if (!$pasien) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pasien tidak ditemukan'
                ], 404);
            }
            $data = [
                'id' => $pasien->id_pasien,
                'no_rm' => $pasien->no_rm,
                'nama_lengkap' => $pasien->nama_lengkap,
                'nik' => $pasien->nik,
                'jenis_kelamin' => $pasien->jenis_kelamin,
                'umur' => $pasien->tanggal_lahir ? \Carbon\Carbon::parse($pasien->tanggal_lahir)->age : null,
                'tanggal_lahir' => $pasien->tanggal_lahir,
                'jenis_pembayaran' => $pasien->jenis_pembayaran,
                'no_bpjs' => $pasien->no_bpjs,
                'asuransi' => $pasien->asuransi ? [
                    'id' => $pasien->asuransi->id,
                    'nama' => $pasien->asuransi->nama_asuransi
                ] : null,
                'alamat' => $pasien->alamat,
                'no_telp' => $pasien->no_telp,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pasien: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store resep (untuk pasien terdaftar)
     */
    public function storeResep(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pasien_id' => 'required|exists:pasiens,id_pasien',
            'status_obat' => 'required|in:Racik,Non Racik',
            'obat' => 'required|array|min:1',
            'obat.*.detail_supplier_id' => 'required',
            'obat.*.jumlah' => 'required|numeric|min:1',
            'obat.*.harga' => 'required|numeric|min:0',
            'obat.*.subtotal' => 'required|numeric|min:0',
        ], [
            'pasien_id.required' => 'Data pasien tidak ditemukan',
            'obat.required' => 'Minimal harus ada 1 obat',
            'obat.*.detail_supplier_id.required' => 'Obat harus dipilih',
            'obat.*.jumlah.required' => 'Jumlah obat harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Generate nomor resep
            $lastResep = Resep::whereDate('created_at', Carbon::today())
                ->orderBy('id', 'desc')
                ->first();

            $counter = $lastResep ? (intval(substr($lastResep->no_resep, -4)) + 1) : 1;
            $noResep = 'RSP/' . date('Ymd') . '/' . str_pad($counter, 4, '0', STR_PAD_LEFT);

            // Hitung total dari request (sudah dihitung di frontend)
            $totalObat = collect($request->obat)->sum('subtotal');
            $embalase = $request->embalase ?? 0;
            $jasaRacik = $request->jasa_racik ?? 0;
            $totalBayar = $totalObat + $embalase + $jasaRacik;

            // Simpan resep
            $resep = Resep::create([
                'no_resep' => $noResep,
                'pasien_id' => $request->pasien_id,
                'jenis_resep' => 'resep',
                'status_obat' => $request->status_obat,
                'jenis_racikan' => $request->jenis_racikan,
                'dosis_signa' => $request->dosis_signa,
                'hasil_racikan' => $request->hasil_racikan,
                'aturan_pakai' => $request->aturan_pakai,
                'embalase' => $embalase,
                'jasa_racik' => $jasaRacik,
                'total_harga' => $totalBayar,
                'keterangan' => $request->keterangan,
                'status' => 'menunggu',
                'tanggal_resep' => Carbon::now(),
                'user_id' => auth()->id(),
            ]);

            // Simpan detail resep dan kurangi stock
            foreach ($request->obat as $obatData) {
                // Ambil data dari DetailstockApotik (bukan DetailSupplier)
                $detailStock = DetailstockApotik::find($obatData['detail_supplier_id']);

                if (!$detailStock) {
                    throw new \Exception("Data stock obat tidak ditemukan");
                }

                // Validasi stock tersedia
                $stockTersedia = $detailStock->stock_apotik - $detailStock->retur;
                if ($stockTersedia < $obatData['jumlah']) {
                    throw new \Exception("Stock obat tidak mencukupi. Stock tersedia: {$stockTersedia}");
                }

                // Simpan detail resep
                DetailResep::create([
                    'resep_id' => $resep->id,
                    'detail_supplier_id' => $detailStock->detailSupplier->id, // ID dari detail_suppliers
                    'detail_obat_rs_id' => $detailStock->detail_obat_rs_id,
                    'jumlah' => $obatData['jumlah'],
                    'harga_satuan' => $obatData['harga'],
                    'subtotal' => $obatData['subtotal'],
                ]);

                // Kurangi stock di detail_stock_apotiks
                $detailStock->decrement('stock_apotik', $obatData['jumlah']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Resep berhasil disimpan dengan nomor: {$noResep}",
                'data' => [
                    'no_resep' => $noResep,
                    'resep_id' => $resep->id
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan resep: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store resep luar (tanpa pasien terdaftar)
     */
    public function storeResepLuar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status_obat_luar' => 'required|in:Racik,Non Racik',
            'obat_luar' => 'required|array|min:1',
            'obat_luar.*.detail_supplier_id' => 'required',
            'obat_luar.*.jumlah' => 'required|numeric|min:1',
            'obat_luar.*.harga' => 'required|numeric|min:0',
            'obat_luar.*.subtotal' => 'required|numeric|min:0',
        ], [
            'status_obat_luar.required' => 'Status obat harus dipilih',
            'obat_luar.required' => 'Minimal harus ada 1 obat',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Generate nomor resep luar
            $lastResep = Resep::where('jenis_resep', 'resep_luar')
                ->whereDate('created_at', Carbon::today())
                ->orderBy('id', 'desc')
                ->first();

            $counter = $lastResep ? (intval(substr($lastResep->no_resep, -4)) + 1) : 1;
            $noResep = 'RSPL/' . date('Ymd') . '/' . str_pad($counter, 4, '0', STR_PAD_LEFT);

            // Hitung total dari request
            $totalObat = collect($request->obat_luar)->sum('subtotal');
            $embalase = $request->embalase_luar ?? 0;
            $jasaRacik = $request->jasa_racik_luar ?? 0;
            $totalBayar = $totalObat + $embalase + $jasaRacik;

            // Simpan resep luar
            $resep = Resep::create([
                'no_resep' => $noResep,
                'pasien_id' => null,
                'jenis_resep' => 'resep_luar',
                'nama_pasien_luar' => $request->nama_pasien_luar,
                'umur' => $request->umur,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat_luar' => $request->alamat,
                'dokter_resep' => $request->dokter_resep,
                'status_obat' => $request->status_obat_luar,
                'jenis_racikan' => $request->jenis_racikan_luar,
                'dosis_signa' => $request->dosis_signa_luar,
                'hasil_racikan' => $request->hasil_racikan_luar,
                'aturan_pakai' => $request->aturan_pakai_luar,
                'embalase' => $embalase,
                'jasa_racik' => $jasaRacik,
                'total_harga' => $totalBayar,
                'keterangan' => $request->keterangan_luar,
                'status' => 'selesai', // Resep luar langsung selesai
                'tanggal_resep' => Carbon::now(),
                'user_id' => auth()->id(),
            ]);

            // Simpan detail resep dan kurangi stock
            foreach ($request->obat_luar as $obatData) {
                // Ambil data dari DetailstockApotik
                $detailStock = DetailstockApotik::find($obatData['detail_supplier_id']);

                if (!$detailStock) {
                    throw new \Exception("Data stock obat tidak ditemukan");
                }

                // Validasi stock tersedia
                $stockTersedia = $detailStock->stock_apotik - $detailStock->retur;
                if ($stockTersedia < $obatData['jumlah']) {
                    throw new \Exception("Stock obat tidak mencukupi. Stock tersedia: {$stockTersedia}");
                }

                // Simpan detail resep
                DetailResep::create([
                    'resep_id' => $resep->id,
                    'detail_supplier_id' => $detailStock->detailSupplier->id,
                    'detail_obat_rs_id' => $detailStock->detail_obat_rs_id,
                    'jumlah' => $obatData['jumlah'],
                    'harga_satuan' => $obatData['harga'],
                    'subtotal' => $obatData['subtotal'],
                ]);

                // Kurangi stock di detail_stock_apotiks
                $detailStock->decrement('stock_apotik', $obatData['jumlah']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Resep luar berhasil disimpan dengan nomor: {$noResep}",
                'data' => [
                    'no_resep' => $noResep,
                    'resep_id' => $resep->id
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan resep luar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export data to Excel (placeholder)
     */
    public function export(Request $request)
    {
        return redirect()->back()
            ->with('info', 'Fitur export akan segera tersedia');
    }
}
