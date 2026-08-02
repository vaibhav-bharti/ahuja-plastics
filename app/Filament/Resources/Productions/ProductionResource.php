<?php

namespace App\Filament\Resources\Productions;

use App\Filament\Resources\Productions\Pages\CreateProduction;
use App\Filament\Resources\Productions\Pages\EditProduction;
use App\Filament\Resources\Productions\Pages\ListProductions;
use App\Filament\Resources\Productions\Pages\ViewProduction;
use App\Filament\Resources\Productions\Schemas\ProductionForm;
use App\Filament\Resources\Productions\Schemas\ProductionInfolist;
use App\Filament\Resources\Productions\Tables\ProductionsTable;
use App\Models\Production;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductionResource extends Resource
{
    protected static ?string $model = Production::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Production';

    protected static ?string $navigationLabel = 'Production';

    protected static ?string $modelLabel = 'Production';

    protected static ?string $pluralModelLabel = 'Productions';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ProductionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductions::route('/'),
            'create' => CreateProduction::route('/create'),
            'view' => ViewProduction::route('/{record}'),
            'edit' => EditProduction::route('/{record}/edit'),
        ];
    }
}