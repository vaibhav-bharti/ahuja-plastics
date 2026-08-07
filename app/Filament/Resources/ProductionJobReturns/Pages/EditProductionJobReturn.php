<?php

namespace App\Filament\Resources\ProductionJobReturns\Pages;

use App\Filament\Resources\ProductionJobReturns\ProductionJobReturnResource;
use App\Models\ProductionJobReturn;
use App\Services\ProductionJobService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProductionJobReturn extends EditRecord
{
    protected static string $resource = ProductionJobReturnResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(ProductionJobService::class)->updateReturn($record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->using(function (ProductionJobReturn $record): bool {
                    app(ProductionJobService::class)->deleteReturn($record);

                    return true;
                }),
        ];
    }
}
