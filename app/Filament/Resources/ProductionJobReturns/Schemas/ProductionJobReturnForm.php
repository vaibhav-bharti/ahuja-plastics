<?php

namespace App\Filament\Resources\ProductionJobReturns\Schemas;

use App\Models\ProductionJob;
use App\Models\ProductionJobReturn;
use App\Services\ProductionJobService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                        ->live()
                        ->afterStateUpdated(fn (Get $get, Set $set): mixed => self::updateCalculatedValues($get, $set))
                        ->required(),

                    DatePicker::make('return_date')
                        ->default(now())
                        ->required(),

                    TextInput::make('return_weight')
                        ->label('Return Weight')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(function (Get $get, ?ProductionJobReturn $record): ?float {
                            return self::maximumFor($get, $record, 'return_weight');
                        })
                        ->suffix('kg')
                        ->default(0)
                        ->live()
                        ->afterStateUpdated(fn (Get $get, Set $set): mixed => self::updateCalculatedValues($get, $set))
                        ->helperText(function (Get $get, ?ProductionJobReturn $record): string {
                            $summary = self::returnSummary($get, $record);

                            return $summary
                                ? 'Combined Return + Feed + Reject balance: '.number_format($summary['accounted_remaining'], 3).' KG'
                                : 'Select a production job first.';
                        })
                        ->required(),

                    TextInput::make('feed_weight')
                        ->label('Feed Weight')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(function (Get $get, ?ProductionJobReturn $record): ?float {
                            return self::maximumFor($get, $record, 'feed_weight');
                        })
                        ->suffix('kg')
                        ->default(0)
                        ->live()
                        ->helperText(function (Get $get, ?ProductionJobReturn $record): string {
                            $summary = self::returnSummary($get, $record);

                            return $summary
                                ? 'Combined Return + Feed + Reject balance: '.number_format($summary['accounted_remaining'], 3).' KG'
                                : 'Select a production job first.';
                        })
                        ->required(),

                    TextInput::make('reject_weight')
                        ->label('Reject Weight')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(function (Get $get, ?ProductionJobReturn $record): ?float {
                            return self::maximumFor($get, $record, 'reject_weight');
                        })
                        ->suffix('kg')
                        ->default(0)
                        ->live()
                        ->helperText(function (Get $get, ?ProductionJobReturn $record): string {
                            $summary = self::returnSummary($get, $record);

                            return $summary
                                ? 'Combined Return + Feed + Reject balance: '.number_format($summary['accounted_remaining'], 3).' KG'
                                : 'Select a production job first.';
                        })
                        ->required(),

                    TextInput::make('good_pcs')
                        ->label('Good Pieces')
                        ->numeric()
                        ->integer()
                        ->minValue(0)
                        ->default(0)
                        ->disabled()
                        ->dehydrated(false)
                        ->afterStateHydrated(fn (Get $get, Set $set): mixed => self::updateCalculatedValues($get, $set))
                        ->helperText('Calculated from return weight and the product PCS per KG.')
                        ->required(),

                    TextInput::make('rate')
                        ->label('Rate')
                        ->prefix('₹')
                        ->disabled()
                        ->dehydrated(false)
                        ->afterStateHydrated(fn (Get $get, Set $set): mixed => self::updateCalculatedValues($get, $set)),

                    TextInput::make('amount')
                        ->label('Amount')
                        ->prefix('₹')
                        ->disabled()
                        ->dehydrated(false),

                    Textarea::make('remarks')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    protected static function updateCalculatedValues(Get $get, Set $set): void
    {
        $jobId = $get('production_job_id');
        $returnWeight = (float) ($get('return_weight') ?: 0);
        $job = $jobId
            ? ProductionJob::query()->with('production.product:id,pcs_per_kg')->find($jobId)
            : null;
        $pcsPerKg = (float) ($job?->production?->product?->pcs_per_kg ?? 0);

        $set('good_pcs', (int) round($returnWeight * $pcsPerKg));

        // An edited return keeps its stored rate snapshot; a new return uses
        // the active product/action rate selected by the job.
        $rate = $get('rate');
        if (($rate === null || $rate === '') && $job) {
            $rate = app(ProductionJobService::class)->rateForJob($job);
            $set('rate', $rate ?? 0);
        }

        $set('amount', number_format($returnWeight * (float) ($rate ?: 0), 2, '.', ''));
    }

    /** @return array{accounted_remaining: float}|null */
    private static function returnSummary(Get $get, ?ProductionJobReturn $record = null): ?array
    {
        $jobId = (int) ($get('production_job_id') ?? 0);

        if (! $jobId) {
            return null;
        }

        return app(ProductionJobService::class)->returnSummary($jobId, $record?->id);
    }

    private static function maximumFor(Get $get, ?ProductionJobReturn $record, string $field): ?float
    {
        $summary = self::returnSummary($get, $record);
        if (! $summary) {
            return null;
        }

        $otherFields = array_diff(['return_weight', 'feed_weight', 'reject_weight'], [$field]);
        $otherWeight = array_sum(array_map(
            fn (string $name): float => (float) ($get($name) ?: 0),
            $otherFields,
        ));

        return max(0, round($summary['accounted_remaining'] - $otherWeight, 3));
    }
}
