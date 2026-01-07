<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DetailPenjualanBebas extends Model
{
    use HasUuids, HasFactory;
    protected $table = 'detail_penjualan_bebas';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'diskon_item' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Relasi ke penjualan bebas
    public function penjualanBebas()
    {
        return $this->belongsTo(PenjualanBebas::class);
    }

    // Relasi ke detail stock apotik
    public function detailStockApotik()
    {
        return $this->belongsTo(DetailStockApotik::class);
    }
}
