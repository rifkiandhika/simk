<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'retur_items';
    protected $primaryKey = 'id_retur_item';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'qty_diretur' => 'integer',
        'qty_diterima_kembali' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal_retur' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto calculate subtotal saat creating/updating
        static::saving(function ($model) {
            $model->subtotal_retur = $model->qty_diretur * $model->harga_satuan;
        });

        // Update total retur setelah saved/deleted
        static::saved(function ($model) {
            $model->retur->updateTotal();
        });

        static::deleted(function ($model) {
            $model->retur->updateTotal();
        });
    }

    /**
     * Relasi ke retur
     */
    public function retur(): BelongsTo
    {
        return $this->belongsTo(Retur::class, 'id_retur', 'id_retur');
    }

    /**
     * Relasi ke produk
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(DetailobatRs::class, 'id_produk', 'id_detail_obat_rs');
    }

    /**
     * Relasi ke purchase order item (jika dari PO)
     */
    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'id_item_sumber', 'id_po_item');
    }

    /**
     * Relasi ke detail stock apotik (jika dari Stock Apotik)
     */
    public function detailStockApotik(): BelongsTo
    {
        return $this->belongsTo(DetailStockApotik::class, 'id_item_sumber', 'id');
    }

    /**
     * Relasi ke retur item batches
     */
    public function batches(): HasMany
    {
        return $this->hasMany(ReturItemBatch::class, 'id_retur_item', 'id_retur_item');
    }

    /**
     * Get sisa quantity yang belum diterima kembali
     */
    public function getSisaQtyAttribute(): int
    {
        return $this->qty_diretur - $this->qty_diterima_kembali;
    }

    /**
     * Check apakah item sudah lengkap diterima
     */
    public function isComplete(): bool
    {
        return $this->qty_diterima_kembali >= $this->qty_diretur;
    }
}