<?php

namespace App\Filament\Resources\Users\Tables;

use App\Support\EmployeeCredentials;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('id', 'desc')

            ->columns([

                TextColumn::make('employee_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('role')
                    ->badge()
                    ->sortable(),

                TextColumn::make('department')
                    ->badge()
                    ->sortable(),

                TextColumn::make('salary_type')
                    ->badge(),

                TextColumn::make('salary')
                    ->money('INR')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->since()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([

            ])

            ->recordActions([

                ViewAction::make(),

                EmployeeCredentials::whatsAppAction(),

                EditAction::make(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);
    }
}
