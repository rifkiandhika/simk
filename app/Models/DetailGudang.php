<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DetailGudang extends Model
{
    use HasUuids;
    protected $table = 'detail_gudangs';
    protected $guarded = [];

    public function gudang()
    {
        return $this->belongsTo(Gudang::class);
    }

    // relasi ke detail_suppliers
    public function barang()
    {
        return $this->belongsTo(DetailSupplier::class);
    }

    public function detailSupplier()
    {
        return $this->belongsTo(DetailSupplier::class, 'barang_id', 'id');
    }
}
