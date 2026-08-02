<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | Basic Information
            |--------------------------------------------------------------------------
            */

            Section::make('Basic Information')
                ->columns(2)
                ->schema([

                    TextInput::make('employee_code')
                        ->label('Employee Code')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50),

                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->tel()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20),

                    Select::make('role')
                        ->options([
                            'Admin' => 'Admin',
                            'Supervisor' => 'Supervisor',
                            'Operator' => 'Operator',
                            'Worker' => 'Worker',
                            'Manager' => 'Manager',
                        ])
                        ->default('Worker')
                        ->live()
                        ->required(),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Employment Information
            |--------------------------------------------------------------------------
            */

            Section::make('Employment Information')
                ->columns(2)
                ->schema([

                    Select::make('department')
                        ->options([
                            'Production' => 'Production',
                            'Deflashing' => 'Deflashing',
                            'Packing' => 'Packing',
                            'Store' => 'Store',
                            'Accounts' => 'Accounts',
                        ])
                        ->required(),

                    DatePicker::make('joining_date')
                        ->default(now()),

                ]),
                            /*
            |--------------------------------------------------------------------------
            | Login Information
            |--------------------------------------------------------------------------
            */

            Section::make('Login Information')
                ->columns(2)
                ->schema([

                    TextInput::make('email')
                        ->email()
                        ->maxLength(255)
                        ->required(fn ($get) => in_array(
                            $get('role'),
                            ['Admin', 'Supervisor', 'Manager']
                        )),

                   TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->maxLength(255)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $operation, $get) =>
                            $operation === 'create'
                            && in_array($get('role'), ['Admin', 'Supervisor', 'Manager'])
                        )
                        ->visible(fn ($get) =>
                            in_array($get('role'), ['Admin', 'Supervisor', 'Manager'])
                        )

                ]),

            /*
            |--------------------------------------------------------------------------
            | Salary Information
            |--------------------------------------------------------------------------
            */

            Section::make('Salary Information')
                ->columns(2)
                ->schema([

                    Select::make('salary_type')
                        ->options([
                            'Monthly'   => 'Monthly',
                            'Daily'     => 'Daily',
                            'Piece Rate'=> 'Piece Rate',
                        ])
                        ->placeholder('Select Salary Type'),

                    TextInput::make('salary')
                        ->numeric()
                        ->default(0)
                        ->prefix('₹'),

                ]),
                            /*
            |--------------------------------------------------------------------------
            | Other Information
            |--------------------------------------------------------------------------
            */

            Section::make('Other Information')
                ->columns(2)
                ->schema([

                    Select::make('is_active')
                        ->label('Status')
                        ->options([
                            1 => 'Active',
                            0 => 'Inactive',
                        ])
                        ->default(1)
                        ->required(),

                    TextInput::make('last_login_at')
                        ->label('Last Login')
                        ->disabled()
                        ->dehydrated(false)
                        ->visibleOn('edit'),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */

            Section::make('Remarks')
                ->schema([

                    \Filament\Forms\Components\Textarea::make('remarks')
                        ->rows(4)
                        ->columnSpanFull(),

                ]),

        ]);
    }
}