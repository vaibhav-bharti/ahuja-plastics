<?php

namespace App\Services;

use App\Models\RawMaterialStock;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Consume Material
     */
    public static function consumeMaterial(
        int $rawMaterialId,
        float $quantity,
        string $referenceType,
        int $referenceId,
        ?string $remarks = null
    ): void {

        DB::transaction(function () use (

            $rawMaterialId,
            $quantity,
            $referenceType,
            $referenceId,
            $remarks

        ) {

            $remaining = $quantity;

            $stocks = RawMaterialStock::where('raw_material_id', $rawMaterialId)
                ->where('status', true)
                ->where('available_qty', '>', 0)
                ->orderBy('purchase_date')   // FIFO (baad me change kar sakte hain)
                ->lockForUpdate()
                ->get();

            foreach ($stocks as $stock) {

                if ($remaining <= 0) {
                    break;
                }

                $consumeQty = min($stock->available_qty, $remaining);

                $stock->available_qty -= $consumeQty;

                $stock->save();

                StockTransaction::create([

                    'raw_material_stock_id' => $stock->id,

                    'transaction_type' => 'OUT',

                    'quantity' => $consumeQty,

                    'balance_qty' => $stock->available_qty,

                    'reference_type' => $referenceType,

                    'reference_id' => $referenceId,

                    'remarks' => $remarks,

                    'created_by' => auth()->id(),

                ]);

                $remaining -= $consumeQty;
            }

            if ($remaining > 0) {

                throw new Exception(
                    'Insufficient stock available.'
                );

            }

        });

    }

    /**
     * Opening Stock Entry
     */
    public static function addStock(
        RawMaterialStock $stock
    ): void {

        StockTransaction::create([

            'raw_material_stock_id' => $stock->id,

            'transaction_type' => 'IN',

            'quantity' => $stock->purchase_qty,

            'balance_qty' => $stock->available_qty,

            'reference_type' => 'Purchase',

            'reference_id' => $stock->id,

            'remarks' => 'Opening Stock',

            'created_by' => auth()->id(),

        ]);

    }

    /**
     * Stock Adjustment
     */
    public static function adjustStock(
        RawMaterialStock $stock,
        float $quantity,
        string $remarks = null
    ): void {

        DB::transaction(function () use (

            $stock,
            $quantity,
            $remarks

        ) {

            $stock->available_qty += $quantity;

            $stock->save();

            StockTransaction::create([

                'raw_material_stock_id' => $stock->id,

                'transaction_type' => 'ADJUSTMENT',

                'quantity' => $quantity,

                'balance_qty' => $stock->available_qty,

                'reference_type' => 'Adjustment',

                'reference_id' => $stock->id,

                'remarks' => $remarks,

                'created_by' => auth()->id(),

            ]);

        });

    }
}