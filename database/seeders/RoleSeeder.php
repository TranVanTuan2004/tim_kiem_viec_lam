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

        // Create roles
        $roles = [
            [
                'name' => 'Admin',
                'guard_name' => 'web',
            ],
            [
                'name' => 'Employer',
                'guard_name' => 'web',
            ],
            [
                'name' => 'Candidate',
                'guard_name' => 'web',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                ['guard_name' => $role['guard_name']]
            );
        }

    }
}