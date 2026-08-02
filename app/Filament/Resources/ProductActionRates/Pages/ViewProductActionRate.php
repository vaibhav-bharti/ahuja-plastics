<?php

namespace App\Filament\Resources\ProductActionRates\Pages;

use App\Filament\Resources\ProductActionRates\ProductActionRateResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProductActionRate extends ViewRecord
{
    protected static string $resource = ProductActionRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
