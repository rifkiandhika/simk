<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DetailobatRs extends Model
{
    use HasUuids;
    protected $table = 'detail_obat_rs';
    protected $primaryKey = 'id_detail_obat_rs';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    public function obat()
    {
        return $this->belongsTo(ObatRs::class);
    }
    public function obatMaster()
    {
        return $this->belongsTo(ObatMaster::class, 'id_obat_master', 'id_obat_master');
    }
}
