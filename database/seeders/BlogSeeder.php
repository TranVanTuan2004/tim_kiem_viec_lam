<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have an admin user to be the author
        $author = User::role('Admin')->first();
        
        if (!$author) {
            $author = User::first();
        }

        if (!$author) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        $blogs = [
            [
                'title' => 'Top 10 Ngôn Ngữ Lập Trình Nên Học Năm 2025',
                'category' => 'Tech News',
                'tags' => ['programming', 'career', 'technology'],
                'featured_image' => 'https://via.placeholder.com/800x400/4F46E5/FFFFFF?text=Programming+Languages+2025',
                'excerpt' => 'Khám phá những ngôn ngữ lập trình đang dẫn đầu xu hướng và mang lại cơ hội việc làm tốt nhất trong năm 2025.',
                'content' => '
                    <p>Năm 2025 đánh dấu sự bùng nổ của AI và Machine Learning, kéo theo sự thay đổi trong nhu cầu tuyển dụng lập trình viên. Dưới đây là danh sách các ngôn ngữ lập trình bạn nên cân nhắc học tập:</p>
                    <h3>1. Python</h3>
                    <p>Không ngạc nhiên khi Python vẫn đứng đầu bảng xếp hạng. Với sự phát triển của AI, Data Science và Automation, Python trở thành ngôn ngữ không thể thiếu.</p>
                    <h3>2. JavaScript / TypeScript</h3>
                    <p>Web development vẫn là mảng lớn nhất. TypeScript đang dần thay thế JavaScript thuần trong các dự án lớn nhờ khả năng quản lý type an toàn.</p>
                    <h3>3. Rust</h3>
                    <p>Rust đang được ưa chuộng nhờ hiệu năng cao và an toàn bộ nhớ. Nhiều ông lớn công nghệ đang chuyển dần các module quan trọng sang Rust.</p>
                    <h3>4. Go (Golang)</h3>
                    <p>Với sự phổ biến của Microservices và Cloud Native, Go là lựa chọn hàng đầu cho backend development.</p>
                    <p>Hãy chọn ngôn ngữ phù hợp với định hướng nghề nghiệp của bạn và bắt đầu học ngay hôm nay!</p>
                ',
            ],
            [
                'title' => 'Bí Quyết Viết CV IT "Chuẩn Chỉnh" Chinh Phục Nhà Tuyển Dụng',
                'category' => 'Career Advice',
                'tags' => ['cv', 'interview', 'job-search'],
                'featured_image' => 'https://via.placeholder.com/800x400/10B981/FFFFFF?text=CV+Writing+Tips',
                'excerpt' => 'CV là vũ khí đầu tiên giúp bạn gây ấn tượng. Tìm hiểu cách tối ưu hóa CV để vượt qua vòng lọc hồ sơ một cách dễ dàng.',
                'content' => '
                    <p>Một chiếc CV ấn tượng không chỉ liệt kê kỹ năng mà còn phải kể được câu chuyện về năng lực của bạn. Dưới đây là các tips quan trọng:</p>
                    <h3>1. Tập trung vào kết quả (Impact)</h3>
                    <p>Thay vì viết "Đã làm việc với ReactJS", hãy viết "Xây dựng module X bằng ReactJS giúp tăng trải nghiệm người dùng và giảm 30% thời gian tải trang".</p>
                    <h3>2. Sử dụng từ khóa (Keywords)</h3>
                    <p>Hệ thống ATS (Applicant Tracking System) sẽ quét CV của bạn. Hãy đảm bảo CV chứa các từ khóa có trong mô tả công việc (JD).</p>
                    <h3>3. Project thực tế</h3>
                    <p>Đối với Fresher/Junior, project cá nhân là minh chứng rõ nhất cho năng lực. Hãy để link GitHub và demo sản phẩm.</p>
                    <p>Đừng quên kiểm tra lỗi chính tả và format CV thật chuyên nghiệp nhé!</p>
                ',
            ],
            [
                'title' => 'Lộ Trình Trở Thành DevOps Engineer Từ Con Số 0',
                'category' => 'DevOps',
                'tags' => ['devops', 'cloud', 'career-path'],
                'featured_image' => 'https://via.placeholder.com/800x400/F59E0B/FFFFFF?text=DevOps+Career+Path',
                'excerpt' => 'DevOps đang là vị trí được săn đón với mức lương hấp dẫn. Bài viết này sẽ vạch ra lộ trình học tập chi tiết cho bạn.',
                'content' => '
                    <p>DevOps là cầu nối giữa Development và Operations. Để trở thành DevOps Engineer, bạn cần trang bị kiến thức đa dạng:</p>
                    <h3>Giai đoạn 1: Nền tảng</h3>
                    <ul>
                        <li>Linux & Scripting (Bash, Python)</li>
                        <li>Networking cơ bản (DNS, HTTP, SSL)</li>
                        <li>Git & Version Control</li>
                    </ul>
                    <h3>Giai đoạn 2: CI/CD & Containerization</h3>
                    <ul>
                        <li>Docker & Kubernetes</li>
                        <li>Jenkins, GitLab CI, GitHub Actions</li>
                    </ul>
                    <h3>Giai đoạn 3: Cloud & IaC</h3>
                    <ul>
                        <li>AWS / Azure / GCP</li>
                        <li>Terraform, Ansible</li>
                    </ul>
                    <p>Hành trình này không dễ dàng nhưng phần thưởng là vô cùng xứng đáng!</p>
                ',
            ],
            [
                'title' => 'Phỏng Vấn System Design: Những Điều Cần Biết',
                'category' => 'Interview Tips',
                'tags' => ['system-design', 'interview', 'architecture'],
                'featured_image' => 'https://via.placeholder.com/800x400/EF4444/FFFFFF?text=System+Design+Interview',
                'excerpt' => 'System Design là vòng phỏng vấn khó nhằn đối với các vị trí Senior. Cùng tìm hiểu cách tiếp cận và giải quyết bài toán thiết kế hệ thống.',
                'content' => '
                    <p>Phỏng vấn System Design không có đáp án đúng sai tuyệt đối, mà đánh giá tư duy giải quyết vấn đề của bạn.</p>
                    <h3>Quy trình 4 bước:</h3>
                    <ol>
                        <li><strong>Làm rõ yêu cầu (Clarify Requirements):</strong> Đặt câu hỏi để hiểu rõ phạm vi (scope), lượng người dùng, tính năng chính.</li>
                        <li><strong>Thiết kế tổng quan (High-level Design):</strong> Vẽ sơ đồ các component chính (Load Balancer, Web Server, Database, Cache).</li>
                        <li><strong>Đi sâu vào chi tiết (Deep Dive):</strong> Chọn một vài component quan trọng để thiết kế chi tiết (Database Schema, API Design).</li>
                        <li><strong>Xử lý các vấn đề mở rộng (Scalability & Bottlenecks):</strong> Bàn về Sharding, Replication, Caching strategy, Failure handling.</li>
                    </ol>
                    <p>Luyện tập thường xuyên với các bài toán như: Thiết kế Twitter, TinyURL, Chat System...</p>
                ',
            ],
            [
                'title' => 'Làm Việc Remote Hiệu Quả: Kinh Nghiệm Cho Developer',
                'category' => 'Work Life',
                'tags' => ['remote-work', 'productivity', 'soft-skills'],
                'featured_image' => 'https://via.placeholder.com/800x400/8B5CF6/FFFFFF?text=Remote+Work+Tips',
                'excerpt' => 'Làm việc từ xa mang lại sự tự do nhưng cũng đầy thách thức. Làm sao để duy trì năng suất và cân bằng cuộc sống?',
                'content' => '
                    <p>Remote work đã trở thành xu hướng bình thường mới. Tuy nhiên, nhiều dev gặp khó khăn trong việc quản lý thời gian và giao tiếp.</p>
                    <h3>1. Thiết lập không gian làm việc riêng</h3>
                    <p>Đừng làm việc trên giường. Hãy có một góc làm việc nghiêm túc để não bộ nhận biết "đây là giờ làm việc".</p>
                    <h3>2. Giao tiếp chủ động (Over-communication)</h3>
                    <p>Khi không gặp mặt trực tiếp, hãy cập nhật tiến độ thường xuyên, rõ ràng. Đừng để đồng nghiệp phải đoán bạn đang làm gì.</p>
                    <h3>3. Kỷ luật thời gian</h3>
                    <p>Sử dụng Pomodoro hoặc Time Blocking để tập trung. Đặt giờ bắt đầu và kết thúc rõ ràng để tránh burnout.</p>
                ',
            ],
            [
                'title' => 'Xu Hướng Frontend 2025: Server Components & AI',
                'category' => 'Tech News',
                'tags' => ['frontend', 'react', 'ai'],
                'featured_image' => 'https://via.placeholder.com/800x400/3B82F6/FFFFFF?text=Frontend+Trends+2025',
                'excerpt' => 'Frontend development đang thay đổi chóng mặt. Cùng điểm qua những xu hướng công nghệ sẽ thống trị trong năm tới.',
                'content' => '
                    <p>Thế giới Frontend không bao giờ đứng yên. Năm 2025 dự kiến sẽ là năm của sự tối ưu hóa và tích hợp AI.</p>
                    <h3>React Server Components (RSC)</h3>
                    <p>RSC đang thay đổi cách chúng ta build ứng dụng React, giúp giảm bundle size phía client và cải thiện performance đáng kể.</p>
                    <h3>AI-Driven Development</h3>
                    <p>Các công cụ như GitHub Copilot, V0.dev đang giúp frontend dev code nhanh hơn. Kỹ năng prompt engineering sẽ trở nên quan trọng.</p>
                    <h3>Micro-frontends</h3>
                    <p>Với các ứng dụng quy mô lớn, Micro-frontends giúp chia nhỏ team và deploy độc lập, tăng tốc độ phát triển.</p>
                ',
            ],
        ];

        foreach ($blogs as $blogData) {
            Blog::create([
                'title' => $blogData['title'],
                'slug' => Str::slug($blogData['title']),
                'excerpt' => $blogData['excerpt'],
                'content' => $blogData['content'],
                'author_id' => $author->id,
                'category' => $blogData['category'],
                'tags' => $blogData['tags'],
                'featured_image' => $blogData['featured_image'],
                'is_published' => true,
                'is_featured' => rand(0, 1) == 1,
                'views_count' => rand(100, 5000),
                'published_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
