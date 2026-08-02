<?php

namespace App\Filament\Resources\Productions\Pages;

use App\Filament\Resources\Productions\ProductionResource;
use App\Services\InventoryService;
use Filament\Resources\Pages\CreateRecord;

class CreateProduction extends CreateRecord
{
    protected static string $resource = ProductionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    /**
     * Deduct inventory after production is completely saved.
     */
    protected function afterCreate(): void
    {
        $this->record->load('materials');

        foreach ($this->record->materials as $material) {

            InventoryService::consumeMaterial(

                rawMaterialId: $material->raw_material_id,

                quantity: (float) $material->quantity,

                referenceType: 'Production',

                referenceId: $this->record->id,

                remarks: 'Production Consumption'

            );

        }
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}