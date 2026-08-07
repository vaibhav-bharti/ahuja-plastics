<?php

namespace App\Filament\Resources\ProductionJobReturns\Pages;

use App\Filament\Resources\ProductionJobReturns\ProductionJobReturnResource;
use App\Models\ProductionJob;
use App\Models\ProductionJobReturn;
use App\Services\ProductionJobService;
use Filament\Resources\Pages\CreateRecord;

class CreateProductionJobReturn extends CreateRecord
{
    protected static string $resource = ProductionJobReturnResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function handleRecordCreation(array $data): ProductionJobReturn
    {
        $job = ProductionJob::query()->findOrFail($data['production_job_id']);

        return app(ProductionJobService::class)->addReturn($job, $data);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
