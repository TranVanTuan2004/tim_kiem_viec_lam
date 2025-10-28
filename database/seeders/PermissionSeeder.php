<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Job Postings Management
            'view jobs',
            'create jobs',
            'edit jobs',
            'delete jobs',
            'publish jobs',
            'approve jobs', // Admin approves jobs

            // Applications Management
            'view applications',
            'view own applications', // Candidate sees their own
            'view job applications', // Employer sees applications for their jobs
            'update application status',

            // Company Management
            'view companies',
            'create company',
            'edit own company',
            'edit any company', // Admin can edit any
            'delete companies',

            // Candidate Profile
            'view profiles',
            'edit own profile',
            'view any profile', // Employer/Admin can view any

            // Reviews
            'view reviews',
            'create review',
            'edit own review',
            'delete any review', // Admin can delete any

            // Messages/Chat
            'send messages',
            'view messages',
            
            // Subscriptions
            'view subscriptions',
            'manage subscriptions',

            // Reports
            'view reports',
            'create report',
            'resolve reports', // Admin resolves reports

            // Skills & Industries (Admin only)
            'manage skills',
            'manage industries',

            // Service Packages & Subscriptions
            'view packages',
            'manage packages', // Admin manages packages
            'subscribe to packages',
            'view own subscription',

            // Analytics
            'view analytics',
            'view own analytics', // Employer sees their stats
            'view system analytics', // Admin sees everything

            // Saved Jobs
            'save jobs',
            'view saved jobs',

            // Job Alerts
            'manage job alerts',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $adminRole = Role::where('name', 'Admin')->first();
        $employerRole = Role::where('name', 'Employer')->first();
        $candidateRole = Role::where('name', 'Candidate')->first();

        if ($adminRole) {
            // Admin gets ALL permissions
            $allPermissions = Permission::all()->pluck('name')->toArray();
            $permissionsToExclude = ['view subscriptions'];
            $adminPermissions = array_diff($allPermissions, $permissionsToExclude);
            $adminRole->givePermissionTo($adminPermissions);
        }

        if ($employerRole) {
            $employerRole->givePermissionTo([
                // Job management
                'view jobs',
                'create jobs',
                'edit jobs',
                'delete jobs',
                'publish jobs',

                // Applications for their jobs
                'view job applications',
                'update application status',

                // Their company
                'create company',
                'edit own company',

                // View profiles
                'view profiles',
                'view any profile',

                // Reviews
                'view reviews',

                // Messages
                'send messages',
                'view messages',
                
                // Subscriptions
                'view subscriptions',
                'manage subscriptions',

                // Reports
                'create report',

                // Packages
                'view packages',
                'subscribe to packages',
                'view own subscription',

                // Analytics
                'view own analytics',
            ]);
        }

        if ($candidateRole) {
            $candidateRole->givePermissionTo([
                // View jobs
                'view jobs',

                // Their applications
                'view own applications',

                // Their profile
                'edit own profile',

                // Reviews
                'view reviews',
                'create review',
                'edit own review',

                // Messages
                'send messages',
                'view messages',

                // Reports
                'create report',

                // Saved jobs
                'save jobs',
                'view saved jobs',

                // Job alerts
                'manage job alerts',
            ]);
        }
    }
}
