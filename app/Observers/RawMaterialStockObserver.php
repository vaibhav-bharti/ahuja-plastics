<?php

namespace App\Observers;

use App\Models\RawMaterialStock;
use App\Models\StockTransaction;

class RawMaterialStockObserver
{
    /**
     * Before Create
     */
    public function creating(RawMaterialStock $stock): void
    {
        /*
        |--------------------------------------------------------------------------
        | Auto Calculations
        |--------------------------------------------------------------------------
        */

        $stock->available_qty = $stock->purchase_qty;

        $stock->total_amount = round(
            $stock->purchase_qty * $stock->purchase_price,
            2
        );

        $stock->created_by ??= auth()->id();

        $stock->status ??= true;
    }

    /**
     * After Create
     */
    public function created(RawMaterialStock $stock): void
    {
        /*
        |--------------------------------------------------------------------------
        | Opening Stock Ledger
        |--------------------------------------------------------------------------
        */

        StockTransaction::create([

            'raw_material_stock_id' => $stock->id,

            'transaction_type' => 'IN',

            'quantity' => $stock->purchase_qty,

            'balance_qty' => $stock->available_qty,

            'reference_type' => 'Opening Stock',

            'reference_id' => $stock->id,

            'remarks' => 'Opening stock entry',

            'created_by' => auth()->id(),

        ]);
    }

    /**
     * Before Update
     */
    public function updating(RawMaterialStock $stock): void
    {
        /*
        |--------------------------------------------------------------------------
        | Total Amount
        |--------------------------------------------------------------------------
        */

        $stock->total_amount = round(

            $stock->purchase_qty * $stock->purchase_price,

            2

        );
    }
}