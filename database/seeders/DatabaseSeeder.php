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

        // Create or get test admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create or get test employer user
        $employer = User::firstOrCreate(
            ['email' => 'employer@example.com'],
            [
                'name' => 'Employer User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        if (! $employer->hasRole('employer')) {
            $employer->assignRole('employer');
        }

        // Create or get test candidate user
        $candidate = User::firstOrCreate(
            ['email' => 'candidate@example.com'],
            [
                'name' => 'Candidate User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        if (! $candidate->hasRole('candidate')) {
            $candidate->assignRole('candidate');
        }

        // Seed a test company and job posting
        $this->call([
            \Database\Seeders\CompanySeeder::class,
            \Database\Seeders\JobPostingSeeder::class,
        ]);
    }
}
