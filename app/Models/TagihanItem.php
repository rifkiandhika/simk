<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanItem extends Model
{
    use HasFactory;
    protected $table = 'tagihan_items';
    protected $guarded = [];

    protected $casts = [
        'qty' => 'integer',
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'ditanggung' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke Tagihan
     */
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'id_tagihan', 'id_tagihan');
    }

    /**
     * Relasi ke Creator (User/Karyawan yang membuat)
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\Karyawan::class, 'created_by', 'id_karyawan');
    }

    /**
     * Get relasi referensi secara dynamic
     * Bisa digunakan untuk mendapatkan data resep/tindakan/lab
     */
    public function getReferensiAttribute()
    {
        switch ($this->referensi_tipe) {
            case 'resep':
                return \App\Models\Resep::find($this->referensi_id);
            case 'tindakan':
                // return \App\Models\Tindakan::find($this->referensi_id);
                return null;
            case 'lab':
                // return \App\Models\Lab::find($this->referensi_id);
                return null;
            case 'radiologi':
                // return \App\Models\Radiologi::find($this->referensi_id);
                return null;
            default:
                return null;
        }
    }

    /**
     * Scope untuk filter berdasarkan kategori
     */
    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope untuk item apotik saja
     */
    public function scopeApotik($query)
    {
        return $query->where('kategori', 'APOTIK');
    }

    /**
     * Scope untuk item yang ditanggung BPJS/Asuransi
     */
    public function scopeDitanggung($query, $ditanggung = true)
    {
        return $query->where('ditanggung', $ditanggung);
    }

    /**
     * Scope untuk filter berdasarkan referensi resep
     */
    public function scopeFromResep($query, $resepId)
    {
        return $query->where('referensi_tipe', 'resep')
                     ->where('referensi_id', $resepId);
    }

    /**
     * Get formatted harga
     */
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Get badge class untuk kategori
     */
    public function getKategoriBadgeAttribute()
    {
        $badges = [
            'APOTIK' => 'badge-primary',
            'TINDAKAN' => 'badge-success',
            'LAB' => 'badge-info',
            'RADIOLOGI' => 'badge-warning',
            'KAMAR' => 'badge-secondary',
            'ADMIN' => 'badge-dark',
        ];

        return $badges[$this->kategori] ?? 'badge-secondary';
    }

    /**
     * Check apakah item ini dari resep
     */
    public function isFromResep()
    {
        return $this->referensi_tipe === 'resep';
    }

    /**
     * Check apakah item ini ditanggung
     */
    public function isDitanggung()
    {
        return $this->ditanggung === true;
    }
}
