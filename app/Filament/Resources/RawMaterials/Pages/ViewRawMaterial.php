<?php

namespace App\Filament\Resources\RawMaterials\Pages;

use App\Filament\Resources\RawMaterials\RawMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRawMaterial extends ViewRecord
{
    protected static string $resource = RawMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Actions\EditAction::make(),

            Actions\Action::make('stock')
                ->label('Manage Stock')
                ->icon('heroicon-o-archive-box')
                ->color('success')
                ->url(fn () => route(
                    'filament.admin.resources.raw-material-stocks.index',
                    [
                        'tableFilters[material][value]' => $this->record->id,
                    ]
                )),

            Actions\Action::make('addStock')
                ->label('Add Stock')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(fn () => route(
                    'filament.admin.resources.raw-material-stocks.create',
                    [
                        'raw_material_id' => $this->record->id,
                    ]
                )),

        ];
    }
}