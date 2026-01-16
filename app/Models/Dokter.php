<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    use HasFactory;
    protected $table = 'dokters';
    protected $primaryKey = 'id_dokter';
    protected $guarded = [];
    
    public function ruangans()
    {
        return $this->belongsToMany(
            Ruangan::class,
            'dokter_ruangans',
            'dokter_id',
            'ruangan_id'
        )->withPivot(['hari', 'jam_mulai', 'jam_selesai'])
         ->withTimestamps();
    }
}
