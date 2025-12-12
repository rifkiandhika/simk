<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ObatRs extends Model
{
    use HasUuids;
    protected $table = 'obat_rs';
    protected $primaryKey = 'id_obat_rs';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    public function detailObats()
    {
        return $this->hasMany(
            DetailobatRs::class,  // model relasi
            'id_obat_rs',         // foreign key di tabel detail_obat_rs
            'id_obat_rs'          // primary key di tabel obat_rs
        );
    }
}
