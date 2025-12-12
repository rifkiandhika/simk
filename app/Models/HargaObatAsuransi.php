<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HargaObatAsuransi extends Model
{
    use HasUuids;
    protected $table = 'harga_obat_asuransis';
    protected $guarded = [];

    protected $casts = [
        'aktif' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }

    public function detailObat()
    {
        return $this->belongsTo(DetailobatRs::class, 'id_detail_obat_rs', 'id_detail_obat_rs');
    }

    public function asuransi()
    {
        return $this->belongsTo(Asuransi::class, 'asuransi_id', 'id');
    }
}
