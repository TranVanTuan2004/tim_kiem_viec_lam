<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'manage users',
            'manage companies',
            'manage jobs',
            'create jobs',
            'edit jobs',
            'delete jobs',
            'view applications',
            'manage applications',
            'create companies',
            'edit companies',
            'delete companies',
            'apply jobs',
            'view jobs',
            'manage profile',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $employerRole = Role::firstOrCreate(['name' => 'Employer', 'guard_name' => 'web']);
        $candidateRole = Role::firstOrCreate(['name' => 'Candidate', 'guard_name' => 'web']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());
        
        $employerRole->givePermissionTo([
            'create jobs',
            'edit jobs',
            'delete jobs',
            'view applications',
            'manage applications',
            'create companies',
            'edit companies',
            'manage profile',
        ]);

        $candidateRole->givePermissionTo([
            'apply jobs',
            'view jobs',
            'manage profile',
        ]);
    }
}