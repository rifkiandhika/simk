<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosis extends Model
{
    use HasUuids, HasFactory;
    protected $table = 'dosis';
    protected $guarded = [];
}
