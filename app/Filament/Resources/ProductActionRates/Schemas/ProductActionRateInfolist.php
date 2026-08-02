<?php

namespace App\Filament\Resources\ProductActionRates\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductActionRateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Action Rate Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('product.name')
                                    ->label('Product'),

                                TextEntry::make('action.name')
                                    ->label('Action'),

                                TextEntry::make('rate')
                                    ->label('Rate')
                                    ->money('INR'),

                                IconEntry::make('status')
                                    ->label('Status')
                                    ->boolean(),
                                TextEntry::make('sort_order')->label('Display Order'),

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