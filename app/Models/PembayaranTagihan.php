<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PembayaranTagihan extends Model
{
    use HasUuids;
    protected $table = 'pembayaran_tagihan';
    protected $primaryKey = 'id_pembayaran';
    protected $guarded = [];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'tanggal_approve' => 'datetime',
        'jumlah_bayar' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_pembayaran)) {
                $model->id_pembayaran = (string) Str::uuid();
            }
            if (empty($model->no_pembayaran)) {
                $model->no_pembayaran = self::generateNoPembayaran();
            }
        });
    }

    /**
     * Generate nomor pembayaran: PAY-YYYYMMDD-XXX
     */
    public static function generateNoPembayaran()
    {
        $prefix = 'PAY-' . date('Ymd') . '-';
        $lastPayment = self::where('no_pembayaran', 'like', $prefix . '%')
            ->orderBy('no_pembayaran', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment->no_pembayaran, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Relationships
     */
    public function tagihan()
    {
        return $this->belongsTo(TagihanPo::class, 'id_tagihan', 'id_tagihan');
    }

    public function karyawanInput()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_input', 'id_karyawan');
    }

    public function karyawanApprove()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_approve', 'id_karyawan');
    }

    /**
     * Helpers
     */
    public function isVerified()
    {
        return $this->status_pembayaran === 'diverifikasi';
    }

    public function isPending()
    {
        return $this->status_pembayaran === 'pending';
    }
}
