<?php

namespace App\Filament\Resources\Deflashings\Pages;

use App\Filament\Resources\Deflashings\DeflashingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDeflashing extends EditRecord
{
    protected static string $resource = DeflashingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
