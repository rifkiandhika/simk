<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasUuids;
    protected $table = 'karyawans';
    protected $guarded = [];
    protected $primaryKey = 'id_karyawan';

    public function user()
    {
        return $this->hasOne(User::class, 'id_karyawan', 'id_karyawan');
    }

    /**
     * Check if karyawan is active
     */
    public function isActive()
    {
        return $this->status_aktif === 'Aktif';
    }

    /**
     * Scope to get only active karyawan
     */
    public function scopeActive($query)
    {
        return $query->where('status_aktif', 'Aktif');
    }

    /**
     * Scope to get karyawan without user account
     */
    public function scopeWithoutUser($query)
    {
        return $query->doesntHave('user');
    }

    /**
     * Get full name with NIP
     */
    public function getFullNameWithNipAttribute()
    {
        return $this->nip . ' - ' . $this->nama_lengkap;
    }

    public function purchaseOrdersPemohon()
    {
        return $this->hasMany(PurchaseOrder::class, 'id_karyawan_pemohon', 'id_karyawan');
    }

    public function purchaseOrdersApprovedAsKepalaGudang()
    {
        return $this->hasMany(PurchaseOrder::class, 'id_kepala_gudang_approval', 'id_karyawan');
    }

    public function purchaseOrdersApprovedAsKasir()
    {
        return $this->hasMany(PurchaseOrder::class, 'id_kasir_approval', 'id_karyawan');
    }

    public function shippingActivities()
    {
        return $this->hasMany(ShippingActivity::class, 'id_karyawan_input', 'id_karyawan');
    }

    public function auditTrails()
    {
        return $this->hasMany(POAuditTrail::class, 'id_karyawan', 'id_karyawan');
    }
}
