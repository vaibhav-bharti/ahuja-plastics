<?php

namespace App\Filament\Resources\RawMaterialStocks\Schemas;

use App\Models\RawMaterial;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                ->icon('heroicon-o-shopping-cart')
                ->columns([
                    'default' => 1,
                    'sm'      => 2,
                    'lg'      => 3,
                ])
                ->schema([

                    DatePicker::make('purchase_date')
                        ->label('Purchase Date')
                        ->prefixIcon('heroicon-o-calendar-days')
                        ->native(false)
                        ->displayFormat('d-m-Y')
                        ->default(now())
                        ->required()
                        ->extraInputAttributes(['class' => 'text-base']),

                    TextInput::make('vendor_name')
                        ->label('Vendor Name')
                        ->prefixIcon('heroicon-o-building-storefront')
                        ->placeholder('e.g. Sharma Traders')
                        ->required()
                        ->maxLength(255)
                        ->extraInputAttributes(['class' => 'text-base']),

                    TextInput::make('invoice_no')
                        ->label('Invoice No.')
                        ->prefixIcon('heroicon-o-document-text')
                        ->placeholder('e.g. INV-00123')
                        ->maxLength(100)
                        ->extraInputAttributes(['class' => 'text-base'])
                        ->columnSpan([
                            'default' => 1,
                            'sm'      => 2,
                            'lg'      => 1,
                        ]),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Material Details
            |--------------------------------------------------------------------------
            */

            Section::make('Material Details')
                ->description('Select material and enter quantity')
                ->icon('heroicon-o-cube')
                ->columns([
                    'default' => 1,
                    'sm'      => 2,
                ])
                ->schema([

                    Select::make('raw_material_id')
                        ->label('Raw Material')
                        ->relationship('material', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->prefixIcon('heroicon-o-archive-box')
                        ->placeholder('Select a material')
                        ->required()
                        ->live()
                        ->default(request('raw_material_id'))
                        ->disabled(fn () => filled(request('raw_material_id')))
                        ->dehydrated()
                        ->extraInputAttributes(['class' => 'text-base']),

                    TextInput::make('purchase_qty')
                        ->label('Purchase Qty')
                        ->numeric()
                        ->minValue(0)
                        ->step(0.01)
                        ->suffix('KG')
                        ->placeholder('0.00')
                        ->required()
                        ->live(onBlur: true)
                        ->extraInputAttributes(['class' => 'text-base font-medium'])
                        ->afterStateUpdated(function (Get $get, Set $set) {

                            $qty = (float) ($get('purchase_qty') ?: 0);
                            $price = (float) ($get('purchase_price') ?: 0);

                            $set('available_qty', $qty);
                            $set('total_amount', round($qty * $price, 2));

                        }),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Quantity & Price
            |--------------------------------------------------------------------------
            */

            Section::make('Quantity & Price')
                ->description('Purchase quantity and pricing')
                ->icon('heroicon-o-currency-rupee')
                ->columns([
                    'default' => 1,
                    'sm'      => 2,
                    'lg'      => 3,
                ])
                ->schema([

                    TextInput::make('purchase_price')
                        ->label('Purchase Price')
                        ->numeric()
                        ->minValue(0)
                        ->step(0.01)
                        ->prefix('₹')
                        ->placeholder('0.00')
                        ->required()
                        ->live(onBlur: true)
                        ->extraInputAttributes(['class' => 'text-base font-medium'])
                        ->afterStateUpdated(function (Get $get, Set $set) {

                            $qty = (float) ($get('purchase_qty') ?: 0);
                            $price = (float) ($get('purchase_price') ?: 0);

                            $set('available_qty', $qty);
                            $set('total_amount', round($qty * $price, 2));

                        }),

                    TextInput::make('available_qty')
                        ->label('Available Qty')
                        ->numeric()
                        ->suffix('KG')
                        ->disabled()
                        ->dehydrated()
                        ->extraInputAttributes(['class' => 'text-base font-semibold text-success-600']),

                    TextInput::make('total_amount')
                        ->label('Total Amount')
                        ->numeric()
                        ->prefix('₹')
                        ->disabled()
                        ->dehydrated()
                        ->extraInputAttributes(['class' => 'text-base font-semibold text-success-600'])
                        ->columnSpan([
                            'default' => 1,
                            'sm'      => 2,
                            'lg'      => 1,
                        ]),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */

            Section::make('Remarks')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->collapsible()
                ->schema([

                    Textarea::make('remarks')
                        ->placeholder('Any additional notes about this purchase...')
                        ->rows(4)
                        ->columnSpanFull(),

                ]),

            /*
            |--------------------------------------------------------------------------
            | System Information
            |--------------------------------------------------------------------------
            */

            Section::make('System Information')
                ->icon('heroicon-o-cog-6-tooth')
                ->collapsible()
                ->collapsed(fn (string $operation) => $operation === 'create')
                ->columns([
                    'default' => 1,
                    'sm'      => 2,
                ])
                ->schema([

                    Select::make('status')
                        ->native(false)
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