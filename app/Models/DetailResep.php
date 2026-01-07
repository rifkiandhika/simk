<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailResep extends Model
{
    use HasFactory;

    protected $table = 'detail_reseps';
    protected $guarded = [];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relasi ke Resep
     */
    public function resep()
    {
        return $this->belongsTo(Resep::class, 'resep_id');
    }

    /**
     * Relasi ke DetailSupplier (Stock Obat)
     */
    public function detailSupplier()
    {
        return $this->belongsTo(DetailSupplier::class, 'detail_supplier_id');
    }

    /**
     * Get nama obat through detailSupplier
     */
    public function getNamaObatAttribute()
    {
        return $this->detailSupplier->obat->nama_obat ?? '-';
    }

    /**
     * Get formatted harga satuan
     */
    public function getFormattedHargaSatuanAttribute()
    {
        return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.');
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}
