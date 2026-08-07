<?php

namespace App\Filament\Resources\RawMaterialStocks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RawMaterialStocksTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('purchase_date', 'desc')

            ->columns([

                TextColumn::make('material.name')
                    ->label('Raw Material')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('material.type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('vendor_name')
                    ->label('Vendor')
                    ->searchable(),

                TextColumn::make('invoice_no')
                    ->label('Invoice')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('purchase_qty')
                    ->label('Purchased')
                    ->suffix(' KG')
                    ->sortable(),

                TextColumn::make('available_qty')
                    ->label('Available')
                    ->badge()
                    ->suffix(' KG')
                    ->color(fn ($state) => match (true) {

                        $state <= 0 => 'danger',

                        $state <= 10 => 'warning',

                        default => 'success',

                    })
                    ->sortable(),

                TextColumn::make('purchase_price')
                    ->label('Price')
                    ->money('INR')
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Amount')
                    ->money('INR')
                    ->sortable(),

                TextColumn::make('purchase_date')
                    ->date('d M Y')
                    ->sortable(),

                IconColumn::make('status')
                    ->boolean()
                    ->label('Status'),

            ])

            ->filters([
                    SelectFilter::make('material')
                        ->relationship('material', 'name')
                        ->searchable()
                        ->preload(),

                ])

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