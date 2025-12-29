<?php

namespace App\Console\Commands;

use App\Models\PurchaseOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckPOStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'po:check-status {--pending : Show only pending POs} {--old : Show only POs > 24 hours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check status PO dan lihat mana yang perlu dibatalkan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('===========================================');
        $this->info('PO Status Check');
        $this->info('Current Time: ' . Carbon::now()->format('Y-m-d H:i:s'));
        $this->info('Timezone: ' . config('app.timezone'));
        $this->info('===========================================');
        $this->line('');

        $cutoffTime = Carbon::now()->subHours(24);

        // Query base
        $query = PurchaseOrder::query();

        if ($this->option('pending')) {
            $query->whereIn('status', [
                'menunggu_persetujuan_kepala_gudang',
                'menunggu_persetujuan_kasir'
            ]);
        }

        if ($this->option('old')) {
            $query->where('created_at', '<=', $cutoffTime);
        }

        $pos = $query->orderBy('created_at', 'desc')->get();

        if ($pos->isEmpty()) {
            $this->info('No POs found with the specified criteria.');
            return Command::SUCCESS;
        }

        // Group by status
        $grouped = $pos->groupBy('status');

        foreach ($grouped as $status => $group) {
            $this->info("Status: {$status} ({$group->count()} PO)");
            $this->line(str_repeat('-', 80));

            $headers = ['No PO', 'Type', 'Created At', 'Hours Ago', 'Should Cancel?'];
            $rows = [];

            foreach ($group as $po) {
                $hoursAgo = $po->created_at->diffInHours(now());
                $shouldCancel = $po->created_at->lte($cutoffTime);

                $rows[] = [
                    $po->no_po,
                    ucfirst($po->tipe_po),
                    $po->created_at->format('Y-m-d H:i:s'),
                    $hoursAgo . 'h',
                    $shouldCancel ? '✓ YES' : '✗ No',
                ];
            }

            $this->table($headers, $rows);
            $this->line('');
        }

        // Summary
        $totalPending = PurchaseOrder::whereIn('status', [
            'menunggu_persetujuan_kepala_gudang',
            'menunggu_persetujuan_kasir'
        ])->count();

        $totalShouldCancel = PurchaseOrder::whereIn('status', [
            'menunggu_persetujuan_kepala_gudang',
            'menunggu_persetujuan_kasir'
        ])->where('created_at', '<=', $cutoffTime)->count();

        $this->info('===========================================');
        $this->info('Summary:');
        $this->line("Total POs: {$pos->count()}");
        $this->line("Total Pending: {$totalPending}");
        $this->line("Should be Cancelled: {$totalShouldCancel}");
        $this->info('===========================================');

        if ($totalShouldCancel > 0) {
            $this->line('');
            $this->warn("⚠ There are {$totalShouldCancel} PO(s) that should be cancelled!");
            $this->line('Run: php artisan po:auto-cancel');
        }

        return Command::SUCCESS;
    }
}
