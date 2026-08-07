<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table): void {
            $table->timestamp('reversed_at')->nullable()->index()->after('remarks');
        });
    }

    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table): void {
            $table->dropColumn('reversed_at');
        });
    }
};
