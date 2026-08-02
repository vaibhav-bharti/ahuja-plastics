<?php

namespace App\Filament\Resources\Moulds\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MouldForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mould Information')
                    ->description('Enter mould details.')
                    ->columns(2)
                    ->schema([

                        TextInput::make('mould_no')
                            ->label('Mould Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('brand')
                            ->maxLength(255),

                        TextInput::make('cavity')
                            ->numeric()
                            ->required()
                            ->minValue(1),

                        TextInput::make('cycle_time')
                            ->label('Cycle Time (Seconds)')
                            ->numeric()
                            ->required()
                            ->minValue(1),

                        Toggle::make('status')
                            ->default(true),

                        Textarea::make('remarks')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
            ]);
    }
}