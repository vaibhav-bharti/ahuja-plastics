<?php

namespace App\Filament\Resources\Machines\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MachineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Machine Information')
                    ->description('Enter machine details.')
                    ->columns(2)
                    ->schema([

                        TextInput::make('machine_no')
                            ->label('Machine Number')
                            ->placeholder('MC-01')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->autocomplete(false),

                        TextInput::make('name')
                            ->label('Machine Name')
                            ->placeholder('Injection Machine 1')
                            ->required()
                            ->maxLength(100),

                        Toggle::make('status')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),

                        Textarea::make('remarks')
                            ->label('Remarks')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                    ]),
            ]);
    }
}