<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\Tagihan;
use App\Models\TagihanPembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class TagihanPembayaranService
{
    /**
     * Proses pembayaran tagihan dengan transaction dan locking
     */
   public function prosesPembayaran(array $data): array
    {
        return DB::transaction(function () use ($data) {

            // 1. Ambil & lock tagihan
            $tagihan = Tagihan::where('id_tagihan', $data['id_tagihan'])
                ->lockForUpdate()
                ->firstOrFail();

            // 2. Validasi sisa tagihan
            if ($data['jumlah_bayar'] > $tagihan->sisa_tagihan) {
                throw new \Exception('Jumlah bayar melebihi sisa tagihan');
            }

            // 3. Verifikasi PIN → ambil karyawan
            $karyawan = Karyawan::where('pin', $data['pin'])
                ->where('status_aktif', 'Aktif')
                ->first();

            if (!$karyawan) {
                throw new \Exception('PIN tidak valid atau karyawan tidak aktif');
            }

            // 4. Simpan pembayaran
            $pembayaran = TagihanPembayaran::create([
                'id_tagihan'    => $tagihan->id_tagihan,
                'tanggal_bayar' => $data['tanggal_bayar'] ?? now(),
                'jumlah_bayar'  => $data['jumlah_bayar'],
                'metode'        => $data['metode'],
                'no_referensi'  => $data['no_referensi'] ?? null,
                'keterangan'    => $data['keterangan'] ?? null,
                'created_by'    => $karyawan->id_karyawan,
            ]);

            // 5. Hitung ulang dari database (AMAN)
            $totalDibayar = TagihanPembayaran::where('id_tagihan', $tagihan->id_tagihan)
                ->sum('jumlah_bayar');

            $sisaTagihan = $tagihan->total_tagihan - $totalDibayar;

            // 6. Tentukan status
            if ($sisaTagihan <= 0) {
                $status = 'LUNAS';
                $tanggalLunas = now();
                $sisaTagihan = 0;
            } elseif ($totalDibayar > 0) {
                $status = 'CICILAN';
                $tanggalLunas = null;
            } else {
                $status = 'BELUM_LUNAS';
                $tanggalLunas = null;
            }

            // 7. Update tagihan
            $tagihan->update([
                'total_dibayar' => $totalDibayar,
                'sisa_tagihan'  => $sisaTagihan,
                'status'        => $status,
                'tanggal_lunas' => $tanggalLunas,
                'id_karyawan'   => $karyawan->id_karyawan,
            ]);

            return [
                'tagihan'    => $tagihan->fresh(),
                'pembayaran' => $pembayaran,
                'karyawan'   => $karyawan->nama_lengkap,
            ];
        });
    }

    /**
     * Verifikasi PIN karyawan
     */
    private function verifyPin(string $pin): Karyawan
    {
        $karyawan = Karyawan::where('pin', $pin)
            ->where('status_aktif', 'Aktif')
            ->first();

        if (!$karyawan) {
            throw new \Exception('PIN tidak valid atau karyawan tidak aktif');
        }

        return $karyawan;
    }

    /**
     * Validasi pembayaran
     */
    protected function validatePembayaran(Tagihan $tagihan, array $data)
    {
        // Cek apakah tagihan sudah di-lock
        if ($tagihan->locked) {
            throw new Exception('Tagihan sudah dikunci. Tidak dapat melakukan pembayaran.');
        }

        // Cek apakah tagihan sudah lunas
        if ($tagihan->status === 'LUNAS') {
            throw new Exception('Tagihan sudah lunas.');
        }

        // Cek jumlah pembayaran
        if ($data['jumlah_bayar'] <= 0) {
            throw new Exception('Jumlah pembayaran harus lebih dari 0.');
        }

        // Cek apakah pembayaran melebihi sisa tagihan
        if ($data['jumlah_bayar'] > $tagihan->sisa_tagihan) {
            throw new Exception(
                "Jumlah pembayaran (Rp " . number_format($data['jumlah_bayar'], 0, ',', '.') .
                    ") melebihi sisa tagihan (Rp " . number_format($tagihan->sisa_tagihan, 0, ',', '.') . ")"
            );
        }

        // Validasi metode pembayaran
        $allowedMethods = ['TUNAI', 'DEBIT', 'CREDIT', 'TRANSFER', 'BPJS', 'ASURANSI'];
        if (!in_array($data['metode'], $allowedMethods)) {
            throw new Exception('Metode pembayaran tidak valid.');
        }

        // Validasi no_referensi untuk metode tertentu
        if (in_array($data['metode'], ['DEBIT', 'CREDIT', 'TRANSFER', 'BPJS', 'ASURANSI'])) {
            if (empty($data['no_referensi'])) {
                throw new Exception("No. Referensi wajib diisi untuk metode pembayaran {$data['metode']}.");
            }
        }
    }

    /**
     * Update data tagihan setelah pembayaran
     */
    protected function updateTagihan(Tagihan $tagihan)
    {
        // Hitung ulang total dibayar dari database
        $totalDibayar = TagihanPembayaran::where('id_tagihan', $tagihan->id_tagihan)
            ->sum('jumlah_bayar');

        $sisaTagihan = $tagihan->total_tagihan - $totalDibayar;

        // Tentukan status
        $status = 'BELUM_LUNAS';
        $tanggalLunas = null;

        if ($sisaTagihan <= 0) {
            $status = 'LUNAS';
            $tanggalLunas = now();
        } elseif ($totalDibayar > 0) {
            $status = 'CICILAN';
        }

        // Update tagihan
        $tagihan->update([
            'total_dibayar' => $totalDibayar,
            'sisa_tagihan' => max(0, $sisaTagihan), // Pastikan tidak negatif
            'status' => $status,
            'tanggal_lunas' => $tanggalLunas,
        ]);
    }

    /**
     * Batalkan pembayaran (void payment)
     */
    public function batalkanPembayaran(int $idPembayaran, string $alasan)
    {
        return DB::transaction(function () use ($idPembayaran, $alasan) {
            // 1. Get pembayaran dengan lock
            $pembayaran = TagihanPembayaran::lockForUpdate()
                ->findOrFail($idPembayaran);

            // 2. Lock tagihan
            $tagihan = Tagihan::where('id_tagihan', $pembayaran->id_tagihan)
                ->lockForUpdate()
                ->firstOrFail();

            // 3. Validasi
            if ($tagihan->locked) {
                throw new Exception('Tagihan sudah dikunci. Tidak dapat membatalkan pembayaran.');
            }

            // Cek apakah pembayaran sudah terlalu lama (misalnya > 7 hari)
            if ($pembayaran->created_at->diffInDays(now()) > 7) {
                throw new Exception('Pembayaran tidak dapat dibatalkan setelah 7 hari.');
            }

            // 4. Log sebelum delete (penting untuk audit)
            $this->logPembatalanPembayaran($pembayaran, $alasan);

            // 5. Hapus pembayaran
            $pembayaran->delete();

            // 6. Update tagihan
            $this->updateTagihan($tagihan);

            return [
                'success' => true,
                'message' => 'Pembayaran berhasil dibatalkan',
                'tagihan' => $tagihan->fresh()
            ];
        });
    }

    /**
     * Lock tagihan (untuk proses closing/audit)
     */
    public function lockTagihan(int $idTagihan)
    {
        return DB::transaction(function () use ($idTagihan) {
            $tagihan = Tagihan::lockForUpdate()->findOrFail($idTagihan);

            if ($tagihan->locked) {
                throw new Exception('Tagihan sudah dalam status locked.');
            }

            if ($tagihan->status !== 'LUNAS') {
                throw new Exception('Hanya tagihan dengan status LUNAS yang dapat di-lock.');
            }

            $tagihan->update([
                'locked' => true,
                'locked_at' => now(),
                'locked_by' => Auth::user()->id_karyawan,
            ]);

            $this->logActivity($tagihan, null, 'LOCK');

            return [
                'success' => true,
                'message' => 'Tagihan berhasil dikunci',
                'tagihan' => $tagihan
            ];
        });
    }

    /**
     * Unlock tagihan (hanya untuk role tertentu)
     */
    public function unlockTagihan(int $idTagihan, string $alasan)
    {
        // Cek permission (implementasikan sesuai kebutuhan)
        if (!Auth::user()->hasRole(['admin', 'supervisor_keuangan'])) {
            throw new Exception('Anda tidak memiliki akses untuk unlock tagihan.');
        }

        return DB::transaction(function () use ($idTagihan, $alasan) {
            $tagihan = Tagihan::lockForUpdate()->findOrFail($idTagihan);

            if (!$tagihan->locked) {
                throw new Exception('Tagihan tidak dalam status locked.');
            }

            $tagihan->update([
                'locked' => false,
                'locked_at' => null,
                'locked_by' => null,
            ]);

            $this->logActivity($tagihan, null, 'UNLOCK', $alasan);

            return [
                'success' => true,
                'message' => 'Tagihan berhasil di-unlock',
                'tagihan' => $tagihan
            ];
        });
    }

    /**
     * Get ringkasan pembayaran
     */
    public function getRingkasanPembayaran(int $idTagihan)
    {
        $tagihan = Tagihan::with(['pembayarans', 'items'])->findOrFail($idTagihan);

        return [
            'no_tagihan' => $tagihan->no_tagihan,
            'total_tagihan' => $tagihan->total_tagihan,
            'total_dibayar' => $tagihan->total_dibayar,
            'sisa_tagihan' => $tagihan->sisa_tagihan,
            'status' => $tagihan->status,
            'jumlah_pembayaran' => $tagihan->pembayarans->count(),
            'riwayat_pembayaran' => $tagihan->pembayarans->map(function ($p) {
                return [
                    'tanggal' => $p->tanggal_bayar,
                    'jumlah' => $p->jumlah_bayar,
                    'metode' => $p->metode,
                    'no_referensi' => $p->no_referensi,
                    'petugas' => $p->creator->nama ?? '-',
                ];
            }),
            'detail_items' => $tagihan->items->groupBy('kategori')->map(function ($items, $kategori) {
                return [
                    'kategori' => $kategori,
                    'subtotal' => $items->sum('subtotal'),
                    'items' => $items->map(fn($i) => [
                        'deskripsi' => $i->deskripsi,
                        'qty' => $i->qty,
                        'harga' => $i->harga,
                        'subtotal' => $i->subtotal,
                        'ditanggung' => $i->ditanggung,
                    ])
                ];
            })
        ];
    }

    /**
     * Log activity untuk audit trail
     */
    protected function logActivity(Tagihan $tagihan, $pembayaran = null, string $action = 'PAYMENT', string $notes = null)
    {
        // Implementasi logging sesuai kebutuhan
        // Bisa menggunakan activity log package seperti spatie/laravel-activitylog
        // atau custom logging table

        activity()
            ->performedOn($tagihan)
            ->causedBy(Auth::user())
            ->withProperties([
                'action' => $action,
                'pembayaran_id' => $pembayaran?->id,
                'jumlah_bayar' => $pembayaran?->jumlah_bayar,
                'total_dibayar' => $tagihan->total_dibayar,
                'sisa_tagihan' => $tagihan->sisa_tagihan,
                'status' => $tagihan->status,
                'notes' => $notes,
            ])
            ->log($action);
    }

    /**
     * Log pembatalan pembayaran
     */
    protected function logPembatalanPembayaran(TagihanPembayaran $pembayaran, string $alasan)
    {
        activity()
            ->performedOn($pembayaran)
            ->causedBy(Auth::user())
            ->withProperties([
                'action' => 'VOID_PAYMENT',
                'id_tagihan' => $pembayaran->id_tagihan,
                'jumlah_bayar' => $pembayaran->jumlah_bayar,
                'metode' => $pembayaran->metode,
                'alasan' => $alasan,
            ])
            ->log('VOID_PAYMENT');
    }
}
