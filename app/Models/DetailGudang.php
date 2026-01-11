<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DetailGudang extends Model
{
    use HasUuids;
    protected $table = 'detail_gudangs';
    protected $guarded = [];

    public function gudang()
    {
        return $this->belongsTo(Gudang::class);
    }

    // Relasi khusus untuk Obat (DetailObatRs)
    public function barangObat()
    {
        return $this->belongsTo(DetailobatRs::class, 'barang_id', 'id_detail_obat_rs');
    }

    // Relasi khusus untuk Alkes/Reagensia/Lainnya (DetailSupplier)
    public function alkes()
    {
        return $this->belongsTo(Alkes::class, 'barang_id', 'id');
    }
    public function reagensia()
    {
        return $this->belongsTo(Reagen::class, 'barang_id', 'id');
    }

    public function barangSupplier()
    {
        return $this->belongsTo(DetailSupplier::class, 'barang_id', 'detail_obat_rs_id');
    }

    // Accessor untuk mendapatkan nama barang
    public function getNamaBarangAttribute()
    {
        if ($this->barang_type === 'DetailObatRs') {
            return $this->barangObat->nama_obat_rs ?? '-';
        } elseif ($this->barang_type === 'DetailSupplier') {
            return $this->barangSupplier->nama ?? '-';
        }

        return '-';
    }

    // Accessor untuk mendapatkan jenis barang
    public function getJenisBarangAttribute()
    {
        if ($this->barang_type === 'DetailObatRs') {
            return 'Obat';
        } elseif ($this->barang_type === 'DetailSupplier' && $this->barangSupplier) {
            return $this->barangSupplier->jenis ?? '-';
        }

        return '-';
    }
}
