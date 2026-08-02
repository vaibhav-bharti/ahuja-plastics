<?php

namespace App\Filament\Resources\Moulds\Pages;

use App\Filament\Resources\Moulds\MouldResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMould extends ViewRecord
{
    protected static string $resource = MouldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
