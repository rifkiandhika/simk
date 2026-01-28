<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturHistory extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'retur_histories';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'waktu_perubahan' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->waktu_perubahan)) {
                $model->waktu_perubahan = now();
            }
        });
    }

    /**
     * Relasi ke retur
     */
    public function retur(): BelongsTo
    {
        return $this->belongsTo(Retur::class, 'id_retur', 'id_retur');
    }

    /**
     * Relasi ke karyawan
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}