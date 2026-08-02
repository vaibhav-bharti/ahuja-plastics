<?php

namespace App\Filament\Resources\ProductActionRates;

use App\Filament\Resources\ProductActionRates\Pages\CreateProductActionRate;
use App\Filament\Resources\ProductActionRates\Pages\EditProductActionRate;
use App\Filament\Resources\ProductActionRates\Pages\ListProductActionRates;
use App\Filament\Resources\ProductActionRates\Pages\ViewProductActionRate;
use App\Filament\Resources\ProductActionRates\Schemas\ProductActionRateForm;
use App\Filament\Resources\ProductActionRates\Schemas\ProductActionRateInfolist;
use App\Filament\Resources\ProductActionRates\Tables\ProductActionRatesTable;
use App\Models\ProductActionRate;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductActionRateResource extends Resource
{
    protected static ?string $model = ProductActionRate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyRupee;

    protected static string|UnitEnum|null $navigationGroup = 'Masters';

    protected static ?string $navigationLabel = 'Product Action Rates';

    protected static ?string $modelLabel = 'Product Action Rate';

    protected static ?string $pluralModelLabel = 'Product Action Rates';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return ProductActionRateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductActionRateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductActionRatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductActionRates::route('/'),
            'create' => CreateProductActionRate::route('/create'),
            'view' => ViewProductActionRate::route('/{record}'),
            'edit' => EditProductActionRate::route('/{record}/edit'),
        ];
    }
}