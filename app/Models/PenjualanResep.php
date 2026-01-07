<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PenjualanResep extends Model
{
    use HasUuids, HasFactory;
    protected $table = 'penjualan_resep';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'tanggal_resep' => 'date',
        'tanggal_transaksi' => 'datetime',
        'subtotal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'pajak' => 'decimal:2',
        'total' => 'decimal:2',
        'bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            if (empty($model->kode_transaksi)) {
                $model->kode_transaksi = 'PR-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    // Relasi ke detail penjualan resep
    public function details()
    {
        return $this->hasMany(DetailPenjualanResep::class);
    }

    // Relasi ke user (petugas apotik)
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // Scope untuk filter status
    public function scopeMenunggu($query)
    {
        return $query->where('status_resep', 'menunggu');
    }

    public function scopeDiproses($query)
    {
        return $query->where('status_resep', 'diproses');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status_resep', 'selesai');
    }
}
