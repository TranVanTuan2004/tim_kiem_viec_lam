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
        // Seed roles first
        $this->call([
            RoleSeeder::class,
        ]);

        // User::factory(10)->create();

        // Create test admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole('admin');

        // Create test employer user
        $employer = User::factory()->create([
            'name' => 'Employer User',
            'email' => 'employer@example.com',
        ]);
        $employer->assignRole('employer');

        // Create test candidate user
        $candidate = User::factory()->create([
            'name' => 'Candidate User',
            'email' => 'candidate@example.com',
        ]);
        $candidate->assignRole('candidate');
    }
}
