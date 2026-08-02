<?php

namespace App\Filament\Resources\RawMaterialStocks\Schemas;

use App\Models\RawMaterial;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class RawMaterialStockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | Purchase Details
            |--------------------------------------------------------------------------
            */

            Section::make('Purchase Details')
                ->description('Enter purchase information')
                ->columns(3)
                ->schema([

                    DatePicker::make('purchase_date')
                        ->default(now())
                        ->required(),

                    TextInput::make('vendor_name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('invoice_no')
                        ->maxLength(100),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Material Details
            |--------------------------------------------------------------------------
            */

            Section::make('Material Details')
                ->description('Select material and enter quantity')
                ->columns(3)
                ->schema([

                    Select::make('raw_material_id')
                        ->label('Raw Material')
                        ->relationship('material', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live(),

                    TextInput::make('material_type')
                        ->label('Material Type')
                        ->disabled()
                        ->dehydrated(false)
                        ->live()
                        ->afterStateHydrated(function (Get $get, Set $set) {

                            if (!$get('raw_material_id')) {
                                return;
                            }

                            $material = RawMaterial::find(
                                $get('raw_material_id')
                            );

                            $set(
                                'material_type',
                                $material?->type
                            );
                        }),

                    TextInput::make('purchase_qty')
                        ->label('Purchase Qty')
                        ->numeric()
                        ->suffix('KG')
                        ->required()
                        ->live(),

                ]),
                            /*
            |--------------------------------------------------------------------------
            | Quantity & Price
            |--------------------------------------------------------------------------
            */

            Section::make('Quantity & Price')
                ->description('Purchase quantity and pricing')
                ->columns(4)
                ->schema([

                    TextInput::make('purchase_price')
                        ->label('Purchase Price')
                        ->numeric()
                        ->prefix('₹')
                        ->required()
                        ->live(),

                    TextInput::make('available_qty')
                        ->label('Available Qty')
                        ->numeric()
                        ->suffix('KG')
                        ->disabled()
                        ->dehydrated()
                        ->live(),

                    TextInput::make('total_amount')
                        ->label('Total Amount')
                        ->numeric()
                        ->prefix('₹')
                        ->disabled()
                        ->dehydrated()
                        ->live(),

                    TextInput::make('unit')
                        ->default('KG')
                        ->disabled()
                        ->dehydrated(false),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Auto Calculation
            |--------------------------------------------------------------------------
            */

            TextInput::make('dummy_calculation')
                ->hidden()
                ->dehydrated(false)
                ->afterStateHydrated(function (Get $get, Set $set) {

                    $qty = (float) ($get('purchase_qty') ?: 0);

                    $price = (float) ($get('purchase_price') ?: 0);

                    $set('available_qty', $qty);

                    $set('total_amount', round($qty * $price, 2));

                })
                ->afterStateUpdated(function (Get $get, Set $set) {

                    $qty = (float) ($get('purchase_qty') ?: 0);

                    $price = (float) ($get('purchase_price') ?: 0);

                    $set('available_qty', $qty);

                    $set('total_amount', round($qty * $price, 2));

                }),
                            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */

            Section::make('Remarks')
                ->schema([

                    \Filament\Forms\Components\Textarea::make('remarks')
                        ->rows(4)
                        ->columnSpanFull(),

                ]),

            /*
            |--------------------------------------------------------------------------
            | System Information
            |--------------------------------------------------------------------------
            */

            Section::make('System Information')
                ->columns(2)
                ->schema([

                    Select::make('status')
                        ->options([
                            1 => 'Active',
                            0 => 'Inactive',
                        ])
                        ->default(1)
                        ->required(),

                    TextInput::make('created_by')
                        ->default(fn () => auth()->id())
                        ->hidden()
                        ->dehydrated(),

                ]),

        ]);
    }
}