<?php

namespace App\Filament\Resources\RawMaterialStocks\Pages;

use App\Filament\Resources\RawMaterialStocks\RawMaterialStockResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRawMaterialStocks extends ListRecords
{
    protected static string $resource = RawMaterialStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
