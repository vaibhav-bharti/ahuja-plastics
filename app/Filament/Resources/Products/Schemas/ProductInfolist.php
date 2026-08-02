<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('name')
                                    ->label('Product Name'),
                                TextEntry::make('pcs_per_kg')
                                    ->label('PCS Per KG'),

                                TextEntry::make('mould.name')
                                    ->label('Mould'),

                                TextEntry::make('mould.cavity')
                                    ->label('Cavity'),

                                TextEntry::make('mould.cycle_time')
                                    ->label('Cycle Time')
                                    ->suffix(' Seconds'),

                                IconEntry::make('status')
                                    ->label('Status')
                                    ->boolean(),

                                TextEntry::make('remarks')
                                    ->label('Remarks')
                                    ->placeholder('-')
                                    ->columnSpanFull(),

                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime('d M Y h:i A'),

                                TextEntry::make('updated_at')
                                    ->label('Updated At')
                                    ->dateTime('d M Y h:i A'),

                            ]),
                    ]),
            ]);
    }
}