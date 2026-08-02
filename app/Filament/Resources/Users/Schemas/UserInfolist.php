<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([

                        TextEntry::make('employee_code')
                            ->label('Employee Code'),

                        TextEntry::make('name'),

                        TextEntry::make('phone'),

                        TextEntry::make('email')
                            ->placeholder('-'),

                        TextEntry::make('role')
                            ->badge(),

                        TextEntry::make('department')
                            ->badge()
                            ->placeholder('-'),

                    ]),

                Section::make('Employment')
                    ->columns(2)
                    ->schema([

                        TextEntry::make('joining_date')
                            ->date('d M Y')
                            ->placeholder('-'),

                        TextEntry::make('salary_type')
                            ->badge()
                            ->placeholder('-'),

                        TextEntry::make('salary')
                            ->money('INR')
                            ->placeholder('-'),

                        IconEntry::make('is_active')
                            ->label('Status')
                            ->boolean(),

                    ]),

                Section::make('System Information')
                    ->columns(2)
                    ->schema([

                        TextEntry::make('last_login_at')
                            ->label('Last Login')
                            ->since()
                            ->placeholder('Never Logged In'),

                        TextEntry::make('created_at')
                            ->dateTime('d M Y h:i A'),

                        TextEntry::make('updated_at')
                            ->dateTime('d M Y h:i A'),

                    ]),

                Section::make('Remarks')
                    ->schema([

                        TextEntry::make('remarks')
                            ->placeholder('-')
                            ->columnSpanFull(),

                    ]),

            ]);
    }
}