<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShippingActivity extends Model
{
    protected $table = 'shipping_activities';
    protected $primaryKey = 'id_shipping';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'tanggal_aktivitas' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id_shipping = (string) Str::uuid();
        });
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'id_po', 'id_po');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_input', 'id_karyawan');
    }
}
