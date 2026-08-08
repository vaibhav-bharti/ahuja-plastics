<?php

namespace App\Filament\Resources\ProductionJobs\Schemas;

use App\Models\ProductActionRate;
use App\Models\Production;
use App\Models\ProductionJob;
use App\Services\ProductionJobService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
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
                        ->afterStateUpdated(function (Set $set): void {
                            $set('action_id', null);
                        })
                        ->required(),

                    Placeholder::make('production_allocation')
                        ->label('Production Allocation')
                        ->content(function (Get $get, ?ProductionJob $record): string {
                            $summary = self::allocationSummary($get, $record);

                            if ($summary === null) {
                                return 'Select a production to see its allocation.';
                            }

                            return sprintf(
                                'Production Output: %s KG · Already Assigned: %s KG · Remaining: %s KG',
                                number_format($summary['output'], 3),
                                number_format($summary['assigned'], 3),
                                number_format($summary['remaining'], 3),
                            );
                        })
                        ->columnSpanFull(),

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
                                ->whereHas('action', fn ($query) => $query->where('status', true))
                                ->with('action:id,name')
                                ->get()
                                ->mapWithKeys(fn (ProductActionRate $rate): array => [$rate->action_id => $rate->action->name])
                                ->all();
                        })
                        ->searchable()
                        ->live()
                        ->required(),

                    Placeholder::make('action_rate_preview')
                        ->label('Applicable Rate')
                        ->content(function (Get $get): string {
                            $productionId = (int) ($get('production_id') ?? 0);
                            $actionId = (int) ($get('action_id') ?? 0);

                            if (! $productionId || ! $actionId) {
                                return 'Select production and action.';
                            }

                            $rate = app(ProductionJobService::class)
                                ->rateForProductionAction($productionId, $actionId);

                            return $rate === null ? 'No active rate configured.' : '₹'.number_format((float) $rate, 2).' per KG';
                        }),

                    Select::make('worker_id')
                        ->label('Worker')
                        ->relationship('worker', 'name', fn ($query) => $query->where('status', true))
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
                        ->maxValue(function (Get $get, ?ProductionJob $record): ?float {
                            return self::allocationSummary($get, $record)['remaining'] ?? null;
                        })
                        ->suffix('kg')
                        ->helperText(function (Get $get, ?ProductionJob $record): string {
                            $summary = self::allocationSummary($get, $record);

                            return $summary
                                ? 'Maximum available: '.number_format($summary['remaining'], 3).' KG'
                                : 'Select a production first.';
                        })
                        ->required(),

                    Textarea::make('remarks')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    /** @return array{output: float, assigned: float, remaining: float}|null */
    private static function allocationSummary(Get $get, ?ProductionJob $record = null): ?array
    {
        $productionId = (int) ($get('production_id') ?? 0);

        if (! $productionId) {
            return null;
        }

        return app(ProductionJobService::class)->allocationSummary($productionId, $record?->id);
    }
}
