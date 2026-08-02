<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ahuja.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('Admin@123'),
            ]
        );

        $this->call([
            // RolesAndPermissionsSeeder::class,
            MasterSeeder::class,
        ]);
    }
}