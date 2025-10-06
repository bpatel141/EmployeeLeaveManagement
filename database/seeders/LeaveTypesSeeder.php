<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Sick', 'description' => 'Sick leave', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Casual', 'description' => 'Casual leave', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('leave_types')->insertOrIgnore($types);
    }
}
