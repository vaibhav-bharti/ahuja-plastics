<?php

namespace App\Filament\Widgets;

use App\Models\Production;
use App\Models\ProductionDowntime;
use App\Models\ProductionMaterial;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductionStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $today = now()->toDateString();

        $todayProduction = Production::whereDate('production_date', $today)
            ->sum('actual_production');

        $materialUsed = ProductionMaterial::whereHas(
            'production',
            fn ($query) => $query->whereDate('production_date', $today)
        )->sum('quantity');

        $downtime = ProductionDowntime::whereHas(
            'production',
            fn ($query) => $query->whereDate('production_date', $today)
        )->sum('total_minutes');

        $difference = Production::whereDate('production_date', $today)
            ->sum('production_difference');

        return [

            Stat::make('Today Production', number_format($todayProduction).' PCS')
                ->description('Actual Production')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),

            Stat::make('Material Used', number_format($materialUsed, 3).' KG')
                ->description('Today Consumption')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('warning'),

            Stat::make('Downtime', $downtime.' Minutes')
                ->description('Today Downtime')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),

            Stat::make(
                'Production Difference',
                ($difference > 0 ? '+' : '').number_format($difference).' PCS'
            )
                ->description('Target vs Actual')
                ->descriptionIcon(
                    $difference >= 0
                        ? 'heroicon-m-arrow-trending-up'
                        : 'heroicon-m-arrow-trending-down'
                )
                ->color(
                    $difference >= 0
                        ? 'success'
                        : 'danger'
                ),

        ];
    }
}