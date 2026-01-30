<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PoProof extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'po_proofs';
    protected $primaryKey = 'id_po_proof';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_po',
        'tipe_bukti',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'id_karyawan_upload',
        'tanggal_upload',
        'catatan',
        'is_active',
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
        'is_active' => 'boolean',
        'file_size' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'id_po', 'id_po');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_upload', 'id_karyawan');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInvoice($query)
    {
        return $query->where('tipe_bukti', 'invoice');
    }

    public function scopeBarang($query)
    {
        return $query->where('tipe_bukti', 'barang');
    }

    // Accessors
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) return '-';
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}