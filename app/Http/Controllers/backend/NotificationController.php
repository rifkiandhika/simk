<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\TagihanPo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Auto-cancel PO yang belum disetujui dalam 24 jam
     */
    public function autoCancelPendingPO()
    {
        $oneDayAgo = Carbon::now()->subDay();

        // Cancel PO yang menunggu approval kepala gudang > 24 jam
        PurchaseOrder::where('status', 'menunggu_persetujuan_kepala_gudang')
            ->where('created_at', '<=', $oneDayAgo)
            ->update([
                'status' => 'dibatalkan',
                'catatan_kepala_gudang' => 'Otomatis dibatalkan karena tidak disetujui dalam 24 jam',
                'status_approval_kepala_gudang' => 'ditolak'
            ]);

        // Cancel PO yang menunggu approval kasir > 24 jam
        PurchaseOrder::where('status', 'menunggu_persetujuan_kasir')
            ->where('tanggal_approval_kepala_gudang', '<=', $oneDayAgo)
            ->update([
                'status' => 'dibatalkan',
                'catatan_kasir' => 'Otomatis dibatalkan karena tidak disetujui dalam 24 jam',
                'status_approval_kasir' => 'ditolak'
            ]);

        return response()->json(['message' => 'Auto-cancel process completed']);
    }

    /**
     * Get notifications data berdasarkan role user
     */
    public function getNotifications()
    {
        $user = Auth::user();
        $notifications = [];

        // 1. PO Draft (untuk pembuat/pemohon)
        $draftPO = PurchaseOrder::where('status', 'draft')
            ->where('id_karyawan_pemohon', $user->id_karyawan)
            ->select('id_po', 'no_po', 'tanggal_permintaan', 'grand_total', 'unit_pemohon')
            ->orderBy('created_at', 'desc')
            ->get();

        $notifications['draft'] = [
            'count' => $draftPO->count(),
            'items' => $draftPO,
            'label' => 'PO Draft',
            'icon' => 'ri-draft-line',
            'color' => 'secondary'
        ];

        // 2. PO Menunggu Approval Kepala Gudang
        if ($user->hasAnyRole(['Superadmin', 'admin', 'kepala_gudang'])) {
            $pendingKepalaGudang = PurchaseOrder::where('status', 'menunggu_persetujuan_kepala_gudang')
                ->where('created_at', '>', Carbon::now()->subDay()) // Hanya yang belum 24 jam
                ->select('id_po', 'no_po', 'tanggal_permintaan', 'grand_total', 'unit_pemohon', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($po) {
                    $po->deadline = Carbon::parse($po->created_at)->addDay();
                    $po->hours_left = now()->diffInHours($po->deadline, false);
                    return $po;
                });

            $notifications['pending_kepala_gudang'] = [
                'count' => $pendingKepalaGudang->count(),
                'items' => $pendingKepalaGudang,
                'label' => 'Menunggu Approval Kepala Gudang',
                'icon' => 'ri-time-line',
                'color' => 'warning'
            ];
        }

        // 3. PO Menunggu Approval Kasir
        if ($user->hasAnyRole(['Superadmin', 'admin', 'kasir'])) {
            $pendingKasir = PurchaseOrder::where('status', 'menunggu_persetujuan_kasir')
                ->where('tanggal_approval_kepala_gudang', '>', Carbon::now()->subDay())
                ->select('id_po', 'no_po', 'tanggal_permintaan', 'grand_total', 'unit_pemohon', 'tanggal_approval_kepala_gudang')
                ->orderBy('tanggal_approval_kepala_gudang', 'desc')
                ->get()
                ->map(function ($po) {
                    $po->deadline = Carbon::parse($po->tanggal_approval_kepala_gudang)->addDay();
                    $po->hours_left = now()->diffInHours($po->deadline, false);
                    return $po;
                });

            $notifications['pending_kasir'] = [
                'count' => $pendingKasir->count(),
                'items' => $pendingKasir,
                'label' => 'Menunggu Approval Kasir',
                'icon' => 'ri-time-line',
                'color' => 'warning'
            ];
        }

        // 4. PO Disetujui (untuk pembuat)
        $approvedPO = PurchaseOrder::where('status', 'disetujui')
            ->where('id_karyawan_pemohon', $user->id_karyawan)
            ->select('id_po', 'no_po', 'tanggal_permintaan', 'grand_total', 'tanggal_approval_kasir')
            ->orderBy('tanggal_approval_kasir', 'desc')
            ->limit(5)
            ->get();

        $notifications['approved'] = [
            'count' => $approvedPO->count(),
            'items' => $approvedPO,
            'label' => 'PO Disetujui',
            'icon' => 'ri-checkbox-circle-line',
            'color' => 'success'
        ];

        // 5. Tagihan Menunggu Pembayaran (untuk finance/kasir)
        if ($user->hasAnyRole(['Superadmin', 'admin', 'kepala_gudang'])) {
            $pendingPayment = TagihanPO::whereIn('status', ['menunggu_pembayaran', 'dibayar_sebagian'])
                ->with(['purchaseOrder:id_po,no_po,no_gr', 'supplier:id,nama_supplier'])
                ->select('id_tagihan', 'no_tagihan', 'id_po', 'id_supplier', 'grand_total', 'sisa_tagihan', 'tanggal_jatuh_tempo', 'status')
                ->orderBy('tanggal_jatuh_tempo', 'asc')
                ->get()
                ->map(function ($tagihan) {
                    $tagihan->is_overdue = $tagihan->tanggal_jatuh_tempo &&
                        now()->isAfter($tagihan->tanggal_jatuh_tempo);
                    $tagihan->days_left = $tagihan->tanggal_jatuh_tempo ?
                        now()->diffInDays($tagihan->tanggal_jatuh_tempo, false) : null;
                    return $tagihan;
                });

            $notifications['pending_payment'] = [
                'count' => $pendingPayment->count(),
                'items' => $pendingPayment,
                'label' => 'Menunggu Pembayaran',
                'icon' => 'ri-money-dollar-circle-line',
                'color' => 'info'
            ];

            // 6. Tagihan Jatuh Tempo (Overdue)
            $overdueTagihan = $pendingPayment->filter(function ($t) {
                return $t->is_overdue;
            })->values();

            $notifications['overdue'] = [
                'count' => $overdueTagihan->count(),
                'items' => $overdueTagihan,
                'label' => 'Tagihan Jatuh Tempo',
                'icon' => 'ri-alert-line',
                'color' => 'danger'
            ];
        }

        // 7. Tagihan Lunas (untuk semua yang terlibat)
        $completedTagihan = TagihanPo::where('status', 'lunas')
            ->with(['purchaseOrder:id_po,no_po,no_gr', 'supplier:id,nama_supplier'])
            ->whereHas('purchaseOrder', function ($q) use ($user) {
                $q->where('id_karyawan_pemohon', $user->id_karyawan);
            })
            ->select('id_tagihan', 'no_tagihan', 'id_po', 'id_supplier', 'grand_total', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();

        $notifications['completed'] = [
            'count' => $completedTagihan->count(),
            'items' => $completedTagihan,
            'label' => 'Tagihan Lunas',
            'icon' => 'ri-check-double-line',
            'color' => 'success'
        ];

        // 8. PO/Tagihan Dibatalkan (untuk pembuat)
        $cancelledPO = PurchaseOrder::where('status', 'dibatalkan')
            ->where('id_karyawan_pemohon', $user->id_karyawan)
            ->where('updated_at', '>', Carbon::now()->subDays(3)) // 3 hari terakhir
            ->select('id_po', 'no_po', 'tanggal_permintaan', 'grand_total', 'updated_at', 'catatan_kepala_gudang', 'catatan_kasir')
            ->orderBy('updated_at', 'desc')
            ->get();

        $notifications['cancelled'] = [
            'count' => $cancelledPO->count(),
            'items' => $cancelledPO,
            'label' => 'PO Dibatalkan',
            'icon' => 'ri-close-circle-line',
            'color' => 'danger'
        ];

        // Total badge count (hanya yang butuh action)
        $totalBadge = 0;
        if (isset($notifications['draft'])) $totalBadge += $notifications['draft']['count'];
        if (isset($notifications['pending_kepala_gudang'])) $totalBadge += $notifications['pending_kepala_gudang']['count'];
        if (isset($notifications['pending_kasir'])) $totalBadge += $notifications['pending_kasir']['count'];
        if (isset($notifications['pending_payment'])) $totalBadge += $notifications['pending_payment']['count'];
        if (isset($notifications['overdue'])) $totalBadge += $notifications['overdue']['count'];

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'total_badge' => $totalBadge
        ]);
    }

    /**
     * Mark notification as read (optional feature)
     */
    public function markAsRead(Request $request)
    {
        // Implementasi jika perlu tracking read/unread
        return response()->json(['success' => true]);
    }
}
