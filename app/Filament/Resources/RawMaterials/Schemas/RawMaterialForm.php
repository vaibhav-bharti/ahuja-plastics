<?php

namespace App\Filament\Resources\RawMaterials\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RawMaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Raw Material Information')
                    ->description('Create or update raw material.')
                    ->columns(2)
                    ->schema([

                        TextInput::make('name')
                            ->label('Material Name')
                            ->required()
                            ->maxLength(255),

                        Select::make('type')
                            ->label('Material Type')
                            ->options([
                                'Fresh' => 'Fresh',
                                'Reused' => 'Reused',
                            ])
                            ->required()
                            ->native(false),

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