<?php

namespace App\Filament\Resources\DeflashingJobs;

use App\Filament\Resources\DeflashingJobs\Pages\CreateDeflashingJob;
use App\Filament\Resources\DeflashingJobs\Pages\EditDeflashingJob;
use App\Filament\Resources\DeflashingJobs\Pages\ListDeflashingJobs;
use App\Filament\Resources\DeflashingJobs\Pages\ViewDeflashingJob;
use App\Filament\Resources\DeflashingJobs\Schemas\DeflashingJobForm;
use App\Filament\Resources\DeflashingJobs\Schemas\DeflashingJobInfolist;
use App\Filament\Resources\DeflashingJobs\Tables\DeflashingJobsTable;
use App\Models\DeflashingJob;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeflashingJobResource extends Resource
{
    protected static ?string $model = DeflashingJob::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DeflashingJobForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DeflashingJobInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeflashingJobsTable::configure($table);
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
            'index' => ListDeflashingJobs::route('/'),
            'create' => CreateDeflashingJob::route('/create'),
            'view' => ViewDeflashingJob::route('/{record}'),
            'edit' => EditDeflashingJob::route('/{record}/edit'),
        ];
    }
}
