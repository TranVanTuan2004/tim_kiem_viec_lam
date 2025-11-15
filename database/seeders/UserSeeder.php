<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        echo "✓ Admin user created: admin@example.com / password\n";

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

        echo "✓ Employer user created: employer@example.com / password\n";

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

        echo "✓ Candidate user created: candidate@example.com / password\n";
    }
}

