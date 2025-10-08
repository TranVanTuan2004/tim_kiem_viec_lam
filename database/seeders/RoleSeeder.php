<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => Role::ADMIN,
                'description' => 'Quản trị viên hệ thống với toàn quyền truy cập',
                'is_active' => true,
            ],
            [
                'name' => 'Nhà tuyển dụng',
                'slug' => Role::EMPLOYER,
                'description' => 'Nhà tuyển dụng có thể đăng tin và quản lý ứng viên',
                'is_active' => true,
            ],
            [
                'name' => 'Ứng viên',
                'slug' => Role::CANDIDATE,
                'description' => 'Ứng viên tìm kiếm và ứng tuyển công việc',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }

        $this->command->info('Roles seeded successfully!');
    }
}