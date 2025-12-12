<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class HistoryStockApotik extends Model
{
    use HasUuids;
    protected $table = 'history_stockap';
    protected $guarded = [];


    public function detailApotik()
    {
        return $this->belongsTo(DetailStockApotik::class, 'detail_apotik_id');
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
}
