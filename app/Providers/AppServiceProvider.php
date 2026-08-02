<?php

namespace App\Providers;
use App\Models\Production;
use App\Observers\ProductionObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\RawMaterialStock;
use App\Observers\RawMaterialStockObserver;

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
        Production::observe(ProductionObserver::class);
        RawMaterialStock::observe(RawMaterialStockObserver::class);
    }
}
