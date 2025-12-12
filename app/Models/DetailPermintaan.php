<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DetailPermintaan extends Model
{
    use HasUuids;
    protected $table = 'detail_permintaans';
    protected $guarded = [];

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class);
    }
}
