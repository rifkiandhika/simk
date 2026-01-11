<?php

namespace App\Services;

use App\Models\Tagihan;
use App\Models\TagihanItem;
use App\Models\Resep;
use App\Models\Pasien;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TagihanService
{
    /**
     * Buat tagihan dari resep
     */
    public function createTagihanFromResep(Resep $resep)
    {
        DB::beginTransaction();
        try {
            $pasien = $resep->pasien;

            
            // Generate nomor tagihan
            $noTagihan = $this->generateNoTagihan();
            
            // Tentukan jenis tagihan berdasarkan konteks
            // Karena dari resep pasien, defaultnya RAWAT_JALAN
            $jenisTagihan = 'RAWAT_JALAN';
            
            // Buat tagihan utama
            $tagihan = Tagihan::create([
                'no_tagihan' => $noTagihan,
                'id_registrasi' => null, // Tidak ada registrasi terpisah
                'resep_id' => $resep->id, // Tidak ada registrasi terpisah
                'id_pasien' => $pasien->id_pasien,
                'tanggal_tagihan' => Carbon::now(),
                'jenis_tagihan' => $jenisTagihan,
                'total_tagihan' => $resep->total_harga,
                'total_dibayar' => 0,
                'sisa_tagihan' => $resep->total_harga,
                'status' => 'BELUM_LUNAS',
                'status_klaim' => $this->determineStatusKlaim($pasien),
                'catatan' => "Tagihan untuk resep: {$resep->no_resep}",
                'created_by' => auth()->user()->id_karyawan,
                'locked' => false,
            ]);
            
            // Buat tagihan items dari detail resep
            $this->createTagihanItemsFromResep($resep, $tagihan);
            
            // Buat item untuk embalase jika ada
            if ($resep->embalase > 0) {
                $this->createEmbalaseItem($tagihan, $resep);
            }
            
            // Buat item untuk jasa racik jika ada
            if ($resep->jasa_racik > 0) {
                $this->createJasaRacikItem($tagihan, $resep);
            }
            
            DB::commit();
            
            return $tagihan;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Generate nomor tagihan otomatis
     */
    private function generateNoTagihan()
    {
        $lastTagihan = Tagihan::whereDate('created_at', Carbon::today())
            ->orderBy('id_tagihan', 'desc')
            ->first();
        
        $counter = $lastTagihan ? (intval(substr($lastTagihan->no_tagihan, -4)) + 1) : 1;
        
        return 'TGH/' . date('Ymd') . '/' . str_pad($counter, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Buat tagihan items dari detail resep
     */
    private function createTagihanItemsFromResep(Resep $resep, Tagihan $tagihan)
    {
        foreach ($resep->details as $detail) {
            $namaObat = $detail->detailSupplier->nama ?? 'Obat';
            $merkObat = $detail->detailSupplier->merk ?? '';
            $deskripsi = trim("{$namaObat} {$merkObat}");
            
            TagihanItem::create([
                'id_tagihan' => $tagihan->id_tagihan,
                'kategori' => 'APOTIK',
                'referensi_tipe' => 'resep',
                'referensi_id' => $resep->id,
                'deskripsi' => $deskripsi,
                'qty' => $detail->jumlah,
                'harga' => $detail->harga_satuan,
                'subtotal' => $detail->subtotal,
                'ditanggung' => $this->isDitanggung($tagihan),
                'created_by' => auth()->user()->id_karyawan,
            ]);
        }
    }
    
    /**
     * Buat item untuk embalase
     */
    private function createEmbalaseItem(Tagihan $tagihan, Resep $resep)
    {
        TagihanItem::create([
            'id_tagihan' => $tagihan->id_tagihan,
            'kategori' => 'APOTIK',
            'referensi_tipe' => 'resep',
            'referensi_id' => $resep->id,
            'deskripsi' => 'Embalase Obat',
            'qty' => 1,
            'harga' => $resep->embalase,
            'subtotal' => $resep->embalase,
            'ditanggung' => false, // Embalase biasanya tidak ditanggung
            'created_by' => auth()->user()->id_karyawan,
        ]);
    }
    
    /**
     * Buat item untuk jasa racik
     */
    private function createJasaRacikItem(Tagihan $tagihan, Resep $resep)
    {
        $deskripsi = "Jasa Racik";
        if ($resep->jenis_racikan) {
            $deskripsi .= " ({$resep->jenis_racikan})";
        }
        
        TagihanItem::create([
            'id_tagihan' => $tagihan->id_tagihan,
            'kategori' => 'APOTIK',
            'referensi_tipe' => 'resep',
            'referensi_id' => $resep->id,
            'deskripsi' => $deskripsi,
            'qty' => 1,
            'harga' => $resep->jasa_racik,
            'subtotal' => $resep->jasa_racik,
            'ditanggung' => $this->isDitanggung($tagihan),
            'created_by' => auth()->user()->id_karyawan,
        ]);
    }
    
    /**
     * Tentukan status klaim berdasarkan jenis pembayaran pasien
     */
    private function determineStatusKlaim(Pasien $pasien)
    {
        if ($pasien->jenis_pembayaran === 'BPJS') {
            return 'PENDING';
        } elseif ($pasien->jenis_pembayaran === 'Asuransi') {
            return 'PENDING';
        }
        
        return 'NON_KLAIM';
    }
    
    /**
     * Tentukan apakah item ditanggung BPJS/Asuransi
     */
    private function isDitanggung(Tagihan $tagihan)
    {
        return in_array($tagihan->status_klaim, ['PENDING', 'DISETUJUI']);
    }
}