<?php

namespace App\Filament\Resources\ProductionJobs\Pages;

use App\Filament\Resources\ProductionJobs\ProductionJobResource;
use App\Services\ProductionJobService;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProductionJob extends EditRecord
{
    protected static string $resource = ProductionJobResource::class;

    protected function afterSave(): void
    {
        app(ProductionJobService::class)->recalculateTotals($this->record);
    }

    protected function getHeaderActions(): array
    {
        return [ViewAction::make()];
    }
}
