<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasUuids, HasFactory;
    protected $table = 'ruangans';
    protected $guarded = [];

    public function pasiens()
    {
        return $this->hasMany(Pasien::class, 'ruangan_id');
    }

    public function dokters()
    {
        return $this->belongsToMany(
            Dokter::class,
            'dokter_ruangans',
            'ruangan_id',
            'dokter_id'
        );
    }

    /**
     * Get jumlah pasien aktif di ruangan
     */
    public function getPasienAktifCountAttribute()
    {
        return $this->pasiens()->where('status_aktif', 'Aktif')->count();
    }

    /**
     * Get persentase okupansi ruangan (khusus rawat inap)
     */
    public function getOkupansiAttribute()
    {
        if ($this->jenis !== 'rawat_inap' || $this->kapasitas == 0) {
            return 0;
        }
        
        return round(($this->pasien_aktif_count / $this->kapasitas) * 100, 2);
    }

    /**
     * Cek apakah ruangan masih tersedia
     */
    public function isTersedia()
    {
        if ($this->jenis === 'rawat_inap') {
            return $this->pasien_aktif_count < $this->kapasitas;
        }
        
        return true; // Untuk jenis lain selalu tersedia
    }

    /**
     * Get formatted jenis ruangan
     */
    public function getJenisLabelAttribute()
    {
        $labels = [
            'rawat_jalan' => 'Rawat Jalan',
            'rawat_inap' => 'Rawat Inap',
            'igd' => 'IGD',
            'penunjang' => 'Penunjang'
        ];
        
        return $labels[$this->jenis] ?? $this->jenis;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Aktif' : 'Nonaktif';
    }

    /**
     * Scope untuk filter ruangan aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope untuk filter berdasarkan jenis
     */
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }
}
