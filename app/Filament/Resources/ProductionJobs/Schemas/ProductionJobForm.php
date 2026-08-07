<?php

namespace App\Filament\Resources\ProductionJobs\Schemas;

use App\Models\ProductActionRate;
use App\Models\Production;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ProductionJobForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Job Details')
                ->columns(2)
                ->schema([
                    Select::make('production_id')
                        ->label('Production')
                        ->relationship('production', 'id')
                        ->getOptionLabelFromRecordUsing(
                            fn (Production $record): string => sprintf(
                                '%s — %s',
                                $record->production_date->format('d M Y'),
                                $record->product?->name ?? "Production #{$record->id}",
                            ),
                        )
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('action_id', null))
                        ->required(),

                    Select::make('action_id')
                        ->label('Action')
                        ->options(function (Get $get): array {
                            $productionId = $get('production_id');

                            if (blank($productionId)) {
                                return [];
                            }

                            $productId = Production::query()->whereKey($productionId)->value('product_id');

                            return ProductActionRate::query()
                                ->where('product_id', $productId)
                                ->where('status', true)
                                ->with('action:id,name')
                                ->get()
                                ->mapWithKeys(fn (ProductActionRate $rate): array => [$rate->action_id => $rate->action->name])
                                ->all();
                        })
                        ->searchable()
                        ->required(),

                    Select::make('worker_id')
                        ->label('Worker')
                        ->relationship('worker', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    DateTimePicker::make('issued_at')
                        ->label('Issued At')
                        ->default(now())
                        ->seconds(false)
                        ->required(),

                    TextInput::make('issued_weight')
                        ->label('Issued Weight')
                        ->numeric()
                        ->minValue(0.001)
                        ->suffix('kg')
                        ->required(),

                    Textarea::make('remarks')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
