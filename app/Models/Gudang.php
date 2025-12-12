<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    use HasUuids;
    protected $table = 'gudangs';
    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(DetailGudang::class);
    }

    public function histories()
    {
        return $this->hasMany(HistoryGudang::class, 'supplier_id', 'supplier_id');
    }

    public function stockApotiks()
    {
        return $this->hasMany(StockApotik::class, 'gudang_id');
    }
}
