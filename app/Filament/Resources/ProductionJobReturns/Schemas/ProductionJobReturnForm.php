<?php

namespace App\Filament\Resources\ProductionJobReturns\Schemas;

use App\Models\ProductionJob;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductionJobReturnForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Return Details')
                ->columns(2)
                ->schema([
                    Select::make('production_job_id')
                        ->label('Production Job')
                        ->relationship('productionJob', 'job_no')
                        ->getOptionLabelFromRecordUsing(
                            fn (ProductionJob $record): string => sprintf(
                                'Job #%d · %s · %s · %s',
                                $record->id,
                                $record->action?->name ?? 'Action',
                                $record->production?->product?->name ?? 'Product',
                                $record->worker?->name ?? 'Worker',
                            ),
                        )
                        ->searchable()
                        ->preload()
                        ->disabledOn('edit')
                        ->dehydrated()
                        ->required(),

                    DatePicker::make('return_date')
                        ->default(now())
                        ->required(),

                    TextInput::make('return_weight')
                        ->label('Return Weight')
                        ->numeric()
                        ->minValue(0)
                        ->suffix('kg')
                        ->default(0)
                        ->required(),

                    TextInput::make('feed_weight')
                        ->label('Feed Weight')
                        ->numeric()
                        ->minValue(0)
                        ->suffix('kg')
                        ->default(0)
                        ->required(),

                    TextInput::make('reject_weight')
                        ->label('Reject Weight')
                        ->numeric()
                        ->minValue(0)
                        ->suffix('kg')
                        ->default(0)
                        ->required(),

                    TextInput::make('good_pcs')
                        ->label('Good Pieces')
                        ->numeric()
                        ->integer()
                        ->minValue(0)
                        ->default(0)
                        ->required(),

                    TextInput::make('rate')
                        ->label('Rate')
                        ->prefix('₹')
                        ->disabled()
                        ->visibleOn('edit'),

                    TextInput::make('amount')
                        ->label('Amount')
                        ->prefix('₹')
                        ->disabled()
                        ->visibleOn('edit'),

                    Textarea::make('remarks')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
