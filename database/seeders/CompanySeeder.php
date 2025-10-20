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

        // Tạo nhiều công ty mẫu
        $companies = [
            [
                'company_slug' => 'techcorp-vietnam',
                'company_name' => 'TechCorp Vietnam',
                'description' => 'Công ty công nghệ giả lập cho mục đích test. Chúng tôi chuyên về phát triển phần mềm và giải pháp công nghệ.',
                'website' => 'https://techcorp.example',
                'city' => 'Hà Nội',
                'province' => 'Hà Nội',
                'address' => '123 Đường Láng, Đống Đa, Hà Nội',
                'size' => '51-200',
                'industry' => 'Software',
                'rating' => 4.5,
                'total_reviews' => 120,
                'is_verified' => true,
            ],
            [
                'company_slug' => 'fpt-software',
                'company_name' => 'FPT Software',
                'description' => 'Công ty phần mềm hàng đầu Việt Nam với hơn 20 năm kinh nghiệm trong lĩnh vực công nghệ thông tin.',
                'website' => 'https://fptsoftware.com',
                'city' => 'Hà Nội',
                'province' => 'Hà Nội',
                'address' => '17 Duy Tân, Cầu Giấy, Hà Nội',
                'size' => '200+',
                'industry' => 'Software',
                'rating' => 4.7,
                'total_reviews' => 890,
                'is_verified' => true,
            ],
            [
                'company_slug' => 'vng-corporation',
                'company_name' => 'VNG Corporation',
                'description' => 'Công ty công nghệ và giải trí hàng đầu Việt Nam, chuyên về game và ứng dụng di động.',
                'website' => 'https://vng.com.vn',
                'city' => 'TP.HCM',
                'province' => 'TP.HCM',
                'address' => '123 Nguyễn Huệ, Quận 1, TP.HCM',
                'size' => '200+',
                'industry' => 'Gaming',
                'rating' => 4.3,
                'total_reviews' => 650,
                'is_verified' => true,
            ],
            [
                'company_slug' => 'tiki',
                'company_name' => 'Tiki',
                'description' => 'Sàn thương mại điện tử hàng đầu Việt Nam, chuyên về bán lẻ trực tuyến.',
                'website' => 'https://tiki.vn',
                'city' => 'TP.HCM',
                'province' => 'TP.HCM',
                'address' => '456 Lê Văn Việt, Quận 9, TP.HCM',
                'size' => '200+',
                'industry' => 'E-commerce',
                'rating' => 4.6,
                'total_reviews' => 1500,
                'is_verified' => true,
            ],
        ];

        foreach ($companies as $companyData) {
            Company::updateOrCreate(
                ['company_slug' => $companyData['company_slug']],
                array_merge($companyData, ['user_id' => $userId])
            );
        }
    }
}
