<?php

namespace App\Filament\Resources\ProductionJobs\Pages;

use App\Filament\Resources\ProductionJobs\ProductionJobResource;
use App\Models\ProductionJob;
use App\Services\ProductionJobService;
use Filament\Resources\Pages\CreateRecord;

class CreateProductionJob extends CreateRecord
{
    protected static string $resource = ProductionJobResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function handleRecordCreation(array $data): ProductionJob
    {
        return app(ProductionJobService::class)->createJob($data);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
