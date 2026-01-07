<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasUuids, HasFactory;
    protected $table = 'pasiens';
    protected $guarded = [];
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke Asuransi
     */
    public function asuransi()
    {
        return $this->belongsTo(Asuransi::class, 'asuransi_id');
    }

    /**
     * Relasi ke Resep
     */
    public function reseps()
    {
        return $this->hasMany(Resep::class, 'pasien_id', 'id_pasien');
    }

    /**
     * Get umur pasien
     */
    public function getUmurAttribute()
    {
        if (!$this->tanggal_lahir) {
            return null;
        }
        return Carbon::parse($this->tanggal_lahir)->age;
    }

    /**
     * Get formatted jenis kelamin
     */
    public function getJenisKelaminTextAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    /**
     * Scope untuk pasien aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', 'Aktif');
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    /**
     * Scope untuk pencarian
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('no_rm', 'like', "%{$keyword}%")
                ->orWhere('nama_lengkap', 'like', "%{$keyword}%")
                ->orWhere('nik', 'like', "%{$keyword}%");
        });
    }
}
