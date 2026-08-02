<?php

namespace App\Filament\Resources\DeflashingJobs\Pages;

use App\Filament\Resources\DeflashingJobs\DeflashingJobResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDeflashingJob extends ViewRecord
{
    protected static string $resource = DeflashingJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
