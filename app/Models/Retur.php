<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Retur extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'returs';
    protected $primaryKey = 'id_retur';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'tanggal_retur' => 'date',
        'tanggal_approval' => 'datetime',
        'tanggal_diproses' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'total_nilai_retur' => 'decimal:2',
    ];

    // Boot method untuk generate no_retur otomatis
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_retur)) {
                $model->no_retur = self::generateNoRetur();
            }
        });
    }

    /**
     * Generate nomor retur otomatis
     */
    public static function generateNoRetur(): string
    {
        $date = now()->format('Ymd');
        $prefix = 'RTR-' . $date . '-';
        
        $lastRetur = self::where('no_retur', 'like', $prefix . '%')
            ->orderBy('no_retur', 'desc')
            ->first();

        if ($lastRetur) {
            $lastNumber = intval(substr($lastRetur->no_retur, -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Relasi ke Purchase Order (jika tipe_retur = 'po')
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'id_sumber', 'id_po');
    }

    /**
     * Relasi ke Stock Apotik (jika tipe_retur = 'stock_apotik')
     */
    public function stockApotik(): BelongsTo
    {
        return $this->belongsTo(StockApotik::class, 'id_sumber', 'id');
    }

    /**
     * Get sumber (PO atau Stock Apotik) secara dinamis
     */
    public function sumber()
    {
        if ($this->tipe_retur === 'po') {
            return $this->purchaseOrder();
        } elseif ($this->tipe_retur === 'stock_apotik') {
            return $this->stockApotik();
        }
        return null;
    }

    /**
     * Relasi ke karyawan pelapor
     */
    public function karyawanPelapor(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_pelapor', 'id_karyawan');
    }

    /**
     * Relasi ke karyawan approval
     */
    public function karyawanApproval(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_approval', 'id_karyawan');
    }

    /**
     * Relasi ke karyawan pemroses
     */
    public function karyawanPemroses(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_pemroses', 'id_karyawan');
    }

    /**
     * Relasi ke supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
    }

    /**
     * Relasi ke retur items
     */
    public function returItems(): HasMany
    {
        return $this->hasMany(ReturItem::class, 'id_retur', 'id_retur');
    }

    /**
     * Relasi ke retur histories
     */
    public function histories(): HasMany
    {
        return $this->hasMany(ReturHistory::class, 'id_retur', 'id_retur');
    }

    /**
     * Relasi ke retur documents
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ReturDocument::class, 'id_retur', 'id_retur');
    }

    /**
     * Scope untuk filter berdasarkan tipe retur
     */
    public function scopeTipePo($query)
    {
        return $query->where('tipe_retur', 'po');
    }

    public function scopeTipeStockApotik($query)
    {
        return $query->where('tipe_retur', 'stock_apotik');
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Check apakah retur dapat diedit
     */
    public function canEdit(): bool
    {
        return in_array($this->status, ['draft', 'ditolak']);
    }

    /**
     * Check apakah retur dapat diapprove
     */
    public function canApprove(): bool
    {
        return $this->status === 'menunggu_persetujuan';
    }

    /**
     * Check apakah retur dapat diproses
     */
    public function canProcess(): bool
    {
        return $this->status === 'disetujui';
    }

    /**
     * Hitung total nilai retur dari items
     */
    public function calculateTotal(): float
    {
        return $this->returItems->sum('subtotal_retur');
    }

    /**
     * Update total nilai retur
     */
    public function updateTotal(): void
    {
        $this->total_nilai_retur = $this->calculateTotal();
        $this->save();
    }
}