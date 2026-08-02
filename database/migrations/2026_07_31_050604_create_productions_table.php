<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productions', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Production Details
            |--------------------------------------------------------------------------
            */

            $table->date('production_date');

            $table->foreignId('shift_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('machine_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('operator_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Snapshot Values
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('cavity');

            $table->unsignedInteger('cycle_time');

            $table->time('shift_start');

            $table->time('shift_end');

            /*
            |--------------------------------------------------------------------------
            | Auto Calculated
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('planned_quantity');

            $table->unsignedInteger('predicted_counter');

            /*
            |--------------------------------------------------------------------------
            | Worker Entry
            |--------------------------------------------------------------------------
            */

            $table->decimal('weight_per_shot', 10, 3)
                ->default(0);

            $table->unsignedInteger('actual_counter')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Calculated Result
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('actual_production')
                ->default(0);

            $table->integer('production_difference')
                ->default(0);
            
            $table->decimal('total_material_qty', 10, 3)->default(0);
            $table->unsignedInteger('total_downtime')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Other Information
            |--------------------------------------------------------------------------
            */

            $table->text('remarks')
                ->nullable();

            $table->boolean('status')
                ->default(true);

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};