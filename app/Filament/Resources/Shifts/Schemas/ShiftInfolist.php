<?php

namespace App\Filament\Resources\Shifts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShiftInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Shift Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('name')
                                    ->label('Shift Name'),

                                IconEntry::make('status')
                                    ->label('Status')
                                    ->boolean(),

                                TextEntry::make('start_time')
                                    ->label('Start Time')
                                    ->time('h:i A'),

                                TextEntry::make('end_time')
                                    ->label('End Time')
                                    ->time('h:i A'),

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