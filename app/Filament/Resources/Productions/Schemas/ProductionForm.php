<?php

namespace App\Filament\Resources\Productions\Schemas;

use App\Models\Product;
use App\Models\RawMaterial;
use App\Services\ProductionService;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;


class ProductionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | Production Details
            |--------------------------------------------------------------------------
            */

            Section::make('Production Details')
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->columns([
                    'default' => 1,
                    'sm' => 2,
                ])
                ->schema([

                    DatePicker::make('production_date')
                        ->native(false)
                        ->default(now())
                        ->required(),

                    Select::make('shift_id')
                        ->relationship('shift', 'name')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::applyCalculatedValues($get, $set);
                        }),

                    Select::make('machine_id')
                        ->relationship('machine', 'machine_no')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('operator_id')
                        ->relationship('operator', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('product_id')
                        ->relationship('product', 'name')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::applyCalculatedValues($get, $set);
                        }),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Auto Values
            |--------------------------------------------------------------------------
            */

            Section::make('Auto Values')
                ->icon(Heroicon::OutlinedSparkles)
                ->columns([
                    'default' => 1,
                    'sm' => 2,
                    'lg' => 3,
                ])
                ->schema([

                    Placeholder::make('mould')
                        ->label('Mould')
                        ->content(
                            fn (Get $get) =>
                            Product::find($get('product_id'))
                                ?->mould
                                ?->name ?? '-'
                        )
                        ->columnSpanFull(),

                    TextInput::make('cavity')
                        ->disabled()
                        ->dehydrated()
                        ->numeric()
                        ->extraInputAttributes(['class' => 'text-base font-medium']),

                    TextInput::make('cycle_time')
                        ->suffix(' Sec')
                        ->disabled()
                        ->dehydrated()
                        ->extraInputAttributes(['class' => 'text-base font-medium']),

                    TextInput::make('shift_start')
                        ->disabled()
                        ->dehydrated()
                        ->extraInputAttributes(['class' => 'text-base font-medium']),

                    TextInput::make('shift_end')
                        ->disabled()
                        ->dehydrated()
                        ->extraInputAttributes(['class' => 'text-base font-medium']),

                    TextInput::make('planned_quantity')
                        ->label('Planned Quantity')
                        ->disabled()
                        ->dehydrated()
                        ->numeric()
                        ->extraInputAttributes(['class' => 'text-base font-medium'])
                        // Capture the "before downtime" base value whenever the
                        // form loads (edit page) so downtime recalculation always
                        // has something correct to subtract from.
                        ->afterStateHydrated(function (?string $state, Set $set) {
                            $set('base_planned_quantity', $state);
                        }),

                    TextInput::make('predicted_counter')
                        ->label('Predicted Counter')
                        ->disabled()
                        ->dehydrated()
                        ->numeric()
                        ->extraInputAttributes(['class' => 'text-base font-medium'])
                        ->afterStateHydrated(function (?string $state, Set $set) {
                            $set('base_predicted_counter', $state);
                        }),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Material Used
            |--------------------------------------------------------------------------
            */

            Section::make('Material Used')
                ->icon(Heroicon::OutlinedCube)
                ->description('Select all raw materials used in this production.')
                ->schema([

                    Repeater::make('materials')
                        ->relationship()
                        ->label('')
                        ->defaultItems(1)
                        ->collapsible()
                        ->cloneable()
                        ->addActionLabel('+ Add Material')
                        // Shows "Material Name — 5 KG" on the collapsed header
                        // instead of leaving the left side blank.
                        ->itemLabel(function (array $state): ?string {
                            if (empty($state['raw_material_id'])) {
                                return 'New Material';
                            }

                            $name = RawMaterial::find($state['raw_material_id'])
                                ?->name ?? 'Material';

                            $qty = $state['quantity'] ?? 0;

                            return "{$name} — {$qty} KG";
                        })
                        // ->live() keeps the repeater's state in sync on every
                        // row ADD / DELETE / CLONE so the Placeholder below
                        // (the single source of truth for the total) always
                        // re-renders with fresh data. No afterStateUpdated
                        // needed here anymore — the Placeholder recalculates
                        // straight from $get('materials') on every live pass.
                        ->live()
                        ->columns([
                            'default' => 2,
                            'lg' => 6,
                        ])
                        ->schema([

                            Select::make('raw_material_id')
                                ->label('Raw Material')
                                ->relationship('rawMaterial', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(['default' => 1, 'lg' => 4]),

                            TextInput::make('quantity')
                                ->label('Qty (KG)')
                                ->numeric()
                                ->inputMode('decimal')
                                ->step(0.001)
                                ->minValue(0.001)
                                ->default(0)
                                ->required()
                                // ->live(onBlur: true) is enough on its own to
                                // trigger the Placeholder's recalculation —
                                // no afterStateUpdated needed here anymore.
                                ->live(onBlur: true)
                                ->suffix('KG')
                                ->extraInputAttributes(['class' => 'text-base font-medium'])
                                ->columnSpan(['default' => 1, 'lg' => 2]),

                            // Hidden for now — not important enough to show yet.
                            Textarea::make('remarks')
                                ->rows(1)
                                ->placeholder('Optional')
                                ->columnSpanFull()
                                ->hidden(),

                        ]),

                    // Single source of truth for the material total. Always
                    // calculated live from $get('materials') — never reads
                    // or writes the productions.total_material_qty column.
                    // Works identically on Create (rows start empty) and
                    // Edit (rows are hydrated from the materials relationship)
                    // because both cases populate the 'materials' repeater
                    // state the same way.
                    Placeholder::make('total_material_qty')
                        ->label('Total Material Used')
                        ->content(function (Get $get): string {

                            $total = collect($get('materials') ?? [])
                                ->sum(fn ($row) => (float) ($row['quantity'] ?? 0));

                            return number_format($total, 3) . ' KG';
                        })
                        ->live(),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Downtime
            |--------------------------------------------------------------------------
            */

            Section::make('Downtime')
                ->icon(Heroicon::OutlinedClock)
                ->description('Add all production downtime details.')
                ->schema([

                    Repeater::make('downtimes')
                        ->relationship()
                        ->label('')
                        ->defaultItems(0)
                        ->collapsible()
                        ->cloneable()
                        ->addActionLabel('+ Add Downtime')
                        // Shows "2:00 PM - 2:30 PM · Power Failure" on the
                        // collapsed header instead of leaving the left side blank.
                        ->itemLabel(function (array $state): ?string {
                            $start = $state['start_time'] ?? null;
                            $end = $state['end_time'] ?? null;
                            $reason = $state['reason'] ?? null;

                            if (! $start && ! $end && ! $reason) {
                                return 'New Downtime';
                            }

                            $formatTime = function (?string $time): string {
                                if (! $time) {
                                    return '—';
                                }

                                try {
                                    return Carbon::parse($time)->format('g:i A');
                                } catch (\Throwable $e) {
                                    return $time;
                                }
                            };

                            $range = $formatTime($start).' - '.$formatTime($end);

                            return $reason
                                ? "{$range} · {$reason}"
                                : $range;
                        })
                        ->live()
                        // Catches row ADD / DELETE / CLONE — these don't change
                        // any single field's value, so the start_time/end_time
                        // afterStateUpdated hooks below never fire for them.
                        // This is what keeps Total Downtime (and predicted
                        // counter / planned quantity) in sync when a row is
                        // removed.
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::recalcDowntimeRow($get, $set, fromRoot: true);
                        })
                        ->columns([
                            'default' => 1,
                            'sm' => 3,
                        ])
                        ->schema([

                            // Start/end time + minutes get their own full row,
                            // 3 fields sharing the row instead of 12, so each
                            // one is wide enough to actually read.
                            TimePicker::make('start_time')
                                ->label('Start Time')
                                // Native = uses the phone's own time wheel/clock
                                // picker, which is much faster to use on mobile
                                // than typing or scrolling a custom dropdown.
                                ->native()
                                ->seconds(false)
                                ->live()
                                ->required()
                                ->extraInputAttributes(['class' => 'text-base font-medium'])
                                ->columnSpan(1)
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::recalcDowntimeRow($get, $set);
                                }),

                            TimePicker::make('end_time')
                                ->label('End Time')
                                ->native()
                                ->seconds(false)
                                ->live()
                                ->required()
                                ->extraInputAttributes(['class' => 'text-base font-medium'])
                                ->columnSpan(1)
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::recalcDowntimeRow($get, $set);
                                }),

                            TextInput::make('total_minutes')
                                ->label('Minutes')
                                ->numeric()
                                ->disabled()
                                ->dehydrated()
                                ->extraInputAttributes(['class' => 'text-base font-medium'])
                                ->columnSpan(1),

                            // Reason + remarks each get their own full-width row.
                            TextInput::make('reason')
                                ->required()
                                ->placeholder('Power Failure / Mould Cleaning')
                                ->columnSpanFull(),

                            Textarea::make('remarks')
                                ->rows(1)
                                ->placeholder('Optional')
                                ->columnSpanFull(),

                        ]),

                    TextInput::make('total_downtime')
                        ->label('Total Downtime')
                        ->disabled()
                        ->dehydrated(false)
                        ->suffix('Minutes')
                        ->extraInputAttributes(['class' => 'text-base font-medium'])
                        ->default(0),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Production Result
            |--------------------------------------------------------------------------
            */

            Section::make('Production Result')
                ->icon(Heroicon::OutlinedChartBar)
                ->columns([
                    'default' => 1,
                    'sm' => 2,
                ])
                ->schema([

                    // Weight & counter are the two fields the user actually
                    // types into — give them a full half-width row each so
                    // the value never gets visually clipped by the suffix.
                    TextInput::make('weight_per_shot')
                        ->label('Weight / Shot')
                        ->numeric()
                        ->inputMode('decimal')
                        ->step(0.001)
                        ->suffix('KG')
                        ->extraInputAttributes(['class' => 'text-base font-medium'])
                        ->required(),

                    TextInput::make('actual_counter')
                        ->label('Machine Counter')
                        ->numeric()
                        ->inputMode('numeric')
                        ->default(0)
                        ->live(onBlur: true)
                        ->extraInputAttributes(['class' => 'text-base font-medium'])
                        ->required()
                        ->afterStateUpdated(function (Get $get, Set $set) {

                            $result = ProductionService::productionResult(

                                (int) ($get('planned_quantity') ?: 0),

                                (int) ($get('actual_counter') ?: 0),

                                (int) ($get('cavity') ?: 0)

                            );

                            $set(
                                'actual_production',
                                $result['actual_production']
                            );

                            $set(
                                'production_difference',
                                $result['production_difference']
                            );

                        }),

                    TextInput::make('actual_production')
                        ->label('Actual Production')
                        ->disabled()
                        ->dehydrated()
                        ->numeric()
                        ->extraInputAttributes(['class' => 'text-base font-medium']),

                    TextInput::make('production_difference')
                        ->label('Production Difference')
                        ->disabled()
                        ->dehydrated()
                        ->numeric()
                        ->extraInputAttributes(['class' => 'text-base font-medium']),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */

            Section::make('Remarks')
                ->icon(Heroicon::OutlinedPencilSquare)
                ->schema([

                    Textarea::make('remarks')
                        ->rows(4)
                        ->columnSpanFull(),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Hidden / Working Fields
            |--------------------------------------------------------------------------
            */

            TextInput::make('created_by')
                ->default(fn () => auth()->id())
                ->hidden()
                ->dehydrated(),

            TextInput::make('status')
                ->default(true)
                ->hidden()
                ->dehydrated(),

            // These two are NOT real model columns — they just remember the
            // "before downtime" planned figures so downtime edits can always
            // subtract from the correct baseline instead of compounding.
            TextInput::make('base_predicted_counter')
                ->hidden()
                ->dehydrated(false),

            TextInput::make('base_planned_quantity')
                ->hidden()
                ->dehydrated(false),

        ]);
    }

    /**
     * Runs when shift or product changes.
     * Pulls fresh values from ProductionService and also stores them as the
     * "base" (pre-downtime) figures used later by the downtime calculation.
     */
    protected static function applyCalculatedValues(Get $get, Set $set): void
    {
        if (! $get('product_id') || ! $get('shift_id')) {
            return;
        }

        $values = ProductionService::calculate(
            (int) $get('product_id'),
            (int) $get('shift_id')
        );

        foreach ($values as $key => $value) {
            $set($key, $value);
        }

        $set('base_predicted_counter', $values['predicted_counter'] ?? 0);
        $set('base_planned_quantity', $values['planned_quantity'] ?? 0);

        // Shift/product changed, so any previously entered downtime no longer
        // applies to the new base — recalculate immediately.
        self::recalcDowntimeRow($get, $set, fromRoot: true);
    }

    /**
     * Fired from an individual downtime row (start_time / end_time).
     * Recomputes that row's minutes, the grand total downtime, and then
     * reduces predicted_counter / planned_quantity accordingly.
     *
     * $fromRoot lets this be reused directly from the shift/product handler,
     * where $get/$set are already at the form root instead of inside a
     * repeater item.
     */
    protected static function recalcDowntimeRow(Get $get, Set $set, bool $fromRoot = false): void
    {
        $prefix = $fromRoot ? '' : '../../';

        if (! $fromRoot) {
            // Calculate minutes for the current row from its own start/end time.
            $start = $get('start_time');
            $end = $get('end_time');
            $minutes = self::diffInMinutes($start, $end);
            $set('total_minutes', $minutes);
        }

        $rows = $get($prefix.'downtimes') ?? [];

        // Make sure every row's minutes are up to date (covers rows edited
        // via means other than the live start/end callbacks, e.g. on load).
        $rows = collect($rows)->map(function ($row) {
            $row['total_minutes'] = self::diffInMinutes(
                $row['start_time'] ?? null,
                $row['end_time'] ?? null
            );

            return $row;
        });

        $totalDowntime = $rows->sum('total_minutes');
        $set($prefix.'total_downtime', $totalDowntime);

        $cycleTime = (float) ($get($prefix.'cycle_time') ?: 0);
        $cavity = (int) ($get($prefix.'cavity') ?: 0);
        $basePredicted = (int) ($get($prefix.'base_predicted_counter') ?: 0);
        $basePlanned = (int) ($get($prefix.'base_planned_quantity') ?: 0);

        $lostShots = $cycleTime > 0
            ? (int) floor(($totalDowntime * 60) / $cycleTime)
            : 0;

        $newPredicted = max($basePredicted - $lostShots, 0);

        $newPlanned = $cavity > 0
            ? $newPredicted * $cavity
            : max($basePlanned - ($lostShots * $cavity), 0);

        $set($prefix.'predicted_counter', $newPredicted);
        $set($prefix.'planned_quantity', $newPlanned);
    }

    /**
     * Safely diff two "H:i" time strings in minutes, handling shifts that
     * cross midnight (e.g. start 22:00, end 02:00).
     */
    protected static function diffInMinutes(?string $start, ?string $end): int
    {
        if (empty($start) || empty($end)) {
            return 0;
        }

        try {
            $startTime = Carbon::parse($start);
            $endTime = Carbon::parse($end);

            if ($endTime->lessThanOrEqualTo($startTime)) {
                $endTime->addDay();
            }

            return (int) $startTime->diffInMinutes($endTime);
        } catch (\Throwable $e) {
            return 0;
        }
    }
}