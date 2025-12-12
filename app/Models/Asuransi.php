<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Asuransi extends Model
{
    use HasUuids;
    protected $table = 'asuransis';
    protected $guarded = [];
}
