<?php

namespace App\Filament\Resources\ProductionJobs\Pages;

use App\Filament\Resources\ProductionJobs\ProductionJobResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProductionJob extends ViewRecord
{
    protected static string $resource = ProductionJobResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
