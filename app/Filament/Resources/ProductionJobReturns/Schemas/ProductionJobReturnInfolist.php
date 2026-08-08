<?php

namespace App\Filament\Resources\ProductionJobReturns\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductionJobReturnInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Return Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('productionJob.display_job_no')->label('Job No.'),
                    TextEntry::make('productionJob.action.name')->label('Action'),
                    TextEntry::make('return_date')->date('d M Y'),
                    TextEntry::make('return_weight')->suffix(' kg'),
                    TextEntry::make('feed_weight')->suffix(' kg'),
                    TextEntry::make('reject_weight')->suffix(' kg'),
                    TextEntry::make('good_pcs')->label('Good Pieces'),
                    TextEntry::make('rate')->money('INR'),
                    TextEntry::make('amount')->money('INR'),
                    TextEntry::make('remarks')->columnSpanFull(),
                ]),
        ]);
    }
}
