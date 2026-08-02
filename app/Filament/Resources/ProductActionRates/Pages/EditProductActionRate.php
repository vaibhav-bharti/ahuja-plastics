<?php

namespace App\Filament\Resources\ProductActionRates\Pages;

use App\Filament\Resources\ProductActionRates\ProductActionRateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProductActionRate extends EditRecord
{
    protected static string $resource = ProductActionRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
