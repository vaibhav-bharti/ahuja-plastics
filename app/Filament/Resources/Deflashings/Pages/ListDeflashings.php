<?php

namespace App\Filament\Resources\Deflashings\Pages;

use App\Filament\Resources\Deflashings\DeflashingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeflashings extends ListRecords
{
    protected static string $resource = DeflashingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
