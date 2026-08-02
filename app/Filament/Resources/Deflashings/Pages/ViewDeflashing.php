<?php

namespace App\Filament\Resources\Deflashings\Pages;

use App\Filament\Resources\Deflashings\DeflashingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDeflashing extends ViewRecord
{
    protected static string $resource = DeflashingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
