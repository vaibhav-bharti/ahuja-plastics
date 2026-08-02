<?php

namespace App\Filament\Resources\DeflashingJobs\Pages;

use App\Filament\Resources\DeflashingJobs\DeflashingJobResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeflashingJobs extends ListRecords
{
    protected static string $resource = DeflashingJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
