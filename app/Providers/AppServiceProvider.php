<?php

namespace App\Providers;
use App\Models\Production;
use App\Observers\ProductionObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\RawMaterialStock;
use App\Observers\RawMaterialStockObserver;
use App\Models\ProductionJob;
use App\Models\ProductionJobReturn;
use App\Models\User;
use App\Models\Action;
use App\Models\Machine;
use App\Models\Mould;
use App\Models\Product;
use App\Models\ProductActionRate;
use App\Models\RawMaterial;
use App\Models\Shift;
use App\Models\StockTransaction;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

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

        Gate::before(function (User $user, string $ability, array $arguments): ?bool {
            if ($user->isAdmin()) {
                return true;
            }

            $subject = $arguments[0] ?? null;
            $modelClass = $subject instanceof Model ? $subject::class : $subject;

            if (! is_string($modelClass) || ! is_a($modelClass, Model::class, true)) {
                return null;
            }

            $module = match ($modelClass) {
                Production::class => 'production',
                ProductionJob::class => 'production_jobs',
                ProductionJobReturn::class => 'job_returns',
                RawMaterialStock::class => 'stock_entries',
                StockTransaction::class => 'stock_transactions',
                Action::class,
                Machine::class,
                Mould::class,
                Product::class,
                ProductActionRate::class,
                RawMaterial::class,
                Shift::class,
                Worker::class => 'masters',
                default => null,
            };

            if ($module === null || ! $user->hasModuleAccess($module)) {
                return false;
            }

            if (in_array($modelClass, [Production::class, ProductionJob::class, ProductionJobReturn::class], true)) {
                return null;
            }

            return in_array($ability, ['viewAny', 'view'], true) ? null : false;
        });
    }
}
