<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    use HasUuids;
    protected $table = 'permintaans';
    protected $guarded = [];

    public function detailPermintaan()
    {
        return $this->hasMany(DetailPermintaan::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
}
