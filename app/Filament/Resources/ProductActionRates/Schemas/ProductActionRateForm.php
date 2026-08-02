<?php

namespace App\Filament\Resources\ProductActionRates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductActionRateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Action Rate')
                    ->description('Configure action rate for a product.')
                    ->columns(2)
                    ->schema([

                        Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('action_id')
                            ->label('Action')
                            ->relationship('action', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('rate')
                            ->label('Rate (₹)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->prefix('₹'),
                        TextInput::make('sort_order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->minValue(1)
                            ->helperText('1 = First, 2 = Second...'),

                        Toggle::make('status')
                            ->label('Active')
                            ->default(true),

                        Textarea::make('remarks')
                            ->label('Remarks')
                            ->rows(3)
                            ->columnSpanFull(),

                    ]),
            ]);
    }
}