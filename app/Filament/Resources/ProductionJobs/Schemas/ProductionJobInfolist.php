<?php

namespace App\Filament\Resources\ProductionJobs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductionJobInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Job Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('display_job_no')->label('Job No.'),
                    TextEntry::make('production.product.name')->label('Product'),
                    TextEntry::make('action.name'),
                    TextEntry::make('worker.name'),
                    TextEntry::make('issued_at')->dateTime('d M Y, h:i A'),
                    TextEntry::make('issued_weight')->label('Issued Weight')->suffix(' kg'),
                    TextEntry::make('returned_weight_total')->label('Received Weight')->suffix(' kg'),
                    TextEntry::make('pending_weight')->label('Pending Weight')->suffix(' kg'),
                    TextEntry::make('feed_weight_total')->suffix(' kg'),
                    TextEntry::make('reject_weight_total')->suffix(' kg'),
                    TextEntry::make('good_pcs_total')->label('Good Pieces'),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('remarks')->columnSpanFull(),
                ]),
        ]);
    }
}
