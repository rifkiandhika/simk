<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Apotik;
use App\Models\Asuransi;
use App\Models\DetailResep;
use App\Models\DetailstockApotik;
use App\Models\Dosis;
use App\Models\Pasien;
use App\Models\Resep;
use App\Models\Ruangan;
use App\Models\Signa;
use App\Services\TagihanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasienController extends Controller
{
    protected $tagihanService; 

    public function __construct(TagihanService $tagihanService)
    {
        $this->tagihanService = $tagihanService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pasien::with(['asuransi', 'ruangan']);

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_rm', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('no_telp', 'like', "%{$search}%");
            });
        }

        // Filter jenis pembayaran
        if ($request->filled('jenis_pembayaran')) {
            $query->where('jenis_pembayaran', $request->jenis_pembayaran);
        }

        // Filter status
        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->status_aktif);
        }

        // Filter jenis ruangan
        if ($request->filled('jenis_ruangan')) {
            $query->where('jenis_ruangan', $request->jenis_ruangan);
        }

        // Filter ruangan
        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        $pasiens = $query->orderBy('created_at', 'desc')->paginate(15);

        // Data untuk filter dropdown
        $ruangans = Ruangan::where('status', 1)
                          ->orderBy('jenis', 'asc')
                          ->orderBy('kode_ruangan', 'asc')
                          ->get();

        return view('pasien.index', compact('pasiens', 'ruangans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $asuransis = Asuransi::where('status', 'Aktif')->get();
        
        // Ambil ruangan yang aktif
        $ruangans = Ruangan::where('status', 1)
                          ->orderBy('jenis', 'asc')
                          ->orderBy('kode_ruangan', 'asc')
                          ->get();
        
        return view('pasien.create', compact('asuransis', 'ruangans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_rm' => 'required|string|max:20|unique:pasiens,no_rm',
            'nik' => 'nullable|string|max:16|unique:pasiens,nik',
            'nama_lengkap' => 'required|string|max:100',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'golongan_darah' => 'nullable|in:A,B,AB,O,A+,A-,B+,B-,AB+,AB-,O+,O-',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:15',
            'no_telp_darurat' => 'nullable|string|max:15',
            'nama_kontak_darurat' => 'nullable|string|max:100',
            'hubungan_kontak_darurat' => 'nullable|string|max:50',
            'status_perkawinan' => 'nullable|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pekerjaan' => 'nullable|string|max:100',
            'jenis_ruangan' => 'nullable|in:rawat_jalan,rawat_inap,igd,penunjang',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'jenis_pembayaran' => 'required|in:BPJS,Umum,Asuransi',
            'no_bpjs' => 'nullable|string|max:20',
            'asuransi_id' => 'nullable|exists:asuransis,id',
            'no_polis_asuransi' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
            'status_aktif' => 'required|in:Aktif,Nonaktif',
            'tanggal' => 'nullable|date',
        ], [
            'ruangan_id.exists' => 'Ruangan yang dipilih tidak valid',
        ]);

        // Validasi kapasitas ruangan untuk rawat inap
        if (!empty($validated['ruangan_id'])) {
            $ruangan = Ruangan::find($validated['ruangan_id']);
            
            if ($ruangan && $ruangan->jenis === 'rawat_inap') {
                $pasienAktif = Pasien::where('ruangan_id', $ruangan->id)
                                    ->where('status_aktif', 'Aktif')
                                    ->count();
                
                if ($pasienAktif >= $ruangan->kapasitas) {
                    return back()->withErrors([
                        'ruangan_id' => "Ruangan {$ruangan->nama_ruangan} sudah penuh (kapasitas: {$ruangan->kapasitas})"
                    ])->withInput();
                }
            }
        }

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . Str::slug($request->nama_lengkap) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('pasien/foto', $filename, 'public');
            $validated['foto'] = $path;
        }

        // Set tanggal registrasi
        $validated['tanggal'] = $validated['tanggal'] ?? now()->toDateString();

        Pasien::create($validated);

        return redirect()->route('pasiens.index')
            ->with('success', 'Data pasien berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pasien $pasien)
    {
        $pasien->load(['asuransi', 'ruangan']);
        return view('pasien.show', compact('pasien'));
    }

    public function getPasienAjax(Pasien $pasien)
    {
        return response()->json([
            'success' => true,
            'data' => $pasien->load(['asuransi', 'ruangan.dokters'])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pasien $pasien)
    {
        $asuransis = Asuransi::where('status', 'Aktif')->get();
        
        // Ambil ruangan yang aktif
        $ruangans = Ruangan::where('status', 1)
                          ->orderBy('jenis', 'asc')
                          ->orderBy('kode_ruangan', 'asc')
                          ->get();
        
        return view('pasien.edit', compact('pasien', 'asuransis', 'ruangans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pasien $pasien)
    {
        $validated = $request->validate([
            'no_rm' => 'required|string|max:20|unique:pasiens,no_rm,' . $pasien->id_pasien . ',id_pasien',
            'nik' => 'nullable|string|max:16|unique:pasiens,nik,' . $pasien->id_pasien . ',id_pasien',
            'nama_lengkap' => 'required|string|max:100',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'golongan_darah' => 'nullable|in:A,B,AB,O,A+,A-,B+,B-,AB+,AB-,O+,O-',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:15',
            'no_telp_darurat' => 'nullable|string|max:15',
            'nama_kontak_darurat' => 'nullable|string|max:100',
            'hubungan_kontak_darurat' => 'nullable|string|max:50',
            'status_perkawinan' => 'nullable|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pekerjaan' => 'nullable|string|max:100',
            'jenis_ruangan' => 'nullable|in:rawat_jalan,rawat_inap,igd,penunjang',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'jenis_pembayaran' => 'required|in:BPJS,Umum,Asuransi',
            'no_bpjs' => 'nullable|string|max:20',
            'asuransi_id' => 'nullable|exists:asuransis,id',
            'no_polis_asuransi' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
            'status_aktif' => 'required|in:Aktif,Nonaktif',
            'tanggal' => 'nullable|date',
        ]);

        // Validasi kapasitas ruangan untuk rawat inap (jika ruangan berubah)
        if (!empty($validated['ruangan_id']) && $validated['ruangan_id'] != $pasien->ruangan_id) {
            $ruangan = Ruangan::find($validated['ruangan_id']);
            
            if ($ruangan && $ruangan->jenis === 'rawat_inap') {
                $pasienAktif = Pasien::where('ruangan_id', $ruangan->id)
                                    ->where('status_aktif', 'Aktif')
                                    ->where('id_pasien', '!=', $pasien->id_pasien)
                                    ->count();
                
                if ($pasienAktif >= $ruangan->kapasitas) {
                    return back()->withErrors([
                        'ruangan_id' => "Ruangan {$ruangan->nama_ruangan} sudah penuh (kapasitas: {$ruangan->kapasitas})"
                    ])->withInput();
                }
            }
        }

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($pasien->foto && Storage::disk('public')->exists($pasien->foto)) {
                Storage::disk('public')->delete($pasien->foto);
            }

            $file = $request->file('foto');
            $filename = time() . '_' . Str::slug($request->nama_lengkap) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('pasien/foto', $filename, 'public');
            $validated['foto'] = $path;
        }

        $pasien->update($validated);

        return redirect()->route('pasiens.index')
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pasien $pasien)
    {
        // Hapus foto jika ada
        if ($pasien->foto && Storage::disk('public')->exists($pasien->foto)) {
            Storage::disk('public')->delete($pasien->foto);
        }

        $pasien->delete();

        return redirect()->route('pasiens.index')
            ->with('success', 'Data pasien berhasil dihapus.');
    }

    /**
     * Generate nomor RM otomatis
     */
    public function generateNoRM()
    {
        $lastPasien = Pasien::orderBy('no_rm', 'desc')->first();

        if ($lastPasien) {
            $lastNumber = (int) substr($lastPasien->no_rm, 2);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $noRM = 'RM' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);

        return response()->json(['no_rm' => $noRM]);
    }

    /**
     * Get stock obat untuk dropdown di modal resep
     */
    public function getStockObat()
    {
        try {
            $stockObat = DetailstockApotik::with(['detailSupplier.hargaObat'])
                ->select(
                    'id',
                    'detail_obat_rs_id',
                    'stock_apotik_id',
                    'no_batch',
                    'stock_apotik',
                    'retur',
                    DB::raw('(stock_apotik - retur) as stock_tersedia')
                )
                ->whereRaw('(stock_apotik - retur) > 0')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'detail_obat_rs_id' => $item->detail_obat_rs_id,
                        'stock_apotik_id' => $item->stock_apotik_id,
                        'nama' => $item->detailSupplier?->nama ?? '-',
                        'judul' => $item->detailSupplier?->judul ?? '-',
                        'jenis' => $item->detailSupplier?->jenis ?? '-',
                        'merk' => $item->detailSupplier?->merk ?? '-',
                        'satuan' => $item->detailSupplier?->satuan ?? '-',
                        'no_batch' => $item->no_batch,
                        'stock' => $item->stock_tersedia,
                        'harga_obat' => $item->detailSupplier?->hargaObat?->total ?? 0,
                        'harga_khusus' => $item->detailSupplier?->hargaObat?->total_khusus ?? 0,
                        'harga_bpjs' => $item->detailSupplier?->hargaObat?->total_bpjs ?? 0,
                        'detail_supplier_id' => $item->detailSupplier?->id ?? null,
                    ];
                });
                $dosis = Dosis::all();
                $signa = Signa::all();

            return response()->json([
                'success' => true,
                'data' => $stockObat,
                'dosis' => $dosis,
                'signa' => $signa,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store resep dari halaman pasien
     * 
     */
    public function storeResep(Request $request)
    {
        // UNCOMMENT untuk debugging
        // dd($request->all());
        
        $validator = Validator::make($request->all(), [
            'pasien_id' => 'required|exists:pasiens,id_pasien',
            'obat_non_racik' => 'nullable|array',
            'obat_non_racik.*.id' => 'required_with:obat_non_racik',
            'obat_non_racik.*.jumlah' => 'required_with:obat_non_racik|numeric|min:1',
            'obat_non_racik.*.harga' => 'required_with:obat_non_racik|numeric|min:0',
            'obat_non_racik.*.subtotal' => 'required_with:obat_non_racik|numeric|min:0',
            'obat_non_racik.*.dosis_signa' => 'nullable|string',  
            'obat_non_racik.*.aturan_pakai' => 'nullable|string', 
            'racikan' => 'nullable|array',
            'racikan.*.nama_racikan' => 'required_with:racikan|string',
            'racikan.*.hasil_racikan' => 'required_with:racikan|string',
            'racikan.*.jumlah_racikan' => 'required_with:racikan|numeric|min:1',
            'racikan.*.jasa_racik' => 'required_with:racikan|numeric|min:0',
            'racikan.*.obat' => 'required_with:racikan|array|min:1',
            'racikan.*.obat.*.id' => 'required',
            'racikan.*.obat.*.jumlah' => 'required|numeric|min:1',
            'racikan.*.obat.*.harga' => 'required|numeric|min:0',
            'racikan.*.obat.*.dosis_signa' => 'nullable|string',   
            'racikan.*.obat.*.aturan_pakai' => 'nullable|string',  
            'embalase' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'diskon_type' => 'required|in:percent,idr',
            'pajak' => 'nullable|numeric|min:0',
            'pajak_type' => 'required|in:percent,idr',
            'keterangan' => 'nullable|string',
            'dokter_resep' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        // Validasi minimal harus ada obat
        if (empty($request->obat_non_racik) && empty($request->racikan)) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal harus ada 1 obat atau racikan'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Generate nomor resep
            $lastResep = Resep::whereDate('created_at', Carbon::today())
                ->orderBy('id', 'desc')
                ->first();

            $counter = $lastResep ? (intval(substr($lastResep->no_resep, -4)) + 1) : 1;
            $noResep = 'RSP-' . date('Ymd') . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);

            // Tentukan status_obat berdasarkan ada tidaknya racikan
            $statusObat = !empty($request->racikan) ? 'Racik' : 'Non Racik';

            // Hitung total
            $totalObat = 0;
            $totalJasaRacik = 0;

            
            $dosisSigmaArray = [];
            $aturanPakaiArray = [];

            // Total dari obat non racik
            if (!empty($request->obat_non_racik)) {
                foreach ($request->obat_non_racik as $obat) {
                    $totalObat += $obat['subtotal'];
                    
                    
                    if (!empty($obat['dosis_signa'])) {
                        $dosisSigmaArray[] = $obat['dosis_signa'];
                    }
                    if (!empty($obat['aturan_pakai'])) {
                        $aturanPakaiArray[] = $obat['aturan_pakai'];
                    }
                }
            }

            // Total dari racikan
            $jenisRacikanArray = [];
            $hasilRacikanArray = [];

            if (!empty($request->racikan)) {
                foreach ($request->racikan as $racikan) {
                    $jenisRacikanArray[] = $racikan['nama_racikan'];
                    $hasilRacikanArray[] = $racikan['hasil_racikan'];

                    foreach ($racikan['obat'] as $obat) {
                        $totalObat += ($obat['jumlah'] * $obat['harga']);
                        
                        // ✅ TAMBAH: Kumpulkan dosis_signa dan aturan_pakai dari obat racikan
                        if (!empty($obat['dosis_signa'])) {
                            $dosisSigmaArray[] = $obat['dosis_signa'];
                        }
                        if (!empty($obat['aturan_pakai'])) {
                            $aturanPakaiArray[] = $obat['aturan_pakai'];
                        }
                    }
                    $totalJasaRacik += $racikan['jasa_racik'];
                }
            }

            $diskonInput = $request->diskon ?? 0;
            $diskonType = $request->diskon_type ?? 'percent';
            $nilaiDiskon = 0;

            if ($diskonType === 'percent') {
                $nilaiDiskon = ($totalObat * $diskonInput) / 100;
            } else {
                $nilaiDiskon = $diskonInput;
            }

            $subtotalSetelahDiskon = $totalObat - $nilaiDiskon;

            
            $pajakInput = $request->pajak ?? 0;
            $pajakType = $request->pajak_type ?? 'percent';
            $nilaiPajak = 0;

            if ($pajakType === 'percent') {
                $nilaiPajak = ($subtotalSetelahDiskon * $pajakInput) / 100;
            } else {
                $nilaiPajak = $pajakInput;
            }

            
            $totalBayar = $subtotalSetelahDiskon + $nilaiPajak + $totalJasaRacik;

            
            $resep = Resep::create([
                'no_resep' => $noResep,
                'pasien_id' => $request->pasien_id,
                'jenis_resep' => 'resep',
                'status_obat' => $statusObat,
                'jenis_racikan' => !empty($jenisRacikanArray) ? implode(', ', $jenisRacikanArray) : null,
                'hasil_racikan' => !empty($hasilRacikanArray) ? implode(', ', $hasilRacikanArray) : null,
                'dosis_signa' => !empty($dosisSigmaArray) ? implode(', ', array_unique($dosisSigmaArray)) : null,  
                'aturan_pakai' => !empty($aturanPakaiArray) ? implode(', ', array_unique($aturanPakaiArray)) : null, 
                // 'embalase' => $embalase,
                'dokter_resep' => $request->dokter_resep,
                'jasa_racik' => $totalJasaRacik,
                'diskon' => $diskonInput,
                'diskon_type' => $diskonType,
                'pajak' => $pajakInput,
                'pajak_type' => $pajakType,
                'total_harga' => $totalBayar,
                'keterangan' => $request->keterangan,
                'status' => 'menunggu',
                'tanggal_resep' => Carbon::now(),
                'user_id' => auth()->id(),
            ]);

            // ===== SIMPAN DETAIL OBAT NON RACIK =====
            if (!empty($request->obat_non_racik)) {
                foreach ($request->obat_non_racik as $obatData) {
                    $detailStock = DetailstockApotik::where('id', $obatData['id'])
                        ->whereRaw('(stock_apotik - retur) >= ?', [$obatData['jumlah']])
                        ->first();

                    if (!$detailStock) {
                        throw new \Exception("Stock obat tidak mencukupi");
                    }

                    $detailSupplierId = $detailStock->detailSupplier?->id;

                    if (!$detailSupplierId) {
                        throw new \Exception("Detail supplier tidak ditemukan untuk obat ini");
                    }

                    DetailResep::create([
                        'resep_id' => $resep->id,
                        'detail_supplier_id' => $detailSupplierId,
                        'detail_obat_rs_id' => $detailStock->detail_obat_rs_id,
                        'jumlah' => $obatData['jumlah'],
                        'harga_satuan' => $obatData['harga'],
                        'subtotal' => $obatData['subtotal'],
                    ]);
                }
            }

            // ===== SIMPAN DETAIL RACIKAN =====
            if (!empty($request->racikan)) {
                foreach ($request->racikan as $racikanData) {
                    foreach ($racikanData['obat'] as $obatData) {
                        $detailStock = DetailstockApotik::where('id', $obatData['id'])
                            ->whereRaw('(stock_apotik - retur) >= ?', [$obatData['jumlah']])
                            ->first();

                        if (!$detailStock) {
                            throw new \Exception("Stock obat dalam racikan {$racikanData['nama_racikan']} tidak mencukupi");
                        }

                        $detailSupplierId = $detailStock->detailSupplier?->id;

                        if (!$detailSupplierId) {
                            throw new \Exception("Detail supplier tidak ditemukan untuk obat dalam racikan");
                        }

                        $subtotal = $obatData['jumlah'] * $obatData['harga'];

                        DetailResep::create([
                            'resep_id' => $resep->id,
                            'detail_supplier_id' => $detailSupplierId,
                            'detail_obat_rs_id' => $detailStock->detail_obat_rs_id,
                            'jumlah' => $obatData['jumlah'],
                            'harga_satuan' => $obatData['harga'],
                            'subtotal' => $subtotal,
                        ]);
                    }
                }
            }

            // Insert ke tabel apotiks untuk tracking
            $pasien = Pasien::find($request->pasien_id);
            Apotik::create([
                'no_rm' => $pasien->no_rm,
                'pasien_id' => $request->pasien_id,
                'status' => 'menunggu',
                'tanggal' => Carbon::today()
            ]);

            // ✅ TAMBAHAN: BUAT TAGIHAN OTOMATIS
            try {
                $tagihan = $this->tagihanService->createTagihanFromResep($resep);
                
                \Log::info('Tagihan berhasil dibuat', [
                    'no_tagihan' => $tagihan->no_tagihan,
                    'no_resep' => $noResep,
                    'total_tagihan' => $tagihan->total_tagihan
                ]);
            } catch (\Exception $e) {
                \Log::error('Gagal membuat tagihan otomatis', [
                    'no_resep' => $noResep,
                    'error' => $e->getMessage()
                ]);
                
                throw new \Exception("Resep berhasil dibuat, tetapi gagal membuat tagihan: " . $e->getMessage());
            }

            DB::commit();

            \Log::info('Resep dan tagihan berhasil dibuat', [
                'no_resep' => $noResep,
                'no_tagihan' => $tagihan->no_tagihan ?? 'N/A',
                'resep_id' => $resep->id,
                'pasien_id' => $request->pasien_id,
                'total_bayar' => $totalBayar
            ]);

            return response()->json([
                'success' => true,
                'message' => "Resep berhasil dibuat dengan nomor: <strong>{$noResep}</strong>.<br>Tagihan otomatis telah dibuat dengan nomor: <strong>{$tagihan->no_tagihan}</strong>.<br>Silakan menuju ke Apotik untuk pengambilan obat.",
                'data' => [
                    'no_resep' => $noResep,
                    'resep_id' => $resep->id,
                    'no_tagihan' => $tagihan->no_tagihan ?? null,
                    'tagihan_id' => $tagihan->id_tagihan ?? null,
                    'total_bayar' => $totalBayar
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Gagal menyimpan resep dan tagihan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'pasien_id' => $request->pasien_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan resep: ' . $e->getMessage()
            ], 500);
        }
    }
}