<?php

namespace App\Filament\Resources\ProductionJobs\Pages;

use App\Filament\Resources\ProductionJobs\ProductionJobResource;
use App\Services\ProductionJobService;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProductionJob extends EditRecord
{
    protected static string $resource = ProductionJobResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(ProductionJobService::class)->updateJob($record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [ViewAction::make()];
    }
}
