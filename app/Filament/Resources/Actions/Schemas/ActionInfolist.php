<?php

namespace App\Filament\Resources\Actions\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Action Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('name')
                                    ->label('Action Name'),

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