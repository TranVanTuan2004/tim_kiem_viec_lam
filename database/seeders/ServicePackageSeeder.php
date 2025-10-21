<?php

namespace Database\Seeders;

use App\Models\ServicePackage;
use Illuminate\Database\Seeder;

class ServicePackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Gói Miễn Phí',
                'slug' => 'free',
                'description' => 'Gói miễn phí cho các công ty mới bắt đầu, đăng được 10 tin tuyển dụng.',
                'price' => 0,
                'duration_days' => 365, // 1 năm
                'max_jobs' => 10, // Đăng tối đa 10 bài
                'features' => [
                    'Đăng tối đa 10 tin tuyển dụng',
                    'Xem hồ sơ ứng viên cơ bản',
                    'Hỗ trợ qua email',
                    'Truy cập cơ sở dữ liệu ứng viên',
                    'Báo cáo tuyển dụng đơn giản',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Gói Trả Phí',
                'slug' => 'premium',
                'description' => 'Gói trả phí với đầy đủ tính năng, đăng tin không giới hạn.',
                'price' => 2000000, // 2M VND
                'duration_days' => 30,
                'max_jobs' => 0, // Không giới hạn
                'features' => [
                    'Đăng tin tuyển dụng không giới hạn',
                    'Xem không giới hạn hồ sơ ứng viên',
                    'Hỗ trợ ưu tiên qua email và điện thoại',
                    'Truy cập đầy đủ cơ sở dữ liệu ứng viên',
                    'Báo cáo tuyển dụng chi tiết',
                    'Tích hợp ATS (Applicant Tracking System)',
                    'Tự động sàng lọc ứng viên',
                    'Thông báo email tự động',
                    'Ưu tiên hiển thị tin tuyển dụng',
                    'Tính năng nâng cao khác',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($packages as $packageData) {
            ServicePackage::firstOrCreate(
                ['slug' => $packageData['slug']],
                $packageData
            );
        }
    }
}