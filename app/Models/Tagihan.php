<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;
    protected $table = 'tagihans';
     protected $primaryKey = 'id_tagihan'; 
    public $incrementing = true; 
    protected $guarded = [];

    protected $casts = [
        'tanggal_tagihan' => 'date',
        'tanggal_lunas' => 'date',
        'locked_at' => 'datetime',
        'total_tagihan' => 'decimal:2',
        'total_dibayar' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
        'locked' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(TagihanItem::class, 'id_tagihan');
    }

    public function pembayarans()
    {
        return $this->hasMany(TagihanPembayaran::class, 'id_tagihan');
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'id_pasien');
    }

    public function registrasi()
    {
        return $this->belongsTo(Registrasi::class, 'id_registrasi');
    }

    public function resep()
    {
        return $this->belongsTo(Resep::class, 'resep_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(Karyawan::class, 'created_by', 'id_karyawan');
    }

    public function locker()
    {
        return $this->belongsTo(Karyawan::class, 'locked_by', 'id_karyawan');
    }

    // Scopes
    public function scopeBelumLunas($query)
    {
        return $query->where('status', '!=', 'LUNAS');
    }

    public function scopeLunas($query)
    {
        return $query->where('status', 'LUNAS');
    }

    public function scopeNotLocked($query)
    {
        return $query->where('locked', false);
    }

    // Helpers
    public function isLocked()
    {
        return $this->locked;
    }

    public function isLunas()
    {
        return $this->status === 'LUNAS';
    }

    public function getPersentasePembayaranAttribute()
    {
        if ($this->total_tagihan <= 0) {
            return 0;
        }

        return round(($this->total_dibayar / $this->total_tagihan) * 100, 2);
    }
}
