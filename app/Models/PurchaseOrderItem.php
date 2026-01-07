<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PurchaseOrderItem extends Model
{
    use HasUuids;
    protected $table = 'purchase_order_items';
    protected $primaryKey = 'id_po_item';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'tanggal_kadaluarsa' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id_po_item = (string) Str::uuid();
        });
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'id_po', 'id_po');
    }

    public function produk()
    {
        return $this->belongsTo(DetailSupplier::class, 'id_produk', 'product_id');
    }

    public function batches()
    {
        return $this->hasMany(PurchaseOrderItemBatch::class, 'id_po_item', 'id_po_item');
    }

    public function getTotalQtyDiterimaFromBatches()
    {
        return $this->batches()->sum('qty_diterima');
    }

    // Method untuk mendapatkan qty yang kondisi baik
    public function getTotalQtyBaikFromBatches()
    {
        return $this->batches()->where('kondisi', 'baik')->sum('qty_diterima');
    }
}
