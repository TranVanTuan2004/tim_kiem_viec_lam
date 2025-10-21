<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call([
            RoleSeeder::class,
        ]);

        // Create or get test admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        if (! $admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
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
        if (! $employer->hasRole('Employer')) {
            $employer->assignRole('Employer');
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
        if (! $candidate->hasRole('Candidate')) {
            $candidate->assignRole('Candidate');
        }

        // Seed a test company and job posting
        $this->call([
            \Database\Seeders\CompanySeeder::class,
            \Database\Seeders\JobPostingSeeder::class,
        ]);
    }
}