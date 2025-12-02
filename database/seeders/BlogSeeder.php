<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = User::where('email', 'admin@example.com')->first() ?? User::first();
        if (! $author) {
            return;
        }

        $blogs = [
            [
                'title' => 'Kinh nghiệm phỏng vấn vị trí Developer',
                'excerpt' => 'Các mẹo thực tế giúp bạn tự tin trong buổi phỏng vấn.',
                'content' => 'Tổng hợp câu hỏi thường gặp, cách trình bày kinh nghiệm và mẹo xử lý tình huống trong phỏng vấn vị trí Developer. Chuẩn bị portfolio, nắm rõ dự án đã làm và luyện tập thuật toán cơ bản.',
                'featured_image' => 'https://images.unsplash.com/photo-1517433456452-f9633a875f6f?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Hướng dẫn tối ưu CV IT',
                'excerpt' => 'Cấu trúc CV rõ ràng, nổi bật kỹ năng và dự án.',
                'content' => 'Cách viết CV IT hiệu quả: tóm tắt mục tiêu nghề nghiệp, liệt kê kỹ năng chính, dự án tiêu biểu, đóng góp mã nguồn và chứng chỉ liên quan. Tối ưu từ khóa ATS.',
                'featured_image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Tổng hợp kỹ năng cần có cho Backend',
                'excerpt' => 'Database, API, caching, bảo mật và quan sát.',
                'content' => 'Những kỹ năng cốt lõi cho Backend: thiết kế REST/GraphQL, tối ưu truy vấn, dùng cache (Redis), bảo mật JWT/OAuth, logging, tracing và scaling microservices.',
                'featured_image' => 'https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Lộ trình học Frontend hiện đại',
                'excerpt' => 'HTML/CSS, JS, framework, state, performance và testing.',
                'content' => 'Lộ trình từ cơ bản tới nâng cao: nắm chắc HTML/CSS/JS, chọn framework (Vue/React), quản lý state, tối ưu hiệu năng, accessibility và viết test E2E.',
                'featured_image' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'DevOps là gì? Bắt đầu từ đâu',
                'excerpt' => 'Tự động hóa CI/CD, container và observability.',
                'content' => 'Giới thiệu DevOps: pipeline CI/CD, Docker/Kubernetes, infrastructure as code (Terraform), giám sát với Prometheus/Grafana, và chiến lược triển khai an toàn.',
                'featured_image' => 'https://images.unsplash.com/photo-1510915228340-29c85a43dcfe?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Bí quyết xin việc tại công ty lớn',
                'excerpt' => 'Networking, portfolio mạnh và chuẩn bị phỏng vấn nhiều vòng.',
                'content' => 'Cách tiếp cận công ty lớn: xây dựng thương hiệu cá nhân, đóng góp OSS, portfolio chuyên nghiệp, luyện assesment và phỏng vấn hệ thống/bảo mật.',
                'featured_image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Clean Code trong dự án thực tế',
                'excerpt' => 'Đặt tên rõ ràng, tách trách nhiệm và viết test.',
                'content' => 'Nguyên tắc Clean Code: SOLID, DRY, small functions, dependency injection, refactor thường xuyên và đảm bảo test suite đáng tin cậy.',
                'featured_image' => 'https://images.unsplash.com/photo-1487017159836-4e23ece2e4cf?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Bảo mật ứng dụng web: các bước cơ bản',
                'excerpt' => 'XSS, CSRF, SQLi, auth và log/audit.',
                'content' => 'Checklist bảo mật: validate input, sanitize output, chống CSRF, quản lý session an toàn, kiểm soát quyền truy cập và giám sát hoạt động bất thường.',
                'featured_image' => 'https://images.unsplash.com/photo-1556157382-5d16c1ea9e84?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Phỏng vấn thuật toán: mẹo và chiến lược',
                'excerpt' => 'Luyện tập pattern, phân tích độ phức tạp và trình bày rõ ràng.',
                'content' => 'Các mẹo giải thuật: two pointers, sliding window, divide and conquer, greedy, DP. Trình bày giải pháp, test edge cases và cải tiến tối ưu.',
                'featured_image' => 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Học Docker và Kubernetes cho người mới',
                'excerpt' => 'Đóng gói, triển khai và mở rộng ứng dụng dễ dàng.',
                'content' => 'Khái niệm container, tạo Dockerfile chuẩn, compose services, Kubernetes cơ bản (pod, deployment, service) và chiến lược scaling/rolling update.',
                'featured_image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        foreach ($blogs as $item) {
            $slug = Str::slug($item['title']);
            Blog::updateOrCreate(
                ['slug' => $slug],
                [
                    'author_id' => $author->id,
                    'title' => $item['title'],
                    'excerpt' => $item['excerpt'],
                    'content' => $item['content'],
                    'featured_image' => $item['featured_image'],
                    'status' => 'published',
                    'published_at' => now()->subDays(rand(0, 30))->setTime(rand(8, 20), rand(0, 59)),
                    'views' => rand(50, 500),
                    'is_featured' => (bool) rand(0, 1),
                    'meta_title' => $item['title'],
                    'meta_description' => 'Chia sẻ kiến thức IT, kinh nghiệm phỏng vấn và lộ trình học.',
                ]
            );
        }
    }
}
