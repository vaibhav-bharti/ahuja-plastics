<?php

namespace App\Filament\Resources\Machines\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MachineInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Machine Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('machine_no')
                                    ->label('Machine Number'),

                                TextEntry::make('name')
                                    ->label('Machine Name'),

                                IconEntry::make('status')
                                    ->label('Status')
                                    ->boolean(),

                                TextEntry::make('remarks')
                                    ->label('Remarks')
                                    ->placeholder('-')
                                    ->columnSpanFull(),

                            ]),
                    ]),

                Section::make('System Information')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime('d M Y h:i A'),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->since(),

                            ]),
                    ]),
            ]);
    }
}