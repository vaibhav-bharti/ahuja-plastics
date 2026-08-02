<?php

namespace App\Filament\Resources\RawMaterialStocks\Pages;

use App\Filament\Resources\RawMaterialStocks\RawMaterialStockResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRawMaterialStock extends EditRecord
{
    protected static string $resource = RawMaterialStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
