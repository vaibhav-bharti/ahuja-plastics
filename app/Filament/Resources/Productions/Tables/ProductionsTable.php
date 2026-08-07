<?php

namespace App\Filament\Resources\Productions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Production;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Production date is the primary business order; ID makes records
            // created for the same day deterministic, with the newest first.
            ->defaultSort(fn (Builder $query): Builder => $query
                ->orderByDesc('production_date')
                ->orderByDesc('id'))

            ->columns([

                TextColumn::make('production_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('shift.name')
                    ->label('Shift')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('machine.machine_no')
                    ->label('Machine')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('operator.name')
                    ->label('Operator')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('planned_quantity')
                    ->label('Planned Qty')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('actual_counter')
                    ->label('Actual Counter')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('production_difference')
                    ->label('Difference')
                    ->badge()
                    ->color(fn ($state) => $state < 0 ? 'danger' : ($state > 0 ? 'success' : 'gray'))
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Approval')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Approved' : 'Pending')
                    ->color(fn (bool $state): string => $state ? 'success' : 'warning'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y h:i A')
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([])

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Production $record): bool => auth()->user()?->isAdmin() && ! $record->status)
                    ->action(fn (Production $record) => DB::transaction(fn () => $record->update(['status' => true]))),
                DeleteAction::make()
                    ->action(fn (Production $record) => DB::transaction(fn () => $record->delete())),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            $records->each(fn (Production $record) => DB::transaction(fn () => $record->delete()));
                        }),
                ]),
            ]);
    }
}
