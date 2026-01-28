<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ReturDocument extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'retur_documents';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    /**
     * Relasi ke retur
     */
    public function retur(): BelongsTo
    {
        return $this->belongsTo(Retur::class, 'id_retur', 'id_retur');
    }

    /**
     * Relasi ke karyawan uploader
     */
    public function karyawanUpload(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_upload', 'id_karyawan');
    }

    /**
     * Get full URL dokumen
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Delete file saat model dihapus
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if (Storage::exists($model->file_path)) {
                Storage::delete($model->file_path);
            }
        });
    }
}