<?php

namespace App\Filament\Resources\ProductionJobReturns;

use App\Filament\Resources\ProductionJobReturns\Pages\CreateProductionJobReturn;
use App\Filament\Resources\ProductionJobReturns\Pages\EditProductionJobReturn;
use App\Filament\Resources\ProductionJobReturns\Pages\ListProductionJobReturns;
use App\Filament\Resources\ProductionJobReturns\Pages\ViewProductionJobReturn;
use App\Filament\Resources\ProductionJobReturns\Schemas\ProductionJobReturnForm;
use App\Filament\Resources\ProductionJobReturns\Schemas\ProductionJobReturnInfolist;
use App\Filament\Resources\ProductionJobReturns\Tables\ProductionJobReturnsTable;
use App\Models\ProductionJobReturn;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProductionJobReturnResource extends Resource
{
    protected static ?string $model = ProductionJobReturn::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;

    protected static string|UnitEnum|null $navigationGroup = 'Production';

    protected static ?string $navigationLabel = 'Job Returns';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return ProductionJobReturnForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductionJobReturnInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductionJobReturnsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductionJobReturns::route('/'),
            'create' => CreateProductionJobReturn::route('/create'),
            'view' => ViewProductionJobReturn::route('/{record}'),
            'edit' => EditProductionJobReturn::route('/{record}/edit'),
        ];
    }
}
