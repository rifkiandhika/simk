<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use iLLuminate\Support\Str;

class KFAMappingHistory extends Model
{
    protected $table = 'kfa_mapping_histories';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Relasi
    public function detailObat()
    {
        return $this->belongsTo(DetailobatRs::class, 'id_detail_obat_rs', 'id_detail_obat_rs');
    }

    public function obatMasterOld()
    {
        return $this->belongsTo(ObatMaster::class, 'id_obat_master_old', 'id_obat_master');
    }

    public function obatMasterNew()
    {
        return $this->belongsTo(ObatMaster::class, 'id_obat_master_new', 'id_obat_master');
    }

    public function user()
    {
        return $this->belongsTo(Karyawan::class, 'changed_by', 'id_karyawan');
    }
}
