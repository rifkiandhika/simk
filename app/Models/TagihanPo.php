<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TagihanPo extends Model
{
    use HasUuids;
    protected $table = 'tagihan_po';
    protected $primaryKey = 'id_tagihan';
    protected $guarded = [];

    protected $casts = [
        'tanggal_tagihan' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_dibuat' => 'datetime',
        'tanggal_approve' => 'datetime',
        'total_tagihan' => 'decimal:2',
        'pajak' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'total_dibayar' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_tagihan)) {
                $model->id_tagihan = (string) Str::uuid();
            }
            if (empty($model->no_tagihan)) {
                $model->no_tagihan = self::generateNoTagihan();
            }
            if (empty($model->tanggal_dibuat)) {
                $model->tanggal_dibuat = now();
            }
        });
    }

    /**
     * Generate nomor tagihan: TAG-YYYYMMDD-XXX
     */
    public static function generateNoTagihan()
    {
        $prefix = 'TAG-' . date('Ymd') . '-';
        $lastTagihan = self::where('no_tagihan', 'like', $prefix . '%')
            ->orderBy('no_tagihan', 'desc')
            ->first();

        if ($lastTagihan) {
            $lastNumber = (int) substr($lastTagihan->no_tagihan, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Relationships
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'id_po', 'id_po');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
    }

    public function items()
    {
        return $this->hasMany(TagihanPoItem::class, 'id_tagihan', 'id_tagihan');
    }

    public function pembayaran()
    {
        return $this->hasMany(PembayaranTagihan::class, 'id_tagihan', 'id_tagihan');
    }

    public function karyawanBuat()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_buat', 'id_karyawan');
    }

    public function karyawanApprove()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_approve', 'id_karyawan');
    }

    /**
     * Helpers
     */
    public function isLunas()
    {
        return $this->status === 'lunas';
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    // public function canBePaid()
    // {
    //     return in_array($this->status, ['menunggu_pembayaran', 'dibayar_sebagian']);
    // }

    public function getSisaTagihanAttribute($value)
    {
        return $this->grand_total - $this->total_dibayar;
    }

    public function getPersenTerbayarAttribute()
    {
        if ($this->grand_total == 0) return 0;
        return round(($this->total_dibayar / $this->grand_total) * 100, 2);
    }

    public function getIsJatuhTempoAttribute()
    {
        if (!$this->tanggal_jatuh_tempo) return false;
        return now()->isAfter($this->tanggal_jatuh_tempo) && !$this->isLunas();
    }

    /**
     * Update total dibayar dan status
     */
    public function updatePembayaran()
    {
        $totalDibayar = $this->pembayaran()
            ->where('status_pembayaran', 'diverifikasi')
            ->sum('jumlah_bayar');

        $this->total_dibayar = $totalDibayar;
        $this->sisa_tagihan = $this->grand_total - $totalDibayar;

        // Update status
        if ($this->sisa_tagihan <= 0) {
            $this->status = 'lunas';
        } elseif ($totalDibayar > 0) {
            $this->status = 'dibayar_sebagian';
        }

        $this->save();
    }

    public function canBePaid(): bool
    {
        return in_array($this->status, ['menunggu_pembayaran', 'dibayar_sebagian'])
            && $this->sisa_tagihan > 0;
    }

    /**
     * Check if tagihan is overdue
     */
    public function isOverdue(): bool
    {
        if (!$this->tanggal_jatuh_tempo) {
            return false;
        }

        return now()->isAfter($this->tanggal_jatuh_tempo)
            && !in_array($this->status, ['lunas', 'dibatalkan']);
    }

    /**
     * Get days left until due date (negative if overdue)
     */
    public function daysLeftAttribute(): ?int
    {
        if (!$this->tanggal_jatuh_tempo) {
            return null;
        }

        return now()->diffInDays($this->tanggal_jatuh_tempo, false);
    }

    /**
     * Get payment percentage
     */
    public function getPaymentPercentageAttribute(): float
    {
        if ($this->grand_total <= 0) {
            return 0;
        }

        return ($this->total_dibayar / $this->grand_total) * 100;
    }

    /**
     * Check if due date is near (within 7 days)
     */
    public function isDueSoon(): bool
    {
        $daysLeft = $this->daysLeftAttribute();
        return $daysLeft !== null && $daysLeft > 0 && $daysLeft <= 7;
    }

    /**
     * Scope untuk tagihan yang belum lunas
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['menunggu_pembayaran', 'dibayar_sebagian']);
    }

    /**
     * Scope untuk tagihan overdue
     */
    public function scopeOverdue($query)
    {
        return $query->whereIn('status', ['menunggu_pembayaran', 'dibayar_sebagian'])
            ->where('tanggal_jatuh_tempo', '<', now());
    }

    /**
     * Scope untuk tagihan jatuh tempo dalam X hari
     */
    public function scopeDueWithinDays($query, $days = 7)
    {
        return $query->whereIn('status', ['menunggu_pembayaran', 'dibayar_sebagian'])
            ->whereBetween('tanggal_jatuh_tempo', [
                now(),
                now()->addDays($days)
            ]);
    }

    /**
     * Scope untuk tagihan yang perlu perhatian (overdue atau due soon)
     */
    public function scopeNeedAttention($query)
    {
        return $query->whereIn('status', ['menunggu_pembayaran', 'dibayar_sebagian'])
            ->where(function ($q) {
                $q->where('tanggal_jatuh_tempo', '<', now()) // Overdue
                    ->orWhereBetween('tanggal_jatuh_tempo', [ // Due within 7 days
                        now(),
                        now()->addDays(7)
                    ]);
            });
    }
}
