<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HistoryGudang extends Model
{
    use HasUuids;
    protected $table = 'history_gudangs';
    protected $guarded = [];

    protected $casts = [
        'waktu_proses' => 'datetime',
        'jumlah' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Relationship dengan Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang_id');
    }

    public function barang()
    {
        return $this->belongsTo(DetailSupplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'referensi_id', 'id_po');
    }

    /**
     * Scope untuk filter berdasarkan status
     */

    public function scopePenerimaan($query)
    {
        return $query->where('status', 'penerimaan');
    }

    public function scopePengiriman($query)
    {
        return $query->where('status', 'pengiriman');
    }

    public function scopeByPeriod($query, $start, $end)
    {
        return $query->whereBetween('waktu_proses', [$start, $end]);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan periode
     */
    public function scopePeriode($query, $tanggalMulai, $tanggalAkhir)
    {
        return $query->whereBetween('waktu_proses', [$tanggalMulai, $tanggalAkhir]);
    }

    /**
     * Accessor untuk format tanggal Indonesia
     */
    public function getWaktuProsesFormattedAttribute()
    {
        return $this->waktu_proses->format('d/m/Y H:i:s');
    }

    /**
     * Accessor untuk status badge
     */
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'penerimaan' ?
            '<span class="badge bg-success">Penerimaan</span>' :
            '<span class="badge bg-warning">Pengiriman</span>';
    }
}
