<?php

namespace App\Filament\Resources\ProductionJobReturns\Pages;

use App\Filament\Resources\ProductionJobReturns\ProductionJobReturnResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProductionJobReturn extends ViewRecord
{
    protected static string $resource = ProductionJobReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
