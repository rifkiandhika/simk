<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apotik extends Model
{
    use HasUuids, HasFactory;
    protected $table = 'apotiks';
    protected $guarded = [];
    protected $casts = [
        'tanggal' => 'date'
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id', 'id_pasien');
    }

    // Relasi ke resep (1 pasien bisa punya banyak resep)
    public function reseps()
    {
        return $this->hasMany(Resep::class, 'pasien_id', 'pasien_id')
            ->whereDate('tanggal_resep', $this->tanggal);
    }
}
