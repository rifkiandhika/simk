<?php

namespace App\Providers;

use App\Models\Tagihan;
use App\Models\TagihanItem;
use App\Observers\TagihanItemObserver;
use App\Observers\TagihanObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Tagihan::observe(TagihanObserver::class);
        TagihanItem::observe(TagihanItemObserver::class);
    }
}
