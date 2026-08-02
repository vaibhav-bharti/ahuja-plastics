<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_material_stocks', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Material
            |--------------------------------------------------------------------------
            */

            $table->foreignId('raw_material_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Purchase
            |--------------------------------------------------------------------------
            */

            $table->date('purchase_date');

            $table->string('vendor_name');

            $table->string('invoice_no')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Quantity
            |--------------------------------------------------------------------------
            */

            $table->decimal('purchase_qty', 10, 3);

            $table->decimal('available_qty', 10, 3);

            /*
            |--------------------------------------------------------------------------
            | Price
            |--------------------------------------------------------------------------
            */

            $table->decimal('purchase_price', 10, 2);

            $table->decimal('total_amount', 12, 2);

            /*
            |--------------------------------------------------------------------------
            | Other
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
        Schema::dropIfExists('raw_material_stocks');
    }
};