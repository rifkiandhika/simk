<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';
    protected $primaryKey = 'id_po';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'tanggal_permintaan' => 'date',
        'tanggal_approval_kepala_gudang' => 'datetime',
        'tanggal_approval_kasir' => 'datetime',
        'tanggal_dikirim_ke_supplier' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id_po = (string) Str::uuid();
            $model->no_po = self::generateNoPO();
        });
    }

    public static function generateNoPO()
    {
        $date = date('Ymd');
        $lastPO = self::whereDate('created_at', today())->latest()->first();
        $urutan = $lastPO ? (int)substr($lastPO->no_po, -3) + 1 : 1;
        return 'PO-' . $date . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);
    }

    // Relations
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'id_po', 'id_po');
    }

    public function karyawanPemohon()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_pemohon', 'id_karyawan');
    }

    public function kepalaGudang()
    {
        return $this->belongsTo(Karyawan::class, 'id_kepala_gudang_approval', 'id_karyawan');
    }

    public function kasir()
    {
        return $this->belongsTo(Karyawan::class, 'id_kasir_approval', 'id_karyawan');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
    }

    public function shippingActivities()
    {
        return $this->hasMany(ShippingActivity::class, 'id_po', 'id_po');
    }

    public function auditTrails()
    {
        return $this->hasMany(POAuditTrail::class, 'id_po', 'id_po');
    }
    public function penerima()
    {
        return $this->belongsTo(Karyawan::class, 'id_penerima', 'id_karyawan');
    }
    public function needsReceiptConfirmation()
    {
        // Internal: selesai tapi belum diterima
        if ($this->tipe_po === 'internal' && $this->status === 'selesai' && !$this->tanggal_diterima) {
            return true;
        }

        // Eksternal: ada shipping activity "diterima" atau "selesai" tapi belum dikonfirmasi
        if ($this->tipe_po === 'eksternal' && !$this->tanggal_diterima) {
            $lastShipping = $this->shippingActivities()
                ->whereIn('status_shipping', ['diterima', 'selesai'])
                ->latest('tanggal_aktivitas')
                ->first();

            return $lastShipping !== null;
        }

        return false;
    }
    public function karyawanInputInvoice()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_input_invoice', 'id_karyawan');
    }

    // Method untuk generate nomor GR
    public static function generateNoGR()
    {
        $prefix = 'PO-GR-';
        $lastGR = self::where('no_gr', 'like', $prefix . '%')
            ->orderBy('no_gr', 'desc')
            ->first();

        if ($lastGR) {
            $lastNumber = (int) substr($lastGR->no_gr, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    // Method untuk cek apakah sudah input invoice
    public function hasInvoice()
    {
        return !empty($this->no_invoice);
    }

    // Method untuk cek apakah perlu input invoice (sudah diterima tapi belum ada invoice)
    public function needsInvoice()
    {
        return $this->status === 'diterima'
            && $this->tipe_po === 'eksternal'
            && empty($this->no_invoice);
    }
}
