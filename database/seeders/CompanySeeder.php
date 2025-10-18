<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Try to associate company with the employer user created in DatabaseSeeder
        $employer = User::where('email', 'employer@example.com')->first();

        // Fallback to the first user if specific employer not found
        $userId = $employer ? $employer->id : User::first()?->id;

        // Ensure we have a user id (if not, abort to avoid DB error)
        if (! $userId) {
            $this->command->info('No users found; skipping CompanySeeder');
            return;
        }

        // Sử dụng updateOrCreate để tránh lỗi trùng lặp
        Company::updateOrCreate(
            [
                // Laravel sẽ tìm một công ty có company_slug này
                'company_slug' => 'techcorp-vietnam'
            ],
            [
                // Nếu tìm thấy, nó sẽ cập nhật với dữ liệu này.
                // Nếu không tìm thấy, nó sẽ tạo mới với toàn bộ dữ liệu này.
                'user_id' => $userId,
                'company_name' => 'TechCorp Vietnam',
                'description' => 'Công ty công nghệ giả lập cho mục đích test',
                'website' => 'https://techcorp.example',
                'city' => 'Hà Nội',
                'province' => 'Hà Nội',
                'address' => '123 Đường Láng, Đống Đa, Hà Nội',
                'size' => '100-500',
                'industry' => 'Software',
                'rating' => 4.5,
                'total_reviews' => 120,
                'is_verified' => true,
            ]
        );
    }
}
