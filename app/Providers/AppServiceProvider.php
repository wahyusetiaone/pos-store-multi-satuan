<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Sale;
use App\Models\ShippingItem;
use App\Observers\SaleObserver;
use App\Observers\ShippingItemObserver;

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
        // Register observers
        \App\Models\Sale::observe(\App\Observers\SaleObserver::class);
        \App\Models\ShippingItem::observe(\App\Observers\ShippingItemObserver::class);
        \App\Models\AccountReceivable::observe(\App\Observers\AccountReceivableObserver::class);
    }
}
