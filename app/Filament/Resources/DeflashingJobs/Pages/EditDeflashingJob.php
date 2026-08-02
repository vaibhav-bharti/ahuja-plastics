<?php

namespace App\Filament\Resources\DeflashingJobs\Pages;

use App\Filament\Resources\DeflashingJobs\DeflashingJobResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDeflashingJob extends EditRecord
{
    protected static string $resource = DeflashingJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
