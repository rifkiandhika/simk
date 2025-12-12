<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItemBatch extends Model
{
    use HasUuids;
    protected $table = 'purchase_order_item_batches';
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'id_po_item', 'id_po_item');
    }
}
