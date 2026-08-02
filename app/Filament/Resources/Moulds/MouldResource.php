<?php

namespace App\Filament\Resources\Moulds;

use App\Filament\Resources\Moulds\Pages\CreateMould;
use App\Filament\Resources\Moulds\Pages\EditMould;
use App\Filament\Resources\Moulds\Pages\ListMoulds;
use App\Filament\Resources\Moulds\Pages\ViewMould;
use App\Filament\Resources\Moulds\Schemas\MouldForm;
use App\Filament\Resources\Moulds\Schemas\MouldInfolist;
use App\Filament\Resources\Moulds\Tables\MouldsTable;
use App\Models\Mould;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MouldResource extends Resource
{
    protected static ?string $model = Mould::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Masters';

    protected static ?string $navigationLabel = 'Moulds';

    protected static ?string $modelLabel = 'Mould';

    protected static ?string $pluralModelLabel = 'Moulds';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return MouldForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MouldInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MouldsTable::configure($table);
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
            'index' => ListMoulds::route('/'),
            'create' => CreateMould::route('/create'),
            'view' => ViewMould::route('/{record}'),
            'edit' => EditMould::route('/{record}/edit'),
        ];
    }
}
