<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'Admin',
                'Employee',
                'Supervisor',
                'Operator',
                'Worker',
                'Manager',
            ])->default('Worker')->change();
        });

        DB::table('users')
            ->where('email', 'admin@ahuja.com')
            ->where('role', 'Worker')
            ->update(['role' => 'Admin']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('email', 'admin@ahuja.com')
            ->where('role', 'Admin')
            ->update(['role' => 'Worker']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'Admin',
                'Supervisor',
                'Operator',
                'Worker',
                'Manager',
            ])->default('Worker')->change();
        });
    }
};
