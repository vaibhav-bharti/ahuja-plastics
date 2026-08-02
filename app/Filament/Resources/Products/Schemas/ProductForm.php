<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Mould;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Information')
                    ->description('Enter product details.')
                    ->columns(2)
                    ->schema([

                        TextInput::make('name')
                            ->label('Product Name')
                            ->required()
                            ->maxLength(255),

                        Select::make('mould_id')
                            ->label('Mould')
                            ->relationship('mould', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                        TextInput::make('pcs_per_kg')
                            ->label('PCS Per KG')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->helperText('Example : 250'),

                        Placeholder::make('cavity')
                            ->label('Mould Cavity')
                            ->content(function ($get) {
                                $mould = Mould::find($get('mould_id'));

                                return $mould?->cavity ?? '-';
                            }),

                        Placeholder::make('cycle_time')
                            ->label('Cycle Time')
                            ->content(function ($get) {
                                $mould = Mould::find($get('mould_id'));

                                return $mould
                                    ? $mould->cycle_time . ' Seconds'
                                    : '-';
                            }),

                        Toggle::make('status')
                            ->default(true),

                        Textarea::make('remarks')
                            ->rows(3)
                            ->columnSpanFull(),

                    ]),
            ]);
    }
}