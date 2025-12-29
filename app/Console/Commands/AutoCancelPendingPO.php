<?php

namespace App\Console\Commands;

use App\Models\DetailSupplier;
use App\Models\PoAuditTrail;
use App\Models\PurchaseOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoCancelPendingPO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'po:auto-cancel 
                            {--force : Force cancel without confirmation}
                            {--dry-run : Show what would be cancelled without actually cancelling}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto cancel PO yang menunggu approval lebih dari 24 jam';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('===========================================');
        $this->info('Auto-Cancel PO Process Started');
        $this->info('Current Time: ' . Carbon::now()->format('Y-m-d H:i:s'));
        $this->info('Timezone: ' . config('app.timezone'));
        $this->info('===========================================');
        $this->line('');

        $isDryRun = $this->option('dry-run');
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->line('');
        }

        $cutoffTime = Carbon::now()->subHours(24);
        $this->info("Cutoff Time: {$cutoffTime->format('Y-m-d H:i:s')}");
        $this->line('');

        $cancelledCount = 0;
        $failedCount = 0;

        // 1. Cancel PO menunggu approval kepala gudang > 24 jam
        $this->info('Checking PO pending Kepala Gudang approval...');
        $kepalaGudangPOs = PurchaseOrder::where('status', 'menunggu_persetujuan_kepala_gudang')
            ->where('created_at', '<=', $cutoffTime)
            ->get();

        $this->info("Found: {$kepalaGudangPOs->count()} PO(s)");

        foreach ($kepalaGudangPOs as $po) {
            if ($isDryRun) {
                $this->line("Would cancel: {$po->no_po} (Created: {$po->created_at->format('Y-m-d H:i:s')})");
                $cancelledCount++;
            } else {
                $result = $this->cancelPO($po, 'kepala_gudang');
                if ($result) {
                    $cancelledCount++;
                } else {
                    $failedCount++;
                }
            }
        }

        $this->line('');

        // 2. Cancel PO menunggu approval kasir > 24 jam
        $this->info('Checking PO pending Kasir approval...');
        $kasirPOs = PurchaseOrder::where('status', 'menunggu_persetujuan_kasir')
            ->where('created_at', '<=', $cutoffTime)
            ->get();

        $this->info("Found: {$kasirPOs->count()} PO(s)");

        foreach ($kasirPOs as $po) {
            if ($isDryRun) {
                $this->line("Would cancel: {$po->no_po} (Created: {$po->created_at->format('Y-m-d H:i:s')})");
                $cancelledCount++;
            } else {
                $result = $this->cancelPO($po, 'kasir');
                if ($result) {
                    $cancelledCount++;
                } else {
                    $failedCount++;
                }
            }
        }

        $this->line('');
        $this->info('===========================================');
        $this->info("Process Completed!");

        if ($isDryRun) {
            $this->info("Would cancel: {$cancelledCount} PO(s)");
        } else {
            $this->info("✓ Successfully cancelled: {$cancelledCount} PO(s)");
            if ($failedCount > 0) {
                $this->error("✗ Failed: {$failedCount} PO(s)");
            }
        }

        $this->info('===========================================');

        Log::info('[AutoCancelPO] Process completed', [
            'cancelled' => $cancelledCount,
            'failed' => $failedCount,
            'dry_run' => $isDryRun,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);

        return Command::SUCCESS;
    }

    /**
     * Cancel individual PO with proper transaction and audit trail
     */
    private function cancelPO(PurchaseOrder $po, string $role)
    {
        DB::beginTransaction();
        try {
            $dataBefore = $po->toArray();

            // Determine catatan field based on role
            $catatanField = $role === 'kepala_gudang' ? 'catatan_kepala_gudang' : 'catatan_kasir';
            $statusField = $role === 'kepala_gudang' ? 'status_approval_kepala_gudang' : 'status_approval_kasir';

            // Update PO
            $po->update([
                'status' => 'dibatalkan',
                $catatanField => 'Otomatis dibatalkan oleh sistem karena tidak ada approval dalam 24 jam',
                $statusField => 'ditolak',
                'tanggal_dibatalkan' => now(),
                'catatan_pembatalan' => "Auto-cancelled after 24 hours waiting for {$role} approval"
            ]);

            // Reset stock_po untuk PO eksternal
            if ($po->tipe_po === 'eksternal') {
                foreach ($po->items as $item) {
                    $produk = DetailSupplier::find($item->id_produk);
                    if ($produk) {
                        $currentStockPo = $produk->stock_po;
                        $newStockPo = max(0, $currentStockPo - $item->qty_diminta);

                        $produk->update(['stock_po' => $newStockPo]);

                        $this->line("  - Reset stock_po {$produk->nama}: {$currentStockPo} → {$newStockPo}");
                    }
                }
            }

            $idSystem = '00000000-0000-0000-0000-000000000000';

            // Create audit trail
            PoAuditTrail::create([
                'id_po' => $po->id_po,
                'id_karyawan' => $idSystem, // System action
                'pin_karyawan' => 'SYSTEM',
                'aksi' => 'auto_cancel',
                'deskripsi_aksi' => "PO dibatalkan otomatis oleh sistem karena tidak ada approval {$role} dalam 24 jam. Created: {$po->created_at->format('Y-m-d H:i:s')}, Cancelled: " . now()->format('Y-m-d H:i:s'),
                'data_sebelum' => $dataBefore,
                'data_sesudah' => $po->fresh()->toArray(),
            ]);

            DB::commit();

            $createdAt = $po->created_at->format('Y-m-d H:i:s');
            $hoursAgo = $po->created_at->diffInHours(now());

            $this->info("✓ Cancelled: {$po->no_po}");
            $this->line("  Role: " . ucfirst($role));
            $this->line("  Created: {$createdAt} ({$hoursAgo}h ago)");
            $this->line("  Type: " . ucfirst($po->tipe_po));

            Log::info("PO auto-cancelled successfully", [
                'no_po' => $po->no_po,
                'id_po' => $po->id_po,
                'role' => $role,
                'created_at' => $createdAt,
                'hours_pending' => $hoursAgo
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->error("✗ Failed to cancel: {$po->no_po}");
            $this->error("  Error: {$e->getMessage()}");

            Log::error("Failed to auto-cancel PO", [
                'no_po' => $po->no_po,
                'id_po' => $po->id_po,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
}
