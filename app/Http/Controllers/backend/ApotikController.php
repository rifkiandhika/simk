<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Apotik;
use App\Models\DetailResep;
use App\Models\DetailstockApotik;
use App\Models\DetailSupplier;
use App\Models\Pasien;
use App\Models\Resep;
use App\Models\Tagihan;
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
        $query = Resep::with(['pasien.asuransi', 'details.detailSupplier']);

        // Filter berdasarkan tanggal
        $filterDate = $request->get('date', date('Y-m-d'));
        $query->whereDate('tanggal_resep', $filterDate);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default tampilkan yang menunggu dan proses
            $query->whereIn('status', ['menunggu', 'proses']);
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_resep', 'like', "%{$search}%")
                    ->orWhereHas('pasien', function ($q2) use ($search) {
                        $q2->where('no_rm', 'like', "%{$search}%")
                            ->orWhere('nama_lengkap', 'like', "%{$search}%");
                    });
            });
        }

        $reseps = $query->orderBy('tanggal_resep', 'asc')->paginate(25);

        // Statistik
        $today = Carbon::today();
        $totalResepHariIni = Resep::whereDate('tanggal_resep', $today)
            ->where('jenis_resep', 'resep')
            ->count();

        $resepMenunggu = Resep::whereDate('tanggal_resep', $today)
            ->where('status', 'menunggu')
            ->count();

        $resepProses = Resep::whereDate('tanggal_resep', $today)
            ->where('status', 'proses')
            ->count();

        $resepSelesai = Resep::whereDate('tanggal_resep', $today)
            ->where('status', 'selesai')
            ->count();

        return view('apotik.index', compact(
            'reseps',
            'totalResepHariIni',
            'resepMenunggu',
            'resepProses',
            'resepSelesai'
        ));
    }

    /**
     * Get stock obat untuk dropdown
     */
    public function getResepDetail($resepId)
    {
        try {
            $resep = Resep::with([
                'pasien',
                'details.detailSupplier.hargaObat',
                'verifiedBy',
                'dispensedBy',
                'tagihan' // TAMBAHKAN INI
            ])->findOrFail($resepId);

            // Check stock availability untuk setiap obat
            $obatDetails = $resep->details->map(function ($detail) {
                $stockTersedia = DetailstockApotik::where('detail_obat_rs_id', $detail->detail_obat_rs_id)
                    ->selectRaw('SUM(stock_apotik - retur) as total_stock')
                    ->first()
                    ->total_stock ?? 0;

                return [
                    'id' => $detail->id,
                    'nama_obat' => $detail->detailSupplier->nama,
                    'judul' => $detail->detailSupplier->judul,
                    'satuan' => $detail->detailSupplier->satuan,
                    'jumlah_diminta' => $detail->jumlah,
                    'stock_tersedia' => $stockTersedia,
                    'harga_satuan' => $detail->harga_satuan,
                    'subtotal' => $detail->subtotal,
                    'stock_cukup' => $stockTersedia >= $detail->jumlah,
                ];
            });

            // TAMBAHKAN: Cek status pembayaran
            $statusPembayaran = [
                'boleh_serahkan' => false,
                'status' => 'BELUM_LUNAS',
                'keterangan' => 'Tagihan belum lunas',
                'total_tagihan' => 0,
                'total_dibayar' => 0,
                'sisa_tagihan' => 0
            ];

            if ($resep->tagihan) {
                $statusPembayaran = [
                    'boleh_serahkan' => in_array($resep->tagihan->status, ['LUNAS', 'CICILAN']),
                    'status' => $resep->tagihan->status,
                    'keterangan' => $this->getKeteranganPembayaran($resep->tagihan->status),
                    'total_tagihan' => $resep->tagihan->total_tagihan,
                    'total_dibayar' => $resep->tagihan->total_dibayar,
                    'sisa_tagihan' => $resep->tagihan->sisa_tagihan
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'resep' => $resep,
                    'obat_details' => $obatDetails,
                    'all_stock_available' => $obatDetails->every(fn($item) => $item['stock_cukup']),
                    'pembayaran' => $statusPembayaran // TAMBAHKAN INI
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getKeteranganPembayaran($status)
    {
        return match($status) {
            'LUNAS' => 'Pembayaran sudah lunas, obat dapat diserahkan',
            'CICILAN' => 'Pembayaran cicilan, obat dapat diserahkan',
            'BELUM_LUNAS' => 'Pembayaran belum lunas, obat tidak dapat diserahkan',
            default => 'Status pembayaran tidak diketahui'
        };
    }

    public function verifikasi($resepId)
    {
        DB::beginTransaction();
        try {
            $resep = Resep::findOrFail($resepId);

            if ($resep->status !== 'menunggu') {
                throw new \Exception('Resep ini sudah diverifikasi atau diselesaikan');
            }

            // Check stock semua obat
            foreach ($resep->details as $detail) {
                $stockTersedia = DetailstockApotik::where('detail_obat_rs_id', $detail->detail_obat_rs_id)
                    ->selectRaw('SUM(stock_apotik - retur) as total_stock')
                    ->first()
                    ->total_stock ?? 0;

                if ($stockTersedia < $detail->jumlah) {
                    throw new \Exception("Stock obat {$detail->detailSupplier->nama} tidak mencukupi");
                }
            }

            // Update status resep
            $resep->update([
                'status' => 'proses',
                'verified_by' => auth()->id(),
                'verified_at' => Carbon::now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Resep berhasil diverifikasi. Silakan lakukan penyerahan obat.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

   public function serahkan($resepId)
    {
        DB::beginTransaction();
        try {
            $resep = Resep::with(['pasien', 'details.detailSupplier'])->findOrFail($resepId);

            // ========== VALIDASI 1: STATUS RESEP ==========
            if ($resep->status !== 'proses') {
                throw new \Exception('Resep harus diverifikasi terlebih dahulu');
            }

            // ========== VALIDASI 2: CARI TAGIHAN YANG TEPAT ==========
            $tagihan = $this->findTagihanForResep($resep);

            if (!$tagihan) {
                throw new \Exception(
                    'Data tagihan untuk resep ini tidak ditemukan. ' .
                    'No Resep: ' . $resep->no_resep . '. ' .
                    'Silakan hubungi bagian kasir untuk memastikan tagihan sudah dibuat.'
                );
            }

            // ========== VALIDASI 3: STATUS PEMBAYARAN ==========
            // HANYA LUNAS dan CICILAN yang boleh
            if (!in_array($tagihan->status, ['LUNAS', 'CICILAN'])) {
                throw new \Exception(
                    "Obat tidak dapat diserahkan karena pembayaran belum lunas.\n\n" .
                    "Detail Tagihan:\n" .
                    "• No Tagihan: {$tagihan->no_tagihan}\n" .
                    "• Total Tagihan: Rp " . number_format($tagihan->total_tagihan, 0, ',', '.') . "\n" .
                    "• Sudah Dibayar: Rp " . number_format($tagihan->total_dibayar, 0, ',', '.') . "\n" .
                    "• Sisa Tagihan: Rp " . number_format($tagihan->sisa_tagihan, 0, ',', '.') . "\n\n" .
                    "Silakan selesaikan pembayaran di kasir terlebih dahulu."
                );
            }

            // ========== VALIDASI 4: STOCK OBAT ==========
            foreach ($resep->details as $detail) {
                $jumlahSisa = $detail->jumlah;

                // Ambil stock batches yang tersedia
                $stockBatches = DetailstockApotik::where('detail_obat_rs_id', $detail->detail_obat_rs_id)
                    ->whereRaw('(stock_apotik - retur) > 0')
                    ->orderBy('created_at', 'asc') // FEFO/FIFO
                    ->get();

                // Hitung total stock tersedia
                $totalStockTersedia = $stockBatches->sum(function($batch) {
                    return $batch->stock_apotik - $batch->retur;
                });

                // Validasi stock mencukupi
                if ($totalStockTersedia < $detail->jumlah) {
                    throw new \Exception(
                        "Stock obat '{$detail->detailSupplier->nama}' tidak mencukupi. " .
                        "Dibutuhkan: {$detail->jumlah}, Tersedia: {$totalStockTersedia}"
                    );
                }

                // Kurangi stock dari batch tertua (FEFO/FIFO)
                foreach ($stockBatches as $batch) {
                    if ($jumlahSisa <= 0) break;

                    $stockTersedia = $batch->stock_apotik - $batch->retur;
                    $jumlahAmbil = min($jumlahSisa, $stockTersedia);

                    $batch->decrement('stock_apotik', $jumlahAmbil);
                    $jumlahSisa -= $jumlahAmbil;
                }

                // Pastikan semua stock terambil
                if ($jumlahSisa > 0) {
                    throw new \Exception(
                        "Gagal mengurangi stock obat '{$detail->detailSupplier->nama}'. " .
                        "Sisa yang belum terambil: {$jumlahSisa}"
                    );
                }
            }

            // ========== UPDATE STATUS RESEP ==========
            $resep->update([
                'status' => 'selesai',
                'dispensed_by' => auth()->id(),
                'dispensed_at' => Carbon::now()
            ]);

            // ========== UPDATE STATUS DI TABEL APOTIKS (jika ada) ==========
            Apotik::where('pasien_id', $resep->pasien_id)
                ->whereDate('created_at', Carbon::today())
                ->update(['status' => 'selesai']);

            DB::commit();

            // ========== RESPONSE MESSAGE ==========
            $message = 'Obat berhasil diserahkan kepada pasien. Stock telah dikurangi.';
            
            if ($tagihan->status === 'CICILAN') {
                $message .= "\n\nInformasi Pembayaran:" .
                        "\n• Status: CICILAN" .
                        "\n• No Tagihan: {$tagihan->no_tagihan}" .
                        "\n• Sisa Tagihan: Rp " . number_format($tagihan->sisa_tagihan, 0, ',', '.');
            } else {
                $message .= "\n\nStatus Pembayaran: LUNAS";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'resep_id' => $resep->id,
                    'no_resep' => $resep->no_resep,
                    'status_pembayaran' => $tagihan->status,
                    'no_tagihan' => $tagihan->no_tagihan,
                    'sisa_tagihan' => $tagihan->sisa_tagihan
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log error untuk debugging
            \Log::error('Error saat menyerahkan obat', [
                'resep_id' => $resepId,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method untuk mencari tagihan yang tepat untuk resep
     * Menggunakan algoritma multi-level priority
     */
    private function findTagihanForResep($resep)
    {
        $tagihan = null;
        
        // ========== PRIORITAS 1: MATCH BY resep_id ==========
        // Jika kolom resep_id sudah ada di tabel tagihans
        $tagihan = Tagihan::where('resep_id', $resep->id)->first();
        
        if ($tagihan) {
            \Log::info('Tagihan ditemukan via resep_id', [
                'resep_id' => $resep->id,
                'tagihan_id' => $tagihan->id_tagihan
            ]);
            return $tagihan;
        }
        
        // ========== PRIORITAS 2: MATCH BY TOTAL HARGA + WAKTU TERDEKAT ==========
        // Cari tagihan dengan total yang sama dan waktu yang paling dekat
        $tagihan = Tagihan::where('id_pasien', $resep->pasien_id)
            ->whereDate('tanggal_tagihan', Carbon::parse($resep->tanggal_resep)->toDateString())
            ->where('total_tagihan', $resep->total_harga)
            ->where(function($query) use ($resep) {
                // Cari yang dibuat dalam rentang waktu ±60 menit dari resep
                $resepTime = Carbon::parse($resep->tanggal_resep);
                $query->whereBetween('created_at', [
                    $resepTime->copy()->subMinutes(60),
                    $resepTime->copy()->addMinutes(60)
                ]);
            })
            // Order by waktu yang paling dekat
            ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, created_at, ?))', [Carbon::parse($resep->tanggal_resep)])
            ->first();
        
        if ($tagihan) {
            \Log::info('Tagihan ditemukan via total_harga + waktu', [
                'resep_id' => $resep->id,
                'tagihan_id' => $tagihan->id_tagihan,
                'total_match' => $tagihan->total_tagihan
            ]);
            return $tagihan;
        }
        
        // ========== PRIORITAS 3: MATCH BY REGISTRASI ID ==========
        // Jika ada registrasi_id di resep
        if (isset($resep->registrasi_id) && $resep->registrasi_id) {
            $tagihan = Tagihan::where('id_registrasi', $resep->registrasi_id)
                ->where('id_pasien', $resep->pasien_id)
                ->whereDate('tanggal_tagihan', Carbon::parse($resep->tanggal_resep)->toDateString())
                ->first();
            
            if ($tagihan) {
                \Log::info('Tagihan ditemukan via registrasi_id', [
                    'resep_id' => $resep->id,
                    'tagihan_id' => $tagihan->id_tagihan,
                    'registrasi_id' => $resep->registrasi_id
                ]);
                return $tagihan;
            }
        }
        
        // ========== PRIORITAS 4: MATCH BY EXACT AMOUNT + STATUS BELUM SELESAI ==========
        // Cari tagihan yang belum terhubung dengan resep lain
        $tagihan = Tagihan::where('id_pasien', $resep->pasien_id)
            ->whereDate('tanggal_tagihan', Carbon::parse($resep->tanggal_resep)->toDateString())
            ->where('total_tagihan', $resep->total_harga)
            ->whereDoesntHave('resep') // Jika ada relasi di Model Tagihan
            ->first();
        
        if ($tagihan) {
            \Log::info('Tagihan ditemukan via exact amount + unused', [
                'resep_id' => $resep->id,
                'tagihan_id' => $tagihan->id_tagihan
            ]);
            return $tagihan;
        }
        
        // ========== LOG JIKA TIDAK DITEMUKAN ==========
        \Log::warning('Tagihan tidak ditemukan untuk resep', [
            'resep_id' => $resep->id,
            'no_resep' => $resep->no_resep,
            'pasien_id' => $resep->pasien_id,
            'tanggal_resep' => $resep->tanggal_resep,
            'total_harga' => $resep->total_harga
        ]);
        
        return null;
    }

    public function tolak(Request $request, $resepId)
    {
        DB::beginTransaction();
        try {
            $resep = Resep::findOrFail($resepId);

            if (!in_array($resep->status, ['menunggu', 'proses'])) {
                throw new \Exception('Resep ini sudah diselesaikan dan tidak bisa ditolak');
            }

            $resep->update([
                'status' => 'batal',
                'rejection_reason' => $request->rejection_reason ?? 'Stock obat tidak mencukupi',
                'verified_by' => auth()->id(),
                'verified_at' => Carbon::now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Resep berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function print($resepId)
    {
        $resep = Resep::with([
            'pasien',
            'details.detailSupplier',
            'dispensedBy'
        ])->findOrFail($resepId);

        if ($resep->status !== 'selesai') {
            return redirect()->back()->with('error', 'Hanya resep yang sudah diserahkan yang bisa dicetak');
        }

        // Bisa pakai view biasa atau PDF
        return view('apotik.print', compact('resep'));

        // Atau jika pakai PDF:
        // $pdf = PDF::loadView('apotik.print', compact('resep'));
        // return $pdf->download("bukti-resep-{$resep->no_resep}.pdf");
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
