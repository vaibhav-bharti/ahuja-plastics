<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProductionJobReturns\ProductionJobReturnResource;
use App\Filament\Resources\ProductionJobs\ProductionJobResource;
use App\Filament\Resources\Productions\ProductionResource;
use App\Filament\Resources\RawMaterialStocks\RawMaterialStockResource;
use App\Filament\Resources\StockTransactions\StockTransactionResource;
use Filament\Widgets\Widget;

class ProductionQuickActions extends Widget
{
    protected string $view = 'filament.widgets.production-quick-actions';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        $isAdmin = auth()->user()?->isAdmin() ?? false;

        $user = auth()->user();

        $actions = [
            ['module' => 'production', 'label' => 'New Production', 'url' => ProductionResource::getUrl('create'), 'icon' => 'heroicon-o-plus-circle'],
            ['module' => 'production', 'label' => 'Production List', 'url' => ProductionResource::getUrl('index'), 'icon' => 'heroicon-o-clipboard-document-list'],
            ['module' => 'production_jobs', 'label' => 'Production Jobs', 'url' => ProductionJobResource::getUrl('index'), 'icon' => 'heroicon-o-wrench-screwdriver'],
            ['module' => 'job_returns', 'label' => 'Job Returns', 'url' => ProductionJobReturnResource::getUrl('index'), 'icon' => 'heroicon-o-arrow-uturn-left'],
        ];

        if ($isAdmin) {
            $actions = [
                ...$actions,
                ['module' => 'production_jobs', 'label' => 'New Job', 'url' => ProductionJobResource::getUrl('create'), 'icon' => 'heroicon-o-plus-circle'],
                ['module' => 'job_returns', 'label' => 'Add Job Return', 'url' => ProductionJobReturnResource::getUrl('create'), 'icon' => 'heroicon-o-plus-circle'],
                ['module' => 'stock_entries', 'label' => 'New Stock Entry', 'url' => RawMaterialStockResource::getUrl('create'), 'icon' => 'heroicon-o-archive-box-arrow-down'],
                ['module' => 'stock_transactions', 'label' => 'Stock Ledger', 'url' => StockTransactionResource::getUrl('index'), 'icon' => 'heroicon-o-arrows-right-left'],
            ];
        }

        return [
            'isAdmin' => $isAdmin,
            'actions' => array_values(array_filter(
                $actions,
                fn (array $action): bool => $isAdmin || $user?->hasModuleAccess($action['module'])
            )),
        ];
    }
}
