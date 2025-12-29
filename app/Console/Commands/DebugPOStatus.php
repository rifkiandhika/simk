<?php

namespace App\Console\Commands;

use App\Models\PurchaseOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DebugPOStatus extends Command
{
    protected $signature = 'po:debug {--id= : ID PO untuk debug spesifik}';
    protected $description = 'Debug PO status untuk troubleshooting auto-cancel';

    public function handle()
    {
        $this->info('===========================================');
        $this->info('PO DEBUG MODE');
        $this->info('Current Time: ' . Carbon::now()->format('Y-m-d H:i:s'));
        $this->info('Timezone: ' . config('app.timezone'));
        $this->info('===========================================');
        $this->line('');

        $cutoffTime = Carbon::now()->subHours(24);
        $this->info("Cutoff Time (24h ago): {$cutoffTime->format('Y-m-d H:i:s')}");
        $this->line('');

        // Jika ada ID spesifik
        if ($this->option('id')) {
            $this->debugSpecificPO($this->option('id'), $cutoffTime);
            return Command::SUCCESS;
        }

        // Check SEMUA PO (tidak hanya pending)
        $this->info('=== ALL POs STATUS ===');
        $allPOs = PurchaseOrder::orderBy('created_at', 'desc')->limit(10)->get();

        if ($allPOs->isEmpty()) {
            $this->error('NO POs FOUND IN DATABASE!');
            return Command::FAILURE;
        }

        $this->table(
            ['No PO', 'Status', 'Created At', 'Hours Ago', 'Should Cancel?'],
            $allPOs->map(function ($po) use ($cutoffTime) {
                $hoursAgo = $po->created_at->diffInHours(now());
                $isPending = in_array($po->status, [
                    'menunggu_persetujuan_kepala_gudang',
                    'menunggu_persetujuan_kasir'
                ]);
                $isOld = $po->created_at->lte($cutoffTime);

                return [
                    $po->no_po,
                    $po->status,
                    $po->created_at->format('Y-m-d H:i:s'),
                    $hoursAgo . 'h',
                    ($isPending && $isOld) ? '✓ YES' : '✗ No'
                ];
            })
        );

        $this->line('');

        // Check pending specifically
        $pendingPOs = PurchaseOrder::whereIn('status', [
            'menunggu_persetujuan_kepala_gudang',
            'menunggu_persetujuan_kasir'
        ])->get();

        $this->info("=== PENDING POs ===");
        $this->line("Total Pending: {$pendingPOs->count()}");

        if ($pendingPOs->isEmpty()) {
            $this->warn('No pending POs found!');
            $this->line('');

            // Check cancelled
            $this->info('=== RECENTLY CANCELLED POs ===');
            $cancelled = PurchaseOrder::where('status', 'dibatalkan')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            if ($cancelled->isEmpty()) {
                $this->warn('No cancelled POs found.');
            } else {
                $this->table(
                    ['No PO', 'Created At', 'Cancelled At', 'Reason'],
                    $cancelled->map(fn($po) => [
                        $po->no_po,
                        $po->created_at->format('Y-m-d H:i:s'),
                        $po->tanggal_dibatalkan?->format('Y-m-d H:i:s') ?? '-',
                        $po->catatan_pembatalan ?? '-'
                    ])
                );
            }

            return Command::SUCCESS;
        }

        // Display pending POs detail
        $this->table(
            ['No PO', 'Status', 'Created At', 'Hours Ago', 'Should Cancel?'],
            $pendingPOs->map(function ($po) use ($cutoffTime) {
                $hoursAgo = $po->created_at->diffInHours(now());
                $shouldCancel = $po->created_at->lte($cutoffTime);

                return [
                    $po->no_po,
                    $po->status,
                    $po->created_at->format('Y-m-d H:i:s'),
                    $hoursAgo . 'h',
                    $shouldCancel ? '✓ YES (>24h)' : '✗ No (<24h)'
                ];
            })
        );

        // Count yang harus di-cancel
        $shouldCancel = $pendingPOs->filter(fn($po) => $po->created_at->lte($cutoffTime));

        $this->line('');
        $this->info('===========================================');
        $this->info('SUMMARY:');
        $this->line("Total POs in DB: {$allPOs->count()} (showing last 10)");
        $this->line("Total Pending: {$pendingPOs->count()}");
        $this->line("Should be Cancelled: {$shouldCancel->count()}");
        $this->info('===========================================');

        if ($shouldCancel->count() > 0) {
            $this->line('');
            $this->warn("⚠ There are {$shouldCancel->count()} PO(s) that should be cancelled!");
            $this->line('Run: php artisan po:auto-cancel');
        } else {
            $this->line('');
            $this->info('✓ All pending POs are still within 24 hours.');
        }

        return Command::SUCCESS;
    }

    private function debugSpecificPO($id, $cutoffTime)
    {
        $po = PurchaseOrder::find($id);

        if (!$po) {
            $this->error("PO with ID '{$id}' not found!");
            return;
        }

        $this->info("=== PO DETAILS ===");
        $this->line("No PO: {$po->no_po}");
        $this->line("Status: {$po->status}");
        $this->line("Type: {$po->tipe_po}");
        $this->line("Created At: {$po->created_at->format('Y-m-d H:i:s')}");
        $this->line("Updated At: {$po->updated_at->format('Y-m-d H:i:s')}");
        $this->line('');

        $hoursAgo = $po->created_at->diffInHours(now());
        $this->line("Hours Since Created: {$hoursAgo} hours");
        $this->line('');

        $isPending = in_array($po->status, [
            'menunggu_persetujuan_kepala_gudang',
            'menunggu_persetujuan_kasir'
        ]);

        $isOld = $po->created_at->lte($cutoffTime);

        $this->info("=== CANCEL ELIGIBILITY ===");
        $this->line("Is Pending Status? " . ($isPending ? '✓ YES' : '✗ NO'));
        $this->line("Is Older than 24h? " . ($isOld ? '✓ YES' : '✗ NO'));
        $this->line("Should be Cancelled? " . ($isPending && $isOld ? '✓ YES' : '✗ NO'));
        $this->line('');

        if ($isPending && $isOld) {
            $this->warn("⚠ This PO SHOULD be cancelled by auto-cancel command!");
        } else {
            if (!$isPending) {
                $this->info("ℹ This PO has status '{$po->status}' - not eligible for auto-cancel.");
            }
            if (!$isOld) {
                $this->info("ℹ This PO is only {$hoursAgo}h old - still within 24h grace period.");
            }
        }
    }
}
