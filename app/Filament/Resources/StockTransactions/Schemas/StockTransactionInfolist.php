<?php

namespace App\Filament\Resources\StockTransactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class StockTransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Transaction Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('created_at')->label('Date & Time')->dateTime('d M Y, h:i A'),
                    TextEntry::make('transaction_type')->label('Type')->badge(),
                    TextEntry::make('reference_type')
                        ->label('Source')
                        ->formatStateUsing(fn (?string $state): string => $state ? Str::headline(class_basename($state)) : 'Manual entry'),
                    TextEntry::make('stock.material.name')->label('Raw Material'),
                    TextEntry::make('stock.invoice_no')->label('Invoice No.')->placeholder('-'),
                    TextEntry::make('creator.name')->label('Created By')->placeholder('-'),
                    TextEntry::make('quantity')->suffix(' kg'),
                    TextEntry::make('balance_qty')->label('Balance After')->suffix(' kg'),
                    TextEntry::make('reference_id')->label('Reference ID')->placeholder('-'),
                    TextEntry::make('remarks')->placeholder('-')->columnSpanFull(),
                ]),
        ]);
    }
}
