<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_action_rates', function (Blueprint $table) {

            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('action_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->decimal('rate', 10, 2);

            $table->boolean('status')->default(true);

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique([
                'product_id',
                'action_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_action_rates');
    }
};