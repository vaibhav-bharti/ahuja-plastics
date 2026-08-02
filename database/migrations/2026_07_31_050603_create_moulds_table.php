<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moulds', function (Blueprint $table) {
            $table->id();
            $table->string('mould_no')->unique();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->unsignedInteger('cavity');
            $table->unsignedInteger('cycle_time')->comment('Seconds');
            $table->boolean('status')->default(true);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moulds');
    }
};