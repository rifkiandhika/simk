<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TagihanPoItem extends Model
{
    use HasUuids;
    protected $table = 'tagihan_po_items';
    protected $primaryKey = 'id_tagihan_item';
    protected $guarded = [];

    protected $casts = [
        'tanggal_kadaluarsa' => 'date',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_tagihan_item)) {
                $model->id_tagihan_item = (string) Str::uuid();
            }
        });
    }

    /**
     * Relationships
     */
    public function tagihan()
    {
        return $this->belongsTo(TagihanPo::class, 'id_tagihan', 'id_tagihan');
    }

    public function poItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'id_po_item', 'id_po_item');
    }

    public function produk()
    {
        return $this->belongsTo(DetailSupplier::class, 'id_produk', 'id');
    }
}
