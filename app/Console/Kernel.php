<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * IMPORTANT: Daftarkan command secara eksplisit
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\AutoCancelPendingPO::class,
        // Tambahkan command lain di sini jika ada
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // PASTIKAN method ini ada dan tidak kosong

        // Auto-cancel PO yang pending > 24 jam
        $schedule->command('po:auto-cancel')
            ->everyMinute() // SEMENTARA set setiap menit untuk testing
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/scheduler.log'))
            ->onSuccess(function () {
                Log::info('[Scheduler] Auto-cancel PO SUCCESS', [
                    'time' => now()->format('Y-m-d H:i:s')
                ]);
            })
            ->onFailure(function () {
                Log::error('[Scheduler] Auto-cancel PO FAILED', [
                    'time' => now()->format('Y-m-d H:i:s')
                ]);
            });

        // Setelah berhasil, ubah ke hourly():
        // ->hourly()
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     */
    protected function scheduleTimezone(): string
    {
        return config('app.timezone', 'Asia/Jakarta');
    }
}
