<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasFactory;

    protected $table = 'reseps';
    protected $guarded = [];

    protected $casts = [
        'tanggal_resep' => 'datetime',
        'embalase' => 'decimal:2',
        'jasa_racik' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    /**
     * Relasi ke Pasien
     */
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id', 'id_pasien');
    }

    /**
     * Relasi ke Ruangan
     */
    // public function ruangan()
    // {
    //     return $this->belongsTo(Ruangan::class, 'ruangan_id');
    // }

    /**
     * Relasi ke User (Petugas yang membuat resep)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke ResepDetail
     */
    public function details()
    {
        return $this->hasMany(DetailResep::class, 'resep_id');
    }

    /**
     * Scope untuk filter berdasarkan jenis resep
     */
    public function scopeJenisResep($query, $jenis)
    {
        return $query->where('jenis_resep', $jenis);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Get formatted total harga
     */
    public function getFormattedTotalHargaAttribute()
    {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
    }
}
