<?php

namespace App\Filament\Resources\RawMaterialStocks;

use App\Filament\Resources\RawMaterialStocks\Pages\CreateRawMaterialStock;
use App\Filament\Resources\RawMaterialStocks\Pages\EditRawMaterialStock;
use App\Filament\Resources\RawMaterialStocks\Pages\ListRawMaterialStocks;
use App\Filament\Resources\RawMaterialStocks\Pages\ViewRawMaterialStock;
use App\Filament\Resources\RawMaterialStocks\Schemas\RawMaterialStockForm;
use App\Filament\Resources\RawMaterialStocks\Schemas\RawMaterialStockInfolist;
use App\Filament\Resources\RawMaterialStocks\Tables\RawMaterialStocksTable;
use App\Models\RawMaterialStock;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RawMaterialStockResource extends Resource
{
    protected static ?string $model = RawMaterialStock::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $navigationLabel = 'Stock Entry';

    protected static ?string $modelLabel = 'Stock Entry';

    protected static ?string $pluralModelLabel = 'Stock Entries';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return RawMaterialStockForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RawMaterialStockInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RawMaterialStocksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRawMaterialStocks::route('/'),
            'create' => CreateRawMaterialStock::route('/create'),
            'view' => ViewRawMaterialStock::route('/{record}'),
            'edit' => EditRawMaterialStock::route('/{record}/edit'),
        ];
    }
}