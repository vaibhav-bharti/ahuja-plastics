<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_jobs', function (Blueprint $table) {
            $table->id();

            $table->string('job_no')->unique();

            $table->foreignId('production_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('action_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('worker_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->dateTime('issued_at');
            $table->decimal('issued_weight', 12, 3);

            $table->decimal('returned_weight_total', 12, 3)->default(0);
            $table->decimal('feed_weight_total', 12, 3)->default(0);
            $table->decimal('reject_weight_total', 12, 3)->default(0);
            $table->unsignedBigInteger('good_pcs_total')->default(0);

            $table->enum('status', ['Pending', 'Partial', 'Completed', 'Cancelled'])
                ->default('Pending');

            $table->text('remarks')->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

            $table->index(['production_id', 'status']);
            $table->index(['worker_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_jobs');
    }
};
