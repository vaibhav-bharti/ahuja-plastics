<?php

namespace App\Filament\Resources\Productions\Pages;

use App\Filament\Resources\Productions\ProductionResource;
use App\Services\InventoryService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class CreateProduction extends CreateRecord
{
    protected static string $resource = ProductionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function beforeCreate(): void
    {
        $calculation = InventoryService::validateProductionInput(
            $this->data['materials'] ?? [],
            (float) ($this->data['weight_per_shot'] ?? 0),
            (float) ($this->data['actual_counter'] ?? 0),
        );

        InventoryService::validateStockAvailability($this->data['materials'] ?? [], $calculation['consumption']);
    }

    protected function afterCreate(): void
    {
        InventoryService::applyProduction($this->record->fresh());
    }

    /** Keep form validation visible; convert unexpected failures to a safe UI notification. */
    public function exception($e, $stopPropagation): void
    {
        if ($e instanceof ValidationException) {
            return;
        }

        report($e);
        Notification::make()->danger()->title('Production was not saved')->body('An unexpected error occurred. No stock changes were applied.')->send();
        $stopPropagation();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
