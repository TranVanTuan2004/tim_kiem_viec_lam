<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = [
            [
                'name' => 'Backend Developer',
                'icon' => 'Code',
                'description' => 'Phát triển hệ thống backend, API, database',
            ],
            [
                'name' => 'Frontend Developer',
                'icon' => 'Smartphone',
                'description' => 'Phát triển giao diện người dùng, web, mobile app',
            ],
            [
                'name' => 'Full Stack Developer',
                'icon' => 'Briefcase',
                'description' => 'Phát triển cả frontend và backend',
            ],
            [
                'name' => 'DevOps Engineer',
                'icon' => 'Cloud',
                'description' => 'Vận hành hệ thống, CI/CD, cloud infrastructure',
            ],
            [
                'name' => 'Data Engineer',
                'icon' => 'Database',
                'description' => 'Xử lý dữ liệu lớn, data warehouse, ETL',
            ],
            [
                'name' => 'Security Engineer',
                'icon' => 'Shield',
                'description' => 'Bảo mật hệ thống, an toàn thông tin',
            ],
            [
                'name' => 'Mobile Developer',
                'icon' => 'Smartphone',
                'description' => 'Phát triển ứng dụng di động iOS, Android',
            ],
            [
                'name' => 'UI/UX Designer',
                'icon' => 'Monitor',
                'description' => 'Thiết kế giao diện và trải nghiệm người dùng',
            ],
            [
                'name' => 'Product Manager',
                'icon' => 'Layers',
                'description' => 'Quản lý sản phẩm, roadmap, backlog',
            ],
            [
                'name' => 'QA/QC Engineer',
                'icon' => 'Terminal',
                'description' => 'Kiểm thử phần mềm, đảm bảo chất lượng',
            ],
        ];

        foreach ($industries as $industry) {
            Industry::updateOrCreate(
                ['slug' => Str::slug($industry['name'])],
                [
                    'name' => $industry['name'],
                    'icon' => $industry['icon'],
                    'description' => $industry['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
