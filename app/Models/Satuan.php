<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    use HasUuids;
    protected $table = 'satuans';
    protected $guarded = [];
}
