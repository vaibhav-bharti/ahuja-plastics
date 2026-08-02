<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Machine;
use App\Models\Mould;
use App\Models\Product;
use App\Models\ProductActionRate;
use App\Models\RawMaterial;
use App\Models\Shift;
use Illuminate\Database\Seeder;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Machines
        |--------------------------------------------------------------------------
        */

        $machine = Machine::firstOrCreate(
            ['machine_no' => 'MC-01'],
            [
                'name' => 'Machine 1',
                'status' => true,
                'remarks' => null,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Shifts
        |--------------------------------------------------------------------------
        */

        $shift = Shift::firstOrCreate(
            ['name' => 'Morning'],
            [
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'status' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Mould
        |--------------------------------------------------------------------------
        */

        $mould = Mould::firstOrCreate(
            ['mould_no' => 'M-001'],
            [
                'name' => 'Demo Mould',
                'brand' => 'Ahuja',
                'cavity' => 8,
                'cycle_time' => 20,
                'status' => true,
                'remarks' => null,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Product
        |--------------------------------------------------------------------------
        */

        $product = Product::firstOrCreate(
            ['name' => 'Demo Product'],
            [
                'mould_id' => $mould->id,
                'pcs_per_kg' => 250,
                'status' => true,
                'remarks' => null,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Raw Materials
        |--------------------------------------------------------------------------
        */

        $fresh = RawMaterial::firstOrCreate(
            ['name' => 'TLX (ABS)'],
            [
                'type' => 'Fresh',
                'status' => true,
                'remarks' => null,
            ]
        );

        $reused = RawMaterial::firstOrCreate(
            ['name' => 'TLX (Reused)'],
            [
                'type' => 'Reused',
                'status' => true,
                'remarks' => null,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Actions
        |--------------------------------------------------------------------------
        */

        $actions = [

            'Production',

            'Deflashing',

            'Packing',

            'Loading',

            'Unloading',

        ];

        $sort = 1;

        foreach ($actions as $name) {

            $action = Action::firstOrCreate(

                ['name' => $name],

                [
                    'status' => true,
                    'remarks' => null,
                ]

            );

            ProductActionRate::firstOrCreate(

                [
                    'product_id' => $product->id,
                    'action_id' => $action->id,
                ],

                [
                    'rate' => 0,
                    'sort_order' => $sort++,
                    'status' => true,
                    'remarks' => null,
                ]

            );
        }
    }
}