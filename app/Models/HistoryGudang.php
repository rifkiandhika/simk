<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class HistoryGudang extends Model
{
    use HasUuids;
    protected $table = 'history_gudangs';
    protected $guarded = [];

    /**
     * Relationship dengan Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function barang()
    {
        return $this->belongsTo(DetailSupplier::class);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
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
