<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $banners = [
            [
                'title' => 'Tìm Công Việc Mơ Ước Của Bạn',
                'description' => 'Hàng nghìn cơ hội việc làm từ các công ty hàng đầu đang chờ đợi bạn. Bắt đầu hành trình sự nghiệp của bạn ngay hôm nay!',
                'image_url' => '/storage/banners/hero-banner.jpg',
                'link_url' => '/jobs',
                'button_text' => 'Khám Phá Ngay',
                'order' => 1,
                'is_active' => true,
                'type' => 'hero',
                'start_date' => $now,
                'end_date' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Khuyến Mãi Đặc Biệt - Đăng Tin Miễn Phí',
                'description' => 'Nhà tuyển dụng đăng tin tuyển dụng MIỄN PHÍ trong 30 ngày đầu tiên. Tiếp cận hàng nghìn ứng viên chất lượng ngay hôm nay!',
                'image_url' => '/storage/banners/promotional-banner.jpg',
                'link_url' => '/employer/register',
                'button_text' => 'Đăng Ký Ngay',
                'order' => 2,
                'is_active' => true,
                'type' => 'promotional',
                'start_date' => $now,
                'end_date' => $now->copy()->addDays(30),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Nâng Cấp Tính Năng Mới - Tìm Việc Thông Minh Với AI',
                'description' => 'Hệ thống đã được nâng cấp với công nghệ AI giúp bạn tìm kiếm việc làm phù hợp hơn, nhanh hơn và chính xác hơn.',
                'image_url' => '/storage/banners/announcement-banner.jpg',
                'link_url' => '/features/ai-job-matching',
                'button_text' => 'Tìm Hiểu Thêm',
                'order' => 3,
                'is_active' => true,
                'type' => 'announcement',
                'start_date' => $now,
                'end_date' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('banners')->insert($banners);

        $this->command->info('✅ Đã tạo 3 banner mẫu thành công!');
        $this->command->info('📝 Lưu ý: Bạn cần copy các file ảnh vào thư mục storage/app/public/banners/');
        $this->command->info('   - hero-banner.jpg');
        $this->command->info('   - promotional-banner.jpg');
        $this->command->info('   - announcement-banner.jpg');
    }
}
