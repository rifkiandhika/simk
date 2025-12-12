<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PoAuditTrail extends Model
{
    protected $table = 'po_audit_trails';
    protected $primaryKey = 'id_audit';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'data_sebelum' => 'array',
        'data_sesudah' => 'array',
        'tanggal_aksi' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id_audit = (string) Str::uuid();
            $model->tanggal_aksi = now();
            $model->ip_address = request()->ip();
            $model->user_agent = request()->userAgent();
        });
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'id_po', 'id_po');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}
