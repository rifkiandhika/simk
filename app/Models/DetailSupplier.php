<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DetailSupplier extends Model
{
    use HasUuids;
    protected $table = 'detail_suppliers';
    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function detailGudangs()
    {
        return $this->hasMany(DetailGudang::class);
    }

    public function detailStockApotiks()
    {
        return $this->hasMany(DetailStockApotik::class, 'obat_id');
    }
    public function obats()
    {
        return $this->belongsTo(DetailObatRs::class, 'detail_obat_rs_id', 'id_detail_obat_rs');
    }
    public function alkes()
    {
        return $this->belongsTo(Alkes::class, 'product_id', 'id');
    }
    public function reagensia()
    {
        return $this->belongsTo(Reagen::class, 'product_id', 'id');
    }
    public function hargaObat()
    {
        return $this->belongsTo(HargaObat::class, 'product_id', 'id_detail_obat_rs');
    }
}
