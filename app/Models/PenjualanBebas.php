<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PenjualanBebas extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'penjualan_bebas';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
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
                $model->kode_transaksi = 'PB-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    // Relasi ke detail penjualan bebas
    public function details()
    {
        return $this->hasMany(DetailPenjualanBebas::class);
    }

    // Relasi ke user (petugas apotik)
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}
