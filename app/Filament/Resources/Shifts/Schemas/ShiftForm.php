<?php

namespace App\Filament\Resources\Shifts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Shift Information')
                    ->description('Enter shift details.')
                    ->columns(2)
                    ->schema([

                        TextInput::make('name')
                            ->label('Shift Name')
                            ->placeholder('Morning Shift')
                            ->required()
                            ->maxLength(100),

                        Toggle::make('status')
                            ->label('Active')
                            ->default(true),

                        TimePicker::make('start_time')
                            ->label('Start Time')
                            ->seconds(false)
                            ->required(),

                        TimePicker::make('end_time')
                            ->label('End Time')
                            ->seconds(false)
                            ->required(),

                    ]),
            ]);
    }
}