<?php

namespace App\Filament\Resources\ProductionJobs\Tables;

use App\Filament\Resources\ProductionJobs\ProductionJobResource;
use App\Models\ProductionJob;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductionJobsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('issued_at', 'desc')
            ->columns([
                TextColumn::make('display_job_no')->label('Job No.')->state(fn (ProductionJob $record): string => $record->display_job_no)->searchable(query: fn ($query, string $search) => $query->where('job_no', 'like', "%{$search}%"))->sortable(query: fn ($query, string $direction) => $query->orderBy('id', $direction)),
                TextColumn::make('production.product.name')->label('Product')->searchable(),
                TextColumn::make('action.name')->searchable(),
                TextColumn::make('worker.name')->searchable(),
                TextColumn::make('issued_at')->dateTime('d M Y, h:i A')->sortable(),
                TextColumn::make('issued_weight')->label('Issued')->suffix(' kg')->sortable(),
                TextColumn::make('returned_weight_total')->label('Received')->suffix(' kg')->sortable(),
                TextColumn::make('pending_weight')->label('Pending')->state(fn (ProductionJob $record): string => $record->pending_weight)->suffix(' kg'),
                TextColumn::make('good_pcs_total')->label('Good Pieces')->sortable(),
                TextColumn::make('status')->badge()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'Pending' => 'Pending',
                    'Partial' => 'Partial',
                    'Completed' => 'Completed',
                    'Cancelled' => 'Cancelled',
                ]),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('edit')
                    ->url(fn (ProductionJob $record): string => ProductionJobResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-pencil-square')
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
            ]);
    }
}
