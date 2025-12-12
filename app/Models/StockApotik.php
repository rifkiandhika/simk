<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class StockApotik extends Model
{
    use HasUuids;
    protected $table = 'stock_apotiks';
    protected $guarded = [];

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang_id');
    }

    public function details()
    {
        return $this->hasMany(DetailstockApotik::class, 'stock_apotik_id');
    }
}
