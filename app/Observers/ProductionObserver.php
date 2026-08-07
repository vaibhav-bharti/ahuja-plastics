<?php

namespace App\Observers;

use App\Models\Production;
use App\Services\ProductionCalculator;
use App\Services\ProductionService;
use App\Services\InventoryService;


class ProductionObserver
{
    /**
     * Handle the Production "creating" event.
     */
    public function creating(Production $production): void
    {
        $this->calculate($production);

        $production->created_by = auth()->id();

        $production->status ??= false;
    }

    /**
     * Handle the Production "updating" event.
     */
    public function updating(Production $production): void
    {
        $this->calculate($production);
    }

    /**
     * Restore stock before the database cascades production_materials. The
     * caller wraps deletion in a transaction, so a failed restore cancels the
     * delete instead of leaving inventory and production out of sync.
     */
    public function deleting(Production $production): void
    {
        $production->load('materials');
        InventoryService::reverseProduction($production->materials, $production);
    }
    /**
     * Calculate Snapshot & Production Values
     */
    protected function calculate(Production $production): void
    {
        
        $snapshot = ProductionService::calculate(

            $production->product_id,

            $production->shift_id

        );

        if (empty($snapshot)) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Snapshot Values
        |--------------------------------------------------------------------------
        */

        $production->cavity = $snapshot['cavity'];

        $production->cycle_time = $snapshot['cycle_time'];

        $production->shift_start = $snapshot['shift_start'];

        $production->shift_end = $snapshot['shift_end'];

        /*
        |--------------------------------------------------------------------------
        | Auto Calculated
        |--------------------------------------------------------------------------
        */

        $production->planned_quantity = $snapshot['planned_quantity'];

        $production->predicted_counter = $snapshot['predicted_counter'];

        /*
        |--------------------------------------------------------------------------
        | Worker Entry Defaults
        |--------------------------------------------------------------------------
        */

        $production->weight_per_shot ??= 0;

        $production->actual_counter ??= 0;

        /*
        |--------------------------------------------------------------------------
        | Production Result
        |--------------------------------------------------------------------------
        */

        $production->actual_production =
            ProductionCalculator::actualProduction(

                $production->actual_counter,

                $production->cavity

            );

        $production->production_difference =
            ProductionCalculator::productionDifference(

                $production->planned_quantity,

                $production->actual_production

            );

        /*
        |--------------------------------------------------------------------------
        | Totals
        |--------------------------------------------------------------------------
        */

        if ($production->relationLoaded('materials')) {

            $production->total_material_qty =
                ProductionService::totalMaterial(
                    $production->materials->toArray()
                );
        }

        if ($production->relationLoaded('downtimes')) {

            $production->total_downtime =
                ProductionService::totalDowntime(
                    $production->downtimes->toArray()
                );
        }
    }


}
