<?php

namespace App\Console\Commands;

use App\Models\PurchaseOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AutoCancelPendingPO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-cancel-pending-p-o';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto-cancel process...');

        $oneDayAgo = Carbon::now()->subDay();
        $cancelledCount = 0;

        // Cancel PO menunggu approval kepala gudang > 24 jam
        $kepalaGudangPOs = PurchaseOrder::where('status', 'menunggu_persetujuan_kepala_gudang')
            ->where('created_at', '<=', $oneDayAgo)
            ->get();

        foreach ($kepalaGudangPOs as $po) {
            $po->update([
                'status' => 'dibatalkan',
                'catatan_kepala_gudang' => 'Otomatis dibatalkan karena tidak disetujui dalam 24 jam',
                'status_approval_kepala_gudang' => 'ditolak'
            ]);
            $cancelledCount++;

            Log::info("PO {$po->no_po} auto-cancelled (pending kepala gudang)");
            $this->line("✓ Cancelled: {$po->no_po} (Kepala Gudang timeout)");
        }

        // Cancel PO menunggu approval kasir > 24 jam
        $kasirPOs = PurchaseOrder::where('status', 'menunggu_persetujuan_kasir')
            ->where('tanggal_approval_kepala_gudang', '<=', $oneDayAgo)
            ->get();

        foreach ($kasirPOs as $po) {
            $po->update([
                'status' => 'dibatalkan',
                'catatan_kasir' => 'Otomatis dibatalkan karena tidak disetujui dalam 24 jam',
                'status_approval_kasir' => 'ditolak'
            ]);
            $cancelledCount++;

            Log::info("PO {$po->no_po} auto-cancelled (pending kasir)");
            $this->line("✓ Cancelled: {$po->no_po} (Kasir timeout)");
        }

        $this->info("\nProcess completed! Total cancelled: {$cancelledCount} PO(s)");

        return Command::SUCCESS;
    }
}
