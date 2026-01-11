<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanPembayaran extends Model
{
    use HasFactory;

    protected $table = 'tagihan_pembayarans';
    protected $guarded = [];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah_bayar' => 'decimal:2',
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
     * Relasi ke Creator (Karyawan yang input pembayaran)
     */
    public function creator()
    {
        return $this->belongsTo(Karyawan::class, 'created_by', 'id_karyawan');
    }

    // ========================================
    // Scopes
    // ========================================
    
    /**
     * Scope untuk pembayaran hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_bayar', today());
    }

    /**
     * Scope untuk pembayaran bulan ini
     */
    public function scopeThisMonth($query)
    {
        return $query->whereYear('tanggal_bayar', now()->year)
                     ->whereMonth('tanggal_bayar', now()->month);
    }

    /**
     * Scope untuk filter berdasarkan metode pembayaran
     */
    public function scopeMetode($query, $metode)
    {
        return $query->where('metode', $metode);
    }

    /**
     * Scope untuk pembayaran tunai
     */
    public function scopeTunai($query)
    {
        return $query->where('metode', 'TUNAI');
    }

    /**
     * Scope untuk pembayaran non-tunai
     */
    public function scopeNonTunai($query)
    {
        return $query->whereIn('metode', ['DEBIT', 'CREDIT', 'TRANSFER']);
    }

    /**
     * Scope untuk pembayaran klaim (BPJS/Asuransi)
     */
    public function scopeKlaim($query)
    {
        return $query->whereIn('metode', ['BPJS', 'ASURANSI']);
    }
    /**
     * Get formatted jumlah bayar
     */
    public function getFormattedJumlahBayarAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_bayar, 0, ',', '.');
    }

    /**
     * Get badge class untuk metode pembayaran
     */
    public function getMetodeBadgeAttribute()
    {
        $badges = [
            'TUNAI' => 'badge-success',
            'DEBIT' => 'badge-info',
            'CREDIT' => 'badge-primary',
            'TRANSFER' => 'badge-warning',
            'BPJS' => 'badge-danger',
            'ASURANSI' => 'badge-secondary',
        ];

        return $badges[$this->metode] ?? 'badge-secondary';
    }

    /**
     * Get label metode pembayaran
     */
    public function getMetodeLabelAttribute()
    {
        $labels = [
            'TUNAI' => 'Tunai',
            'DEBIT' => 'Kartu Debit',
            'CREDIT' => 'Kartu Kredit',
            'TRANSFER' => 'Transfer Bank',
            'BPJS' => 'BPJS Kesehatan',
            'ASURANSI' => 'Asuransi Swasta',
        ];

        return $labels[$this->metode] ?? $this->metode;
    }
    
    /**
     * Check apakah pembayaran tunai
     */
    public function isTunai()
    {
        return $this->metode === 'TUNAI';
    }

    /**
     * Check apakah pembayaran non-tunai
     */
    public function isNonTunai()
    {
        return in_array($this->metode, ['DEBIT', 'CREDIT', 'TRANSFER']);
    }

    /**
     * Check apakah pembayaran dari BPJS/Asuransi
     */
    public function isKlaim()
    {
        return in_array($this->metode, ['BPJS', 'ASURANSI']);
    }

    /**
     * Check apakah memerlukan no referensi (transfer/debit/credit)
     */
    public function requiresReference()
    {
        return in_array($this->metode, ['DEBIT', 'CREDIT', 'TRANSFER', 'BPJS', 'ASURANSI']);
    }
    
    /**
     * Boot method untuk auto-update tagihan setelah pembayaran
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($pembayaran) {
            $tagihan = $pembayaran->tagihan;
            
            if ($tagihan) {
                // Update total dibayar
                $tagihan->total_dibayar += $pembayaran->jumlah_bayar;
                $tagihan->sisa_tagihan = $tagihan->total_tagihan - $tagihan->total_dibayar;
                
                // Update status berdasarkan sisa tagihan
                if ($tagihan->sisa_tagihan <= 0) {
                    $tagihan->status = 'LUNAS';
                    $tagihan->tanggal_lunas = $pembayaran->tanggal_bayar;
                } elseif ($tagihan->total_dibayar > 0) {
                    $tagihan->status = 'CICILAN';
                } else {
                    $tagihan->status = 'BELUM_LUNAS';
                }
                
                $tagihan->save();
            }
        });

        static::deleted(function ($pembayaran) {
            $tagihan = $pembayaran->tagihan;
            
            if ($tagihan) {
                // Kurangi total dibayar
                $tagihan->total_dibayar -= $pembayaran->jumlah_bayar;
                $tagihan->sisa_tagihan = $tagihan->total_tagihan - $tagihan->total_dibayar;
                
                // Update status
                if ($tagihan->total_dibayar <= 0) {
                    $tagihan->status = 'BELUM_LUNAS';
                    $tagihan->tanggal_lunas = null;
                } elseif ($tagihan->sisa_tagihan > 0) {
                    $tagihan->status = 'CICILAN';
                    $tagihan->tanggal_lunas = null;
                }
                
                $tagihan->save();
            }
        });

        static::updated(function ($pembayaran) {
            $tagihan = $pembayaran->tagihan;
            
            if ($tagihan) {
                // Recalculate total dari semua pembayaran
                $totalDibayar = $tagihan->pembayarans()->sum('jumlah_bayar');
                $tagihan->total_dibayar = $totalDibayar;
                $tagihan->sisa_tagihan = $tagihan->total_tagihan - $totalDibayar;
                
                // Update status
                if ($tagihan->sisa_tagihan <= 0) {
                    $tagihan->status = 'LUNAS';
                    if (!$tagihan->tanggal_lunas) {
                        $tagihan->tanggal_lunas = $pembayaran->tanggal_bayar;
                    }
                } elseif ($totalDibayar > 0) {
                    $tagihan->status = 'CICILAN';
                    $tagihan->tanggal_lunas = null;
                } else {
                    $tagihan->status = 'BELUM_LUNAS';
                    $tagihan->tanggal_lunas = null;
                }
                
                $tagihan->save();
            }
        });
    }
    /**
     * Validate jumlah bayar tidak melebihi sisa tagihan
     */
    public function validateAmount()
    {
        $tagihan = $this->tagihan;
        
        if (!$tagihan) {
            return false;
        }

        // Jika update, kurangi jumlah bayar lama dari sisa tagihan
        if ($this->exists) {
            $sisaTagihan = $tagihan->sisa_tagihan + $this->getOriginal('jumlah_bayar');
        } else {
            $sisaTagihan = $tagihan->sisa_tagihan;
        }

        return $this->jumlah_bayar <= $sisaTagihan;
    }

    /**
     * Get struk pembayaran (bisa digunakan untuk print)
     */
    public function getStrukData()
    {
        return [
            'no_kwitansi' => 'KWT/' . date('Ymd', strtotime($this->tanggal_bayar)) . '/' . str_pad($this->id, 4, '0', STR_PAD_LEFT),
            'tanggal' => $this->tanggal_bayar->format('d/m/Y'),
            'waktu' => $this->created_at->format('H:i:s'),
            'pasien' => $this->tagihan->pasien->nama_lengkap ?? '-',
            'no_rm' => $this->tagihan->pasien->no_rm ?? '-',
            'no_tagihan' => $this->tagihan->no_tagihan,
            'jumlah_bayar' => $this->jumlah_bayar,
            'metode' => $this->metode_label,
            'no_referensi' => $this->no_referensi,
            'keterangan' => $this->keterangan,
            'kasir' => $this->creator->nama ?? '-',
        ];
    }
}