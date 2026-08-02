<?php

namespace App\Filament\Resources\ProductActionRates\Pages;

use App\Filament\Resources\ProductActionRates\ProductActionRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductActionRates extends ListRecords
{
    protected static string $resource = ProductActionRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
