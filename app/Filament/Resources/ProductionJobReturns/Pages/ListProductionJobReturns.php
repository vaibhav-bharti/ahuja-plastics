<?php

namespace App\Filament\Resources\ProductionJobReturns\Pages;

use App\Filament\Resources\ProductionJobReturns\ProductionJobReturnResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListProductionJobReturns extends ListRecords
{
    protected static string $resource = ProductionJobReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Create job return')
                ->url(ProductionJobReturnResource::getUrl('create'))
                ->icon('heroicon-o-plus')
                ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
        ];
    }
}
