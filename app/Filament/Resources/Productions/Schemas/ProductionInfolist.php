<?php

namespace App\Filament\Resources\Productions\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;

class ProductionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |----------------------------------------------------------------
                | Production Details
                |----------------------------------------------------------------
                */

                Section::make('Production Details')
                    ->icon(Heroicon::OutlinedClipboardDocumentList)
                    ->schema([

                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                            'lg' => 3,
                        ])
                            ->schema([

                                TextEntry::make('production_date')
                                    ->label('Production Date')
                                    ->date('d M Y')
                                    ->icon(Heroicon::OutlinedCalendarDays)
                                    ->iconColor('gray')
                                    ->weight(FontWeight::SemiBold),

                                TextEntry::make('shift.name')
                                    ->label('Shift')
                                    ->badge()
                                    ->color('info')
                                    ->icon(Heroicon::OutlinedClock),

                                TextEntry::make('machine.machine_no')
                                    ->label('Machine')
                                    ->badge()
                                    ->color('warning')
                                    ->icon(Heroicon::OutlinedCog6Tooth),

                                TextEntry::make('operator.name')
                                    ->label('Operator')
                                    ->badge()
                                    ->color('primary')
                                    ->icon(Heroicon::OutlinedUser),

                                TextEntry::make('product.name')
                                    ->label('Product')
                                    ->badge()
                                    ->color('success')
                                    ->icon(Heroicon::OutlinedCube),

                                TextEntry::make('product.mould.name')
                                    ->label('Mould')
                                    ->placeholder('-')
                                    ->weight(FontWeight::SemiBold)
                                    ->icon(Heroicon::OutlinedSquares2x2)
                                    ->iconColor('gray'),

                            ]),

                    ]),

                /*
                |----------------------------------------------------------------
                | Production Snapshot
                | (exactly what the Create form's "Auto Values" +
                | "Production Result" sections save)
                |----------------------------------------------------------------
                */

                Section::make('Production Snapshot')
                    ->icon(Heroicon::OutlinedSparkles)
                    ->schema([

                        Grid::make([
                            'default' => 2,
                            'sm' => 3,
                            'lg' => 5,
                        ])
                            ->schema([

                                TextEntry::make('cavity')
                                    ->label('Cavity')
                                    ->color('gray')
                                    ->weight(FontWeight::Medium),

                                TextEntry::make('cycle_time')
                                    ->label('Cycle Time')
                                    ->suffix(' Sec')
                                    ->color('gray')
                                    ->weight(FontWeight::Medium),

                                TextEntry::make('shift_start')
                                    ->label('Shift Start')
                                    ->color('gray')
                                    ->weight(FontWeight::Medium),

                                TextEntry::make('shift_end')
                                    ->label('Shift End')
                                    ->color('gray')
                                    ->weight(FontWeight::Medium),
                                // TextEntry::make('debug')->state(fn ($record) => dd($record->toArray())),
                                TextEntry::make('weight_per_shot')
                                    ->label('Weight / Shot')
                                    ->suffix(' KG')
                                    ->color('info')
                                    ->weight(FontWeight::Medium),

                                TextEntry::make('planned_quantity')
                                    ->label('Planned Quantity')
                                    ->badge()
                                    ->color('primary')
                                    ->size('lg')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('predicted_counter')
                                    ->label('Predicted Counter')
                                    ->badge()
                                    ->color('warning')
                                    ->size('lg')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('actual_counter')
                                    ->label('Actual (Machine) Counter')
                                    ->badge()
                                    ->color('success')
                                    ->size('lg')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('actual_production')
                                    ->label('Actual Production')
                                    ->badge()
                                    ->color('success')
                                    ->size('lg')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('production_difference')
                                    ->label('Production Difference')
                                    ->badge()
                                    ->size('lg')
                                    ->weight(FontWeight::Bold)
                                    // Green when on/ahead of plan, red when short —
                                    // the number tells its own story at a glance.
                                    ->color(fn (?string $state): string => match (true) {
                                        $state === null || $state === '' => 'gray',
                                        (float) $state >= 0 => 'success',
                                        default => 'danger',
                                    })
                                    ->icon(fn (?string $state) => (float) ($state ?? 0) >= 0
                                        ? Heroicon::OutlinedArrowTrendingUp
                                        : Heroicon::OutlinedArrowTrendingDown),

                            ]),

                    ]),

                /*
                |----------------------------------------------------------------
                | Materials Used
                |----------------------------------------------------------------
                */

                Section::make('Materials Used')
                    ->icon(Heroicon::OutlinedCube)
                    ->schema([

                        RepeatableEntry::make('materials')
                            ->label('')
                            ->schema([

                                TextEntry::make('rawMaterial.name')
                                    ->label('Raw Material')
                                    ->badge()
                                    ->color('success')
                                    ->icon(Heroicon::OutlinedBeaker),

                                TextEntry::make('quantity')
                                    ->label('Qty')
                                    ->suffix(' KG')
                                    ->weight(FontWeight::Bold)
                                    ->color('info'),

                                TextEntry::make('remarks')
                                    ->label('Remarks')
                                    ->placeholder('-')
                                    ->color('gray'),

                            ])
                            ->columns([
                                'default' => 1,
                                'sm' => 3,
                            ])
                            ->contained(false),

                        TextEntry::make('total_material_qty')
                            ->label('Total Material Used')
                            // Not a stored column — it's the live sum of the
                            // materials relationship, same math the form does.
                            ->state(fn ($record) => number_format(
                                $record->materials->sum('quantity'),
                                3
                            ).' KG')
                            ->badge()
                            ->color('info')
                            ->weight(FontWeight::Bold),

                    ]),

                /*
                |----------------------------------------------------------------
                | Downtime
                |----------------------------------------------------------------
                */

                Section::make('Downtime')
                    ->icon(Heroicon::OutlinedClock)
                    ->schema([

                        RepeatableEntry::make('downtimes')
                            ->label('')
                            ->schema([

                                TextEntry::make('start_time')
                                    ->label('Start')
                                    ->time('h:i A')
                                    ->color('gray')
                                    ->weight(FontWeight::Medium),

                                TextEntry::make('end_time')
                                    ->label('End')
                                    ->time('h:i A')
                                    ->color('gray')
                                    ->weight(FontWeight::Medium),

                                TextEntry::make('total_minutes')
                                    ->label('Duration')
                                    ->suffix(' min')
                                    ->badge()
                                    ->color('danger'),

                                TextEntry::make('reason')
                                    ->label('Reason')
                                    ->badge()
                                    ->color('warning')
                                    ->icon(Heroicon::OutlinedExclamationTriangle),

                                TextEntry::make('remarks')
                                    ->label('Remarks')
                                    ->placeholder('-')
                                    ->color('gray'),

                            ])
                            ->columns([
                                'default' => 1,
                                'sm' => 2,
                                'lg' => 5,
                            ])
                            ->contained(false),

                        TextEntry::make('total_downtime')
                            ->label('Total Downtime')
                            // Not a stored column — live sum of the downtimes
                            // relationship, same math the form does.
                            ->state(fn ($record) => $record->downtimes->sum('total_minutes').' Minutes')
                            ->badge()
                            ->color('danger')
                            ->weight(FontWeight::Bold),

                    ]),

                /*
                |----------------------------------------------------------------
                | Other Information
                |----------------------------------------------------------------
                */

                Section::make('Other Information')
                    ->icon(Heroicon::OutlinedInformationCircle)
                    ->schema([

                        TextEntry::make('remarks')
                            ->placeholder('No remarks added')
                            ->color('gray')
                            ->columnSpanFull(),

                        Grid::make([
                            'default' => 2,
                            'lg' => 4,
                        ])
                            ->schema([

                                IconEntry::make('status')
                                    ->label('Status')
                                    ->boolean()
                                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                                    ->falseIcon(Heroicon::OutlinedXCircle)
                                    ->trueColor('success')
                                    ->falseColor('danger'),

                                TextEntry::make('creator.name')
                                    ->label('Created By')
                                    ->badge()
                                    ->color('gray')
                                    ->icon(Heroicon::OutlinedUserCircle),

                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime('d M Y h:i A')
                                    ->color('gray')
                                    ->icon(Heroicon::OutlinedClock),

                                TextEntry::make('updated_at')
                                    ->label('Updated At')
                                    ->dateTime('d M Y h:i A')
                                    ->color('gray')
                                    ->icon(Heroicon::OutlinedClock),

                            ]),

                    ]),

            ]);
    }
}