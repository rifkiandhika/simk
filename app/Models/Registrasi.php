<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registrasi extends Model
{
    public function tagihan()
    {
        return $this->hasOne(Tagihan::class, 'id_registrasi');
    }
}
