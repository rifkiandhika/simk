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
            $jenisTagihan = 'RAWAT_JALAN';
            
            // ✅ HITUNG TOTAL DARI ITEM (belum termasuk diskon/pajak)
            $totalObat = 0;
            foreach ($resep->details as $detail) {
                $totalObat += $detail->subtotal;
            }
            
            // ✅ HITUNG DISKON
            $nilaiDiskon = 0;
            if ($resep->diskon > 0) {
                if ($resep->diskon_type === 'percent') {
                    $nilaiDiskon = ($totalObat * $resep->diskon) / 100;
                } else {
                    $nilaiDiskon = $resep->diskon;
                }
            }
            
            // ✅ SUBTOTAL SETELAH DISKON (belum + jasa racik)
            $subtotalSetelahDiskon = $totalObat - $nilaiDiskon;
            
            // ✅ TAMBAH JASA RACIK (jika ada)
            $subtotalDenganJasaRacik = $subtotalSetelahDiskon + ($resep->jasa_racik ?? 0);
            
            // ✅ HITUNG PAJAK (dari subtotal setelah diskon + jasa racik)
            $nilaiPajak = 0;
            if ($resep->pajak > 0) {
                if ($resep->pajak_type === 'percent') {
                    $nilaiPajak = ($subtotalDenganJasaRacik * $resep->pajak) / 100;
                } else {
                    $nilaiPajak = $resep->pajak;
                }
            }
            
            // ✅ TOTAL AKHIR
            $totalTagihan = $subtotalDenganJasaRacik + $nilaiPajak;
            
            // Buat tagihan utama
            $tagihan = Tagihan::create([
                'no_tagihan' => $noTagihan,
                'id_registrasi' => null,
                'resep_id' => $resep->id,
                'id_pasien' => $pasien->id_pasien,
                'tanggal_tagihan' => Carbon::now(),
                'jenis_tagihan' => $jenisTagihan,
                'diskon' => $resep->diskon ?? 0,
                'diskon_type' => $resep->diskon_type ?? 'percent',
                'pajak' => $resep->pajak ?? 0,
                'pajak_type' => $resep->pajak_type ?? 'percent',
                'total_tagihan' => $totalTagihan, // ✅ GUNAKAN TOTAL YANG DIHITUNG ULANG
                'total_dibayar' => 0,
                'sisa_tagihan' => $totalTagihan, // ✅ GUNAKAN TOTAL YANG DIHITUNG ULANG
                'status' => 'BELUM_LUNAS',
                'status_klaim' => $this->determineStatusKlaim($pasien),
                'catatan' => "Tagihan untuk resep: {$resep->no_resep}",
                'created_by' => auth()->user()->id_karyawan,
                'locked' => false,
            ]);
            
            // Buat tagihan items dari detail resep (obat-obatan)
            $this->createTagihanItemsFromResep($resep, $tagihan);
            
            // ✅ Buat item untuk DISKON (jika ada) - sebagai PENGURANG
            if ($nilaiDiskon > 0) {
                $this->createDiskonItem($tagihan, $resep, $nilaiDiskon);
            }
            
            // Buat item untuk jasa racik jika ada
            if ($resep->jasa_racik > 0) {
                $this->createJasaRacikItem($tagihan, $resep);
            }
            
            // ✅ Buat item untuk PAJAK (jika ada) - sebagai PENAMBAH
            if ($nilaiPajak > 0) {
                $this->createPajakItem($tagihan, $resep, $nilaiPajak);
            }
            
            // Buat item untuk embalase jika ada (opsional, jika masih dipakai)
            if (isset($resep->embalase) && $resep->embalase > 0) {
                $this->createEmbalaseItem($tagihan, $resep);
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
                // 'kategori' => 'APOTIK',
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
     * ✅ BARU: Buat item untuk diskon (sebagai pengurang)
     */
    private function createDiskonItem(Tagihan $tagihan, Resep $resep, $nilaiDiskon)
    {
        $deskripsi = "Diskon ";
        if ($resep->diskon_type === 'percent') {
            $deskripsi .= "({$resep->diskon}%)";
        } else {
            $deskripsi .= "(Rp " . number_format($resep->diskon, 0, ',', '.') . ")";
        }
        
        TagihanItem::create([
            'id_tagihan' => $tagihan->id_tagihan,
            // 'kategori' => 'DISKON',
            'referensi_tipe' => 'resep',
            'referensi_id' => $resep->id,
            'deskripsi' => $deskripsi,
            'qty' => 1,
            'harga' => -$nilaiDiskon, // ✅ NEGATIF untuk pengurang
            'subtotal' => -$nilaiDiskon, // ✅ NEGATIF untuk pengurang
            'ditanggung' => false,
            'created_by' => auth()->user()->id_karyawan,
        ]);
    }
    
    /**
     * ✅ BARU: Buat item untuk pajak (sebagai penambah)
     */
    private function createPajakItem(Tagihan $tagihan, Resep $resep, $nilaiPajak)
    {
        $deskripsi = "Pajak ";
        if ($resep->pajak_type === 'percent') {
            $deskripsi .= "({$resep->pajak}%)";
        } else {
            $deskripsi .= "(Rp " . number_format($resep->pajak, 0, ',', '.') . ")";
        }
        
        TagihanItem::create([
            'id_tagihan' => $tagihan->id_tagihan,
            // 'kategori' => 'PAJAK',
            'referensi_tipe' => 'resep',
            'referensi_id' => $resep->id,
            'deskripsi' => $deskripsi,
            'qty' => 1,
            'harga' => $nilaiPajak, // ✅ POSITIF untuk penambah
            'subtotal' => $nilaiPajak, // ✅ POSITIF untuk penambah
            'ditanggung' => false,
            'created_by' => auth()->user()->id_karyawan,
        ]);
    }
    
    /**
     * Buat item untuk embalase
     */
    private function createEmbalaseItem(Tagihan $tagihan, Resep $resep)
    {
        TagihanItem::create([
            'id_tagihan' => $tagihan->id_tagihan,
            // 'kategori' => 'APOTIK',
            'referensi_tipe' => 'resep',
            'referensi_id' => $resep->id,
            'deskripsi' => 'Embalase Obat',
            'qty' => 1,
            'harga' => $resep->embalase,
            'subtotal' => $resep->embalase,
            'ditanggung' => false,
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
            // 'kategori' => 'APOTIK',
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