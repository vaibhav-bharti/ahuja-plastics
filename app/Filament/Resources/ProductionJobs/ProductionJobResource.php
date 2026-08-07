<?php

namespace App\Filament\Resources\ProductionJobs;

use App\Filament\Resources\ProductionJobs\Pages\CreateProductionJob;
use App\Filament\Resources\ProductionJobs\Pages\EditProductionJob;
use App\Filament\Resources\ProductionJobs\Pages\ListProductionJobs;
use App\Filament\Resources\ProductionJobs\Pages\ViewProductionJob;
use App\Filament\Resources\ProductionJobs\Schemas\ProductionJobForm;
use App\Filament\Resources\ProductionJobs\Schemas\ProductionJobInfolist;
use App\Filament\Resources\ProductionJobs\Tables\ProductionJobsTable;
use App\Models\ProductionJob;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProductionJobResource extends Resource
{
    protected static ?string $model = ProductionJob::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|UnitEnum|null $navigationGroup = 'Production';

    protected static ?string $navigationLabel = 'Production Jobs';

    protected static ?string $recordTitleAttribute = 'job_no';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return ProductionJobForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductionJobInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductionJobsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductionJobs::route('/'),
            'create' => CreateProductionJob::route('/create'),
            'view' => ViewProductionJob::route('/{record}'),
            'edit' => EditProductionJob::route('/{record}/edit'),
        ];
    }
}
