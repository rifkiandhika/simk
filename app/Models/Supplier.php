<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasUuids;
    protected $table = 'suppliers';
    protected $guarded = [];

    public function detailSuppliers()
    {
        return $this->hasMany(DetailSupplier::class);
    }
    // Di model Supplier
    public function obats()
    {
        return $this->hasMany(DetailobatRs::class);
    }

    public function alkes()
    {
        return $this->hasMany(Alkes::class);
    }

    public function reagensias()
    {
        return $this->hasMany(Reagen::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'id_supplier', 'id_supplier');
    }
}
