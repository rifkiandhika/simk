<?php

namespace App\Models;

use Carbon\Carbon;
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
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_approval_kepala_gudang' => 'datetime',
        'tanggal_approval_kasir' => 'datetime',
        'tanggal_dikirim_ke_supplier' => 'datetime',
        'tanggal_upload_bukti_invoice' => 'datetime',
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
        return $this->hasMany(PoAuditTrail::class, 'id_po', 'id_po');
    }
    public function penerima()
    {
        return $this->belongsTo(Karyawan::class, 'id_penerima', 'id_karyawan');
    }
    public function karyawanUploadBukti()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_upload_bukti', 'id_karyawan');
    }
    public function getBuktiInvoiceUrlAttribute()
    {
        if ($this->bukti_invoice) {
            return asset('storage/' . $this->bukti_invoice);
        }
        return null;
    }
    public function hasBuktiInvoice()
    {
        return !empty($this->bukti_invoice);
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
        return $this->status === 'selesai'
            && $this->tipe_po === 'eksternal'
            && (
                empty($this->no_invoice)
                || empty($this->no_surat_jalan)
                || empty($this->no_kwitansi)
                || empty($this->nomor_faktur_pajak)
            );
    }


    public function tagihan()
    {
        return $this->hasOne(TagihanPo::class, 'id_po', 'id_po');
    }

    /**
     * Check apakah PO sudah punya tagihan
     */
    public function hasTagihan()
    {
        return $this->tagihan()->exists();
    }

    /**
     * Get status tagihan
     */
    public function getStatusTagihanAttribute()
    {
        if (!$this->hasTagihan()) {
            return 'Belum Ada Tagihan';
        }

        return $this->tagihan->status;
    }

    public function isPendingApproval(): bool
    {
        return in_array($this->status, [
            'menunggu_persetujuan_kepala_gudang',
            'menunggu_persetujuan_kasir'
        ]);
    }

    /**
     * Get hours left before auto-cancel
     */
    public function hoursLeftBeforeCancel(): ?int
    {
        if (!$this->isPendingApproval()) {
            return null;
        }

        $referenceDate = $this->status === 'menunggu_persetujuan_kepala_gudang'
            ? $this->created_at
            : $this->tanggal_approval_kepala_gudang;

        if (!$referenceDate) {
            return null;
        }

        $deadline = Carbon::parse($referenceDate)->addDay();
        $hoursLeft = now()->diffInHours($deadline, false);

        return $hoursLeft > 0 ? (int)$hoursLeft : 0;
    }

    /**
     * Check if PO will be auto-cancelled soon (< 6 hours)
     */
    public function isNearDeadline(): bool
    {
        $hoursLeft = $this->hoursLeftBeforeCancel();
        return $hoursLeft !== null && $hoursLeft > 0 && $hoursLeft < 6;
    }

    /**
     * Get deadline timestamp
     */
    public function getDeadlineAttribute(): ?Carbon
    {
        if (!$this->isPendingApproval()) {
            return null;
        }

        $referenceDate = $this->status === 'menunggu_persetujuan_kepala_gudang'
            ? $this->created_at
            : $this->tanggal_approval_kepala_gudang;

        return $referenceDate ? Carbon::parse($referenceDate)->addDay() : null;
    }

    /**
     * Scope untuk PO yang perlu approval kepala gudang
     */
    public function scopePendingKepalaGudang($query)
    {
        return $query->where('status', 'menunggu_persetujuan_kepala_gudang')
            ->where('created_at', '>', Carbon::now()->subDay());
    }

    /**
     * Scope untuk PO yang perlu approval kasir
     */
    public function scopePendingKasir($query)
    {
        return $query->where('status', 'menunggu_persetujuan_kasir')
            ->where('tanggal_approval_kepala_gudang', '>', Carbon::now()->subDay());
    }

    /**
     * Scope untuk PO yang akan di-auto-cancel
     */
    public function scopeToBeAutoCancelled($query)
    {
        $oneDayAgo = Carbon::now()->subDay();

        return $query->where(function ($q) use ($oneDayAgo) {
            $q->where('status', 'menunggu_persetujuan_kepala_gudang')
                ->where('created_at', '<=', $oneDayAgo);
        })->orWhere(function ($q) use ($oneDayAgo) {
            $q->where('status', 'menunggu_persetujuan_kasir')
                ->where('tanggal_approval_kepala_gudang', '<=', $oneDayAgo);
        });
    }
}
