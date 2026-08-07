<?php

namespace App\Filament\Resources\Productions\Pages;

use App\Filament\Resources\Productions\ProductionResource;
use App\Services\InventoryService;
use App\Models\Production;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class EditProduction extends EditRecord
{
    protected static string $resource = ProductionResource::class;

    protected array $oldMaterials = [];

    protected function beforeSave(): void
    {
        // Hold this production row until relationships and ledger are synced.
        // This prevents two browser tabs from reversing/applying the same
        // production concurrently.
        Production::query()->lockForUpdate()->findOrFail($this->record->id);

        $calculation = InventoryService::validateProductionInput(
            $this->data['materials'] ?? [],
            (float) ($this->data['weight_per_shot'] ?? 0),
            (float) ($this->data['actual_counter'] ?? 0),
        );

        /*
        |--------------------------------------------------------------------------
        | Store Old Material Snapshot
        |--------------------------------------------------------------------------
        */

        $this->record->load('materials');

        $this->oldMaterials = $this->record
            ->materials
            ->map(function ($item) {

                return [
                    'id' => $item->id,
                    'raw_material_id' => $item->raw_material_id,
                    'quantity' => $item->quantity,
                    'consumed_qty' => $item->consumed_qty,
                    'remaining_qty' => $item->remaining_qty,
                ];

            })
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Validate Stock Availability
        |--------------------------------------------------------------------------
        */

        InventoryService::validateStockAvailability(

            $this->data['materials'],

            $calculation['consumption'],

            collect($this->oldMaterials)

        );
    }

    protected function afterSave(): void
    {
        InventoryService::syncProduction(

            $this->record->fresh([
                'materials',
            ]),

            collect($this->oldMaterials)

        );
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->action(fn (Production $record) => DB::transaction(fn () => $record->delete())),
        ];
    }

    public function exception($e, $stopPropagation): void
    {
        if ($e instanceof ValidationException) {
            return;
        }

        report($e);
        Notification::make()->danger()->title('Production was not saved')->body('An unexpected error occurred. No stock changes were applied.')->send();
        $stopPropagation();
    }
}
