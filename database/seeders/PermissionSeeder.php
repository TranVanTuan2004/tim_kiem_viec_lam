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
            'view job applications',
            // User Management
            'view users',
        

            // Job Postings Management
            'view jobs',


            // Applications Management
            'view applications',


            // Company Management
            'view companies',
    
            // Candidate Profile
            'view profiles',
      
            // Reviews
            'view reviews',
 

            // Messages/Chat
            'send messages',
 
            
            // Subscriptions
            'view subscriptions',

            // Reports
            'view reports',
     

            // Skills & Industries (Admin only)
            'view skills',
            'view industries',

            // Service Packages & Subscriptions
            'view packages',
         
         

            // Analytics
            'view analytics',

            // Saved Jobs
            'save jobs',
            'view saved jobs',

            // Applications for their jobs
            'view job applications',

            // Their company
            'create company',

            // View profiles
            'view profiles',

            // Reviews
            'view reviews',

            // Messages
            'send messages',
            
            // Subscriptions
            'view subscriptions',

            // Reports
            'create report',

            // Packages
            'view packages',

            // Analytics
            'view own analytics',


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
                // Applications for their jobs
                'view job applications',

                    // Their company
                    'create company',

                // View profiles
                'view profiles',

                // Reviews
                'view reviews',

                // Messages
                'send messages',
                
                // Subscriptions
                'view subscriptions',

                // Reports
                'create report',

                // Packages
                'view packages',

                // Analytics
                'view own analytics',
            ]);
        }

        if ($candidateRole) {
            $candidateRole->givePermissionTo([

            ]);
        }
    }
}
