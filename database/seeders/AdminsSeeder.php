<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AdminsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure admin role exists
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create only one admin user
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'email_verified_at' => now(),
                'password' => app()->make(\Illuminate\Hashing\HashManager::class)->make('password'),
            ]
        );

        // Attach admin role if not attached
        if (! $user->roles()->where('name', 'admin')->exists()) {
            $user->roles()->attach($adminRole->id);
        }
    }
}
