<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();

            $table->enum('role', [
                'Admin',
                'Supervisor',
                'Operator',
                'Worker',
                'Manager',
            ])->default('Worker')->after('password');

            $table->enum('department', [
                'Production',
                'Deflashing',
                'Packing',
                'Store',
                'Accounts',
            ])->nullable()->after('role');

            $table->date('joining_date')
                ->nullable()
                ->after('department');

            $table->enum('salary_type', [
                'Monthly',
                'Daily',
                'Piece Rate',
            ])->nullable()->after('joining_date');

            $table->decimal('salary', 10, 2)
                ->default(0)
                ->after('salary_type');

            $table->text('remarks')
                ->nullable()
                ->after('salary');

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'role',

                'department',

                'joining_date',

                'salary_type',

                'salary',

                'remarks',

            ]);

        });
    }
};