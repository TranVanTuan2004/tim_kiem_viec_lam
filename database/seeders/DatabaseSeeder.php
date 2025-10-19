<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first (already done)
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        
        // Create test admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole('Admin');

        // Create test employer user
        $employer = User::firstOrCreate(
            ['email' => 'employer@example.com'],
            [
                'name' => 'Employer User',
                'email' => 'employer@example.com',
                'password' => bcrypt('password'),
            ]
        );
        $employer->assignRole('Employer');

        // Create test candidate user
        $candidate = User::firstOrCreate(
            ['email' => 'candidate@example.com'],
            [
                'name' => 'Candidate User',
                'email' => 'candidate@example.com',
                'password' => bcrypt('password'),
            ]
        );
        $candidate->assignRole('Candidate');
    }
}
