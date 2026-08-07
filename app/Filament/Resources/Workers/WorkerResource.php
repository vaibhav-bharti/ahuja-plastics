<?php

namespace App\Filament\Resources\Workers;

use App\Filament\Resources\Workers\Pages\CreateWorker;
use App\Filament\Resources\Workers\Pages\EditWorker;
use App\Filament\Resources\Workers\Pages\ListWorkers;
use App\Filament\Resources\Workers\Pages\ViewWorker;
use App\Models\Worker;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Masters';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Worker Details')
                ->columns(2)
                ->schema([
                    TextInput::make('worker_code')
                        ->label('Worker Code')
                        ->disabledOn('create')
                        ->dehydrated()
                        ->helperText('Generated automatically when the worker is created.'),
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('phone')->tel()->maxLength(30),
                    Toggle::make('status')->label('Active')->default(true),
                    Textarea::make('remarks')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Worker Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('worker_code'),
                    TextEntry::make('name'),
                    TextEntry::make('phone')->placeholder('-'),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('remarks')->placeholder('-')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('worker_code')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('phone')->searchable(),
                IconColumn::make('status')->label('Active')->boolean(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkers::route('/'),
            'create' => CreateWorker::route('/create'),
            'view' => ViewWorker::route('/{record}'),
            'edit' => EditWorker::route('/{record}/edit'),
        ];
    }
}
