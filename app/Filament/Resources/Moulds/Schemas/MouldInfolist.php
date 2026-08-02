<?php

namespace App\Filament\Resources\Moulds\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MouldInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mould Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('mould_no')
                                    ->label('Mould Number'),

                                TextEntry::make('name'),

                                TextEntry::make('brand'),

                                TextEntry::make('cavity'),

                                TextEntry::make('cycle_time')
                                    ->suffix(' Seconds'),

                                IconEntry::make('status')
                                    ->boolean(),

                                TextEntry::make('remarks')
                                    ->columnSpanFull(),

                                TextEntry::make('created_at')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->dateTime(),
                            ]),
                    ]),
            ]);
    }
}