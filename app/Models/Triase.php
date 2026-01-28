<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Triase extends Model
{
     use HasFactory;

    protected $table = 'triases';
    protected $primaryKey = 'id_triase';

    protected $fillable = [
        'id_igd',
        'prioritas',
        'keluhan',
        'vital_sign_ringkas',
        'petugas'
    ];

    public function igd()
    {
        return $this->belongsTo(Igd::class, 'id_igd', 'id_igd');
    }

    public function petugas()
    {
        return $this->belongsTo(Karyawan::class, 'petugas', 'id_karyawan');
    }

    // Accessors
    public function getWarnaAttribute()
    {
        return match($this->prioritas) {
            'P1 Merah' => 'red',
            'P2 Kuning' => 'yellow',
            'P3 Hijau' => 'green',
            'P4 Hitam' => 'black',
            default => 'gray'
        };
    }
}
