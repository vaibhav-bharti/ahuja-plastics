<?php

namespace App\Filament\Resources\Deflashings;

use App\Filament\Resources\Deflashings\Pages\CreateDeflashing;
use App\Filament\Resources\Deflashings\Pages\EditDeflashing;
use App\Filament\Resources\Deflashings\Pages\ListDeflashings;
use App\Filament\Resources\Deflashings\Pages\ViewDeflashing;
use App\Filament\Resources\Deflashings\Schemas\DeflashingForm;
use App\Filament\Resources\Deflashings\Schemas\DeflashingInfolist;
use App\Filament\Resources\Deflashings\Tables\DeflashingsTable;
use App\Models\Deflashing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeflashingResource extends Resource
{
    protected static ?string $model = Deflashing::class;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DeflashingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DeflashingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeflashingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeflashings::route('/'),
            'create' => CreateDeflashing::route('/create'),
            'view' => ViewDeflashing::route('/{record}'),
            'edit' => EditDeflashing::route('/{record}/edit'),
        ];
    }
}
