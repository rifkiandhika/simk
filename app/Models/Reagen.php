<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Reagen extends Model
{
    use HasUuids;
    protected $table = 'reagensias';
    protected $guarded = [];

    public function satuan()
    {
        return $this->hasMany(Satuan::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Karyawan::class, 'created_by', 'id_karyawan');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Karyawan::class, 'updated_by', 'id_karyawan');
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'Aktif';
    }

    public function getSuhuPenyimpananRangeAttribute()
    {
        if ($this->suhu_penyimpanan_min && $this->suhu_penyimpanan_max) {
            return "{$this->suhu_penyimpanan_min}°C - {$this->suhu_penyimpanan_max}°C";
        } elseif ($this->suhu_penyimpanan_min) {
            return "Min: {$this->suhu_penyimpanan_min}°C";
        } elseif ($this->suhu_penyimpanan_max) {
            return "Max: {$this->suhu_penyimpanan_max}°C";
        }
        return null;
    }

    public function getHasHazardAttribute()
    {
        return !empty($this->bahaya_keselamatan);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'Nonaktif');
    }

    public function scopeByMerk($query, $merk)
    {
        return $query->where('merk', $merk);
    }

    public function scopeStokRendah($query)
    {
        return $query->where('stok_minimal', '>', 0);
    }

    public function scopeWithHazard($query)
    {
        return $query->whereNotNull('bahaya_keselamatan')
            ->where('bahaya_keselamatan', '!=', '');
    }
}
