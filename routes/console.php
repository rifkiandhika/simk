<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your console based routes
| including scheduled commands.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// ============================================
// SCHEDULER UNTUK AUTO-CANCEL PO
// ============================================

// Auto-cancel PO yang menunggu approval > 24 jam
Schedule::command('po:auto-cancel')
    ->hourly()  // Setiap jam
    ->withoutOverlapping()  // Prevent duplicate runs
    ->runInBackground()  // Run in background
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('[Scheduler] Auto-cancel PO executed successfully');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('[Scheduler] Auto-cancel PO failed');
    });

// ============================================
// Alternatif interval (uncomment yang diinginkan):
// ============================================

// Setiap 30 menit:
// Schedule::command('po:auto-cancel')->everyThirtyMinutes();

// Setiap 15 menit:
// Schedule::command('po:auto-cancel')->everyFifteenMinutes();

// Setiap 5 menit (untuk testing):
// Schedule::command('po:auto-cancel')->everyFiveMinutes();

// Setiap 1 menit (untuk testing):
// Schedule::command('po:auto-cancel')->everyMinute();

// Setiap hari jam 9 pagi:
// Schedule::command('po:auto-cancel')->dailyAt('09:00');