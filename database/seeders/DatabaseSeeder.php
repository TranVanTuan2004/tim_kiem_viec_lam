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
            PermissionSeeder::class,
        ]);

        // Create test users BEFORE seeding companies and jobs
        // This ensures CompanySeeder can find the employer user
        
        // Create or get test admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_active' => true,
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
                'is_active' => true,
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
                'is_active' => true,
            ]
        );
        if (! $candidate->hasRole('Candidate')) {
            $candidate->assignRole('Candidate');
        }

        // Now seed data that depends on users
        $this->call([
            IndustrySeeder::class,
            CompanySeeder::class,
            JobPostingSeeder::class,
            ServicePackageSeeder::class,
            HomepageSectionSeeder::class,
            BannerSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}