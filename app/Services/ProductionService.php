<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Shift;

class ProductionService
{
    /**
     * Product Snapshot
     */
    public static function productSnapshot(int $productId): array
    {
        $product = Product::with('mould')->find($productId);

        if (! $product || ! $product->mould) {
            return [];
        }

        return [

            'cavity'          => $product->mould->cavity,

            'cycle_time'      => $product->mould->cycle_time,

        ];
    }

    /**
     * Shift Snapshot
     */
    public static function shiftSnapshot(int $shiftId): array
    {
        $shift = Shift::find($shiftId);

        if (! $shift) {
            return [];
        }

        return [

            'shift_start' => $shift->start_time,

            'shift_end'   => $shift->end_time,

        ];
    }

    /**
     * Load all snapshot values
     */
    public static function calculate(
        int $productId,
        int $shiftId
    ): array {

        $product = self::productSnapshot($productId);

        $shift = self::shiftSnapshot($shiftId);

        if (empty($product) || empty($shift)) {
            return [];
        }

        $plannedQuantity = ProductionCalculator::plannedQuantity(

            $product['cycle_time'],

            $product['cavity'],

            $shift['shift_start'],

            $shift['shift_end']

        );

        $predictedCounter = ProductionCalculator::predictedCounter(

            $product['cycle_time'],

            $shift['shift_start'],

            $shift['shift_end']

        );

        return [

            /*
            |--------------------------------------------------------------------------
            | Snapshot
            |--------------------------------------------------------------------------
            */

            'cavity'             => $product['cavity'],

            'cycle_time'         => $product['cycle_time'],

            'shift_start'        => $shift['shift_start'],

            'shift_end'          => $shift['shift_end'],

            /*
            |--------------------------------------------------------------------------
            | Auto Calculated
            |--------------------------------------------------------------------------
            */

            'planned_quantity'   => $plannedQuantity,

            'predicted_counter'  => $predictedCounter,

        ];
    }

    /**
     * Calculate Production Result
     */
    public static function productionResult(
        int $plannedQuantity,
        int $actualCounter,
        int $cavity
    ): array {

        $actualProduction = ProductionCalculator::actualProduction(

            $actualCounter,

            $cavity

        );

        return [

            'actual_production' => $actualProduction,

            'production_difference' => ProductionCalculator::productionDifference(

                $plannedQuantity,

                $actualProduction

            ),

        ];
    }

    /**
     * Total Material Used
     */
    public static function totalMaterial(array $materials): float
    {
        return round(

            collect($materials)

                ->sum(fn ($item) => (float) ($item['quantity'] ?? 0)),

            3

        );
    }

    /**
     * Total Downtime
     */
    public static function totalDowntime(array $downtimes): int
    {
        return collect($downtimes)

            ->sum(fn ($item) => (int) ($item['total_minutes'] ?? 0));
    }
}