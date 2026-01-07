<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DetailstockApotik extends Model
{
    use HasUuids;
    protected $table = 'detail_stock_apotiks';
    protected $guarded = [];

    public function stockApotik()
    {
        return $this->belongsTo(StockApotik::class, 'stock_apotik_id');
    }

    public function detailSupplier()
    {
        return $this->belongsTo(DetailSupplier::class, 'detail_obat_rs_id', 'product_id');
    }

    public function obat()
    {
        return $this->belongsTo(ObatRs::class, 'obat_id');
    }

    public function histories()
    {
        return $this->hasMany(HistoryStockApotik::class, 'detail_apotik_id');
    }

    public function getStockGudangAttribute()
    {

        if (isset($this->attributes['stock_gudang'])) {
            return $this->attributes['stock_gudang'];
        }


        return $this->barangGudang ? $this->barangGudang->stock_gudang : 0;
    }

    public function getNamaBarangAttribute()
    {
        return $this->detailSupplier?->nama ?? '-';
    }

    // Accessor untuk mendapatkan judul barang
    public function getJudulBarangAttribute()
    {
        return $this->detailSupplier?->judul ?? '-';
    }

    // Accessor untuk mendapatkan jenis
    public function getJenisBarangAttribute()
    {
        return $this->detailSupplier?->jenis ?? '-';
    }

    // Accessor untuk mendapatkan satuan
    public function getSatuanBarangAttribute()
    {
        return $this->detailSupplier?->satuan ?? '-';
    }

    // Accessor untuk mendapatkan merk
    public function getMerkBarangAttribute()
    {
        return $this->detailSupplier?->merk ?? '-';
    }
}
