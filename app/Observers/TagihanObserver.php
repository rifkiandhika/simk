<?php

namespace App\Observers;

use App\Models\Tagihan;

class TagihanObserver
{
    /**
     * Handle sebelum save
     */
    public function saving(Tagihan $tagihan)
    {
        // Auto-calculate sisa tagihan
        $tagihan->sisa_tagihan = $tagihan->total_tagihan - $tagihan->total_dibayar;

        // Pastikan tidak negatif
        if ($tagihan->sisa_tagihan < 0) {
            $tagihan->sisa_tagihan = 0;
        }

        // Auto-update status berdasarkan sisa tagihan
        if ($tagihan->sisa_tagihan <= 0 && $tagihan->total_tagihan > 0) {
            $tagihan->status = 'LUNAS';
            if (!$tagihan->tanggal_lunas) {
                $tagihan->tanggal_lunas = now();
            }
        } elseif ($tagihan->total_dibayar > 0 && $tagihan->sisa_tagihan > 0) {
            $tagihan->status = 'CICILAN';
        } else {
            $tagihan->status = 'BELUM_LUNAS';
            $tagihan->tanggal_lunas = null;
        }
    }

    /**
     * Handle setelah create
     */
    public function created(Tagihan $tagihan)
    {
        // Generate nomor tagihan jika belum ada
        if (empty($tagihan->no_tagihan)) {
            $tagihan->no_tagihan = $this->generateNoTagihan($tagihan);
            $tagihan->saveQuietly(); // Save tanpa trigger observer lagi
        }
    }

    /**
     * Handle sebelum update
     */
    public function updating(Tagihan $tagihan)
    {
        // Cek jika tagihan sudah locked
        if ($tagihan->locked && $tagihan->isDirty()) {
            // Allow update hanya untuk field tertentu
            $allowedFields = ['locked', 'locked_at', 'locked_by', 'catatan'];
            $dirtyFields = array_keys($tagihan->getDirty());

            $notAllowedChanges = array_diff($dirtyFields, $allowedFields);

            if (!empty($notAllowedChanges)) {
                throw new \Exception('Tagihan sudah dikunci. Tidak dapat diubah.');
            }
        }
    }

    /**
     * Handle sebelum delete
     */
    public function deleting(Tagihan $tagihan)
    {
        if ($tagihan->locked) {
            throw new \Exception('Tagihan sudah dikunci. Tidak dapat dihapus.');
        }

        if ($tagihan->total_dibayar > 0) {
            throw new \Exception('Tagihan sudah ada pembayaran. Tidak dapat dihapus.');
        }
    }

    /**
     * Generate nomor tagihan
     */
    protected function generateNoTagihan(Tagihan $tagihan)
    {
        $prefix = 'TGH';
        $date = $tagihan->tanggal_tagihan->format('Ymd');
        $jenis = substr($tagihan->jenis_tagihan, 0, 2); // IG, RA, RW

        // Get counter untuk hari ini
        $lastNo = Tagihan::whereDate('tanggal_tagihan', $tagihan->tanggal_tagihan)
            ->where('jenis_tagihan', $tagihan->jenis_tagihan)
            ->count();

        $counter = str_pad($lastNo + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefix}/{$date}/{$jenis}/{$counter}";
    }
}
