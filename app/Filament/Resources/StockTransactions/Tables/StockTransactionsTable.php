<?php

namespace App\Filament\Resources\StockTransactions\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class StockTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),

                TextColumn::make('stock.material.name')
                    ->label('Raw Material')
                    ->searchable(),

                TextColumn::make('stock.invoice_no')
                    ->label('Stock Entry / Invoice')
                    ->formatStateUsing(fn (?string $state, $record): string => $state ?: "Stock #{$record->raw_material_stock_id}")
                    ->searchable(),

                TextColumn::make('transaction_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'IN' => 'success',
                        'OUT' => 'danger',
                        default => 'warning',
                    }),

                TextColumn::make('quantity')
                    ->suffix(' kg')
                    ->sortable(),

                TextColumn::make('balance_qty')
                    ->label('Balance After')
                    ->suffix(' kg')
                    ->sortable(),

                TextColumn::make('reference_type')
                    ->label('Source')
                    ->formatStateUsing(fn (?string $state): string => $state ? Str::headline(class_basename($state)) : 'Manual entry')
                    ->toggleable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('transaction_type')
                    ->label('Transaction Type')
                    ->options([
                        'IN' => 'Stock In',
                        'OUT' => 'Stock Out',
                        'ADJUSTMENT' => 'Adjustment',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
