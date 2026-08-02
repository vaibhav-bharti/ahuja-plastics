<?php

namespace App\Filament\Resources\Productions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('production_date', 'desc')

            ->columns([

                TextColumn::make('production_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('shift.name')
                    ->label('Shift')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('machine.machine_no')
                    ->label('Machine')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('operator.name')
                    ->label('Operator')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('planned_quantity')
                    ->label('Planned Qty')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('actual_counter')
                    ->label('Actual Counter')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('counter_difference')
                    ->label('Difference')
                    ->badge()
                    ->color(fn ($state) => $state < 0 ? 'danger' : ($state > 0 ? 'success' : 'gray'))
                    ->sortable(),

                IconColumn::make('status')
                    ->label('Status')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y h:i A')
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([])

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
