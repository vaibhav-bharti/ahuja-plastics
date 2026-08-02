<?php

namespace App\Filament\Resources\Actions\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Action Information')
                    ->description('Enter process/action details.')
                    ->columns(2)
                    ->schema([

                        TextInput::make('name')
                            ->label('Action Name')
                            ->placeholder('Deflashing')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),

                        Toggle::make('status')
                            ->label('Active')
                            ->default(true),

                        Textarea::make('remarks')
                            ->label('Remarks')
                            ->rows(3)
                            ->columnSpanFull(),

                    ]),
            ]);
    }
}