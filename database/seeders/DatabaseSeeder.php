<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\LeaveTypesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and leave types required by the application
        $this->call([
            RolesSeeder::class,
            LeaveTypesSeeder::class,
            AdminsSeeder::class,
            DepartmentsSeeder::class,
        ]);
    }
}
