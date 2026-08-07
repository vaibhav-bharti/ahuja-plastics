<?php

namespace App\Filament\Resources\StockTransactions;

use App\Filament\Resources\StockTransactions\Pages\ListStockTransactions;
use App\Filament\Resources\StockTransactions\Pages\ViewStockTransaction;
use App\Filament\Resources\StockTransactions\Schemas\StockTransactionInfolist;
use App\Filament\Resources\StockTransactions\Tables\StockTransactionsTable;
use App\Models\StockTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StockTransactionResource extends Resource
{
    protected static ?string $model = StockTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $navigationLabel = 'Stock Transactions';

    protected static ?string $modelLabel = 'Stock Transaction';

    protected static ?string $pluralModelLabel = 'Stock Transactions';

    protected static ?int $navigationSort = 2;

    public static function infolist(Schema $schema): Schema
    {
        return StockTransactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockTransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockTransactions::route('/'),
            'view' => ViewStockTransaction::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
