<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_job_returns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('production_job_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->date('return_date');
            $table->decimal('return_weight', 12, 3)->default(0);
            $table->decimal('feed_weight', 12, 3)->default(0);
            $table->decimal('reject_weight', 12, 3)->default(0);
            $table->unsignedBigInteger('good_pcs')->default(0);

            // Snapshot copied from product_action_rates when the return is recorded.
            $table->decimal('rate', 12, 2);
            $table->decimal('amount', 14, 2);

            $table->text('remarks')->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

            $table->index(['production_job_id', 'return_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_job_returns');
    }
};
