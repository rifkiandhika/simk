<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Alkes extends Model
{
    use HasUuids;
    protected $table = 'alkes';
    protected $guarded = [];

    protected $casts = [
        'tanggal_kalibrasi_terakhir' => 'date',
        'tanggal_kalibrasi_berikutnya' => 'date',
        'tanggal_kadaluarsa' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'stok_minimal' => 'integer',
        'jumlah_stok' => 'integer',
        'harga_beli' => 'integer',
        'harga_jual_umum' => 'integer',
        'harga_jual_bpjs' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
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

    public function getIsStokRendahAttribute()
    {
        return $this->jumlah_stok <= $this->stok_minimal;
    }

    public function getIsKadaluarsaAttribute()
    {
        if (!$this->tanggal_kadaluarsa) {
            return false;
        }
        return $this->tanggal_kadaluarsa->isPast();
    }

    public function getIsNeedKalibrasiAttribute()
    {
        if (!$this->tanggal_kalibrasi_berikutnya) {
            return false;
        }
        return $this->tanggal_kalibrasi_berikutnya->isPast();
    }

    public function getFormattedHargaBeliAttribute()
    {
        return 'Rp ' . number_format($this->harga_beli, 0, ',', '.');
    }

    public function getFormattedHargaJualUmumAttribute()
    {
        return 'Rp ' . number_format($this->harga_jual_umum, 0, ',', '.');
    }

    public function getFormattedHargaJualBpjsAttribute()
    {
        return 'Rp ' . number_format($this->harga_jual_bpjs, 0, ',', '.');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeByKondisi($query, $kondisi)
    {
        return $query->where('kondisi', $kondisi);
    }

    public function scopeStokRendah($query)
    {
        return $query->whereColumn('jumlah_stok', '<=', 'stok_minimal');
    }

    public function scopeKadaluarsa($query)
    {
        return $query->whereNotNull('tanggal_kadaluarsa')
            ->where('tanggal_kadaluarsa', '<', now());
    }

    public function scopeNeedKalibrasi($query)
    {
        return $query->whereNotNull('tanggal_kalibrasi_berikutnya')
            ->where('tanggal_kalibrasi_berikutnya', '<', now());
    }

    public function scopeKadaluarsaSoon($query, $days = 30)
    {
        return $query->whereNotNull('tanggal_kadaluarsa')
            ->where('tanggal_kadaluarsa', '>=', now())
            ->where('tanggal_kadaluarsa', '<=', now()->addDays($days));
    }
}
