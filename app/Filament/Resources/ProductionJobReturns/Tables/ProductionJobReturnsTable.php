<?php

namespace App\Filament\Resources\ProductionJobReturns\Tables;

use App\Filament\Resources\ProductionJobReturns\ProductionJobReturnResource;
use App\Models\ProductionJobReturn;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductionJobReturnsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('return_date', 'desc')
            ->columns([
                TextColumn::make('productionJob.job_no')->label('Job No.')->searchable(),
                TextColumn::make('productionJob.action.name')->label('Action')->searchable(),
                TextColumn::make('return_date')->date('d M Y')->sortable(),
                TextColumn::make('return_weight')->suffix(' kg')->sortable(),
                TextColumn::make('feed_weight')->suffix(' kg')->toggleable(),
                TextColumn::make('reject_weight')->suffix(' kg')->toggleable(),
                TextColumn::make('good_pcs')->label('Good Pieces')->sortable(),
                TextColumn::make('rate')->money('INR'),
                TextColumn::make('amount')->money('INR')->sortable(),
            ])
            ->filters([
                SelectFilter::make('production_job_id')
                    ->label('Job')
                    ->relationship('productionJob', 'job_no')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('edit')
                    ->url(fn (ProductionJobReturn $record): string => ProductionJobReturnResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-pencil-square')
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
            ]);
    }
}
