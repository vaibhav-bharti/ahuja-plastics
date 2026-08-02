<?php

namespace App\Filament\Resources\RawMaterialStocks\Pages;

use App\Filament\Resources\RawMaterialStocks\RawMaterialStockResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRawMaterialStock extends ViewRecord
{
    protected static string $resource = RawMaterialStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
