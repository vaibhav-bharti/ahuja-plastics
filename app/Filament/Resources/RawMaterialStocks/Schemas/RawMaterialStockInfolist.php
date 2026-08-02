<?php

namespace App\Filament\Resources\RawMaterialStocks\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RawMaterialStockInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | Purchase Details
                |--------------------------------------------------------------------------
                */

                Section::make('Purchase Details')
                    ->columns(2)
                    ->schema([

                        TextEntry::make('purchase_date')
                            ->date('d M Y'),

                        TextEntry::make('vendor_name')
                            ->label('Vendor'),

                        TextEntry::make('invoice_no')
                            ->label('Invoice No')
                            ->placeholder('-'),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Material Details
                |--------------------------------------------------------------------------
                */

                Section::make('Material Details')
                    ->columns(2)
                    ->schema([

                        TextEntry::make('material.name')
                            ->label('Raw Material'),

                        TextEntry::make('material.type')
                            ->label('Material Type')
                            ->badge(),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Quantity Summary
                |--------------------------------------------------------------------------
                */

                Section::make('Quantity Summary')
                    ->columns(2)
                    ->schema([

                        TextEntry::make('purchase_qty')
                            ->label('Purchased Qty')
                            ->suffix(' KG'),

                        TextEntry::make('available_qty')
                            ->label('Available Qty')
                            ->suffix(' KG')
                            ->badge(),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Price Summary
                |--------------------------------------------------------------------------
                */

                Section::make('Price Summary')
                    ->columns(2)
                    ->schema([

                        TextEntry::make('purchase_price')
                            ->label('Purchase Price')
                            ->money('INR'),

                        TextEntry::make('total_amount')
                            ->label('Total Amount')
                            ->money('INR'),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | System Information
                |--------------------------------------------------------------------------
                */

                Section::make('System Information')
                    ->columns(2)
                    ->schema([

                        IconEntry::make('status')
                            ->label('Status')
                            ->boolean(),

                        TextEntry::make('creator.name')
                            ->label('Created By')
                            ->placeholder('-'),

                        TextEntry::make('created_at')
                            ->dateTime('d M Y h:i A'),

                        TextEntry::make('updated_at')
                            ->dateTime('d M Y h:i A'),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Remarks
                |--------------------------------------------------------------------------
                */

                Section::make('Remarks')
                    ->schema([

                        TextEntry::make('remarks')
                            ->placeholder('-')
                            ->columnSpanFull(),

                    ]),

            ]);
    }
}