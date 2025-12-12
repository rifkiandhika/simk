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
}
