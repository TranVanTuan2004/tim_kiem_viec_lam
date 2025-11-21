<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use Illuminate\Database\Seeder;

class HomepageSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'section_key' => 'hero',
                'title' => 'Hero Section',
                'subtitle' => 'Banner chính trang chủ',
                'content' => [
                    'heading' => 'Tìm việc làm mơ ước của bạn',
                    'subheading' => 'Hàng ngàn cơ hội việc làm đang chờ đón bạn',
                ],
                'order' => 1,
                'is_active' => true,
            ],
            [
                'section_key' => 'featured_jobs',
                'title' => 'Việc làm nổi bật',
                'subtitle' => 'Các công việc được đề xuất',
                'content' => [
                    'limit' => 8,
                    'show_featured_only' => true,
                ],
                'order' => 2,
                'is_active' => true,
            ],
            [
                'section_key' => 'top_companies',
                'title' => 'Công ty hàng đầu',
                'subtitle' => 'Các nhà tuyển dụng uy tín',
                'content' => [
                    'limit' => 12,
                ],
                'order' => 3,
                'is_active' => true,
            ],
            [
                'section_key' => 'statistics',
                'title' => 'Thống kê',
                'subtitle' => 'Con số ấn tượng',
                'content' => [
                    'show_users' => true,
                    'show_jobs' => true,
                    'show_companies' => true,
                    'show_applications' => true,
                ],
                'order' => 4,
                'is_active' => true,
            ],
            [
                'section_key' => 'categories',
                'title' => 'Danh mục ngành nghề',
                'subtitle' => 'Khám phá theo lĩnh vực',
                'content' => [
                    'limit' => 8,
                ],
                'order' => 5,
                'is_active' => true,
            ],
            [
                'section_key' => 'testimonials',
                'title' => 'Đánh giá từ người dùng',
                'subtitle' => 'Câu chuyện thành công',
                'content' => [
                    'testimonials' => [],
                ],
                'order' => 6,
                'is_active' => false,
            ],
        ];

        foreach ($sections as $section) {
            HomepageSection::updateOrCreate(
                ['section_key' => $section['section_key']],
                $section
            );
        }
    }
}
