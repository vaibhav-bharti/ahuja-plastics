<?php

namespace App\Filament\Resources\ProductionJobs\Pages;

use App\Filament\Resources\ProductionJobs\ProductionJobResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListProductionJobs extends ListRecords
{
    protected static string $resource = ProductionJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Create production job')
                ->url(ProductionJobResource::getUrl('create'))
                ->icon('heroicon-o-plus')
                ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
        ];
    }
}
