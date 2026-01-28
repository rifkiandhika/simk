<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturItemBatch extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'retur_item_batches';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'tanggal_kadaluarsa' => 'date',
        'qty_diretur' => 'integer',
    ];

    /**
     * Relasi ke retur item
     */
    public function returItem(): BelongsTo
    {
        return $this->belongsTo(ReturItem::class, 'id_retur_item', 'id_retur_item');
    }

    /**
     * Check apakah batch sudah kadaluarsa
     */
    public function isExpired(): bool
    {
        return $this->tanggal_kadaluarsa && $this->tanggal_kadaluarsa < now();
    }
}