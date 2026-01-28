<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Igd extends Model
{
    use HasFactory;

    protected $table = 'igd';
    protected $primaryKey = 'id_igd';

    protected $guarded = [];

    protected $casts = [
        'waktu_datang' => 'datetime',
        'waktu_keluar' => 'datetime',
    ];

    
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'id_pasien', 'id_pasien');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_jaga', 'id_dokter');
    }

    public function perawat()
    {
        return $this->belongsTo(Perawat::class, 'perawat_jaga', 'id_perawat');
    }

    public function triase()
    {
        return $this->hasOne(Triase::class, 'id_igd', 'id_igd');
    }

    public function createdBy()
    {
        return $this->belongsTo(Karyawan::class, 'created_by', 'id_karyawan');
    }

    
    public function getFormattedWaktuDatangAttribute()
    {
        return $this->waktu_datang ? $this->waktu_datang->format('d-m-Y H:i') : null;
    }

    public function getFormattedWaktuKeluarAttribute()
    {
        return $this->waktu_keluar ? $this->waktu_keluar->format('d-m-Y H:i') : null;
    }

    public function getDurasiPerawatanAttribute()
    {
        if (!$this->waktu_keluar) {
            return $this->waktu_datang->diffForHumans();
        }
        
        return $this->waktu_datang->diffForHumans($this->waktu_keluar, true);
    }

    
    public function scopeDalamPerawatan($query)
    {
        return $query->where('status', 'Dalam Perawatan');
    }

    public function scopeHariIni($query)
    {
        return $query->whereDate('waktu_datang', today());
    }

    public function scopeTriaseMerah($query)
    {
        return $query->where('status_triase', 'Merah');
    }
}