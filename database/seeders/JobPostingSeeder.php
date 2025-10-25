<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\JobPosting;
use Illuminate\Database\Seeder;

class JobPostingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả các công ty
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->error('No companies found. Please run CompanySeeder first.');
            return;
        }

        // TechCorp Vietnam Jobs
        $techcorp = Company::where('company_slug', 'techcorp-vietnam')->first();
        if ($techcorp) {
            JobPosting::updateOrCreate(
                ['slug' => 'senior-backend-developer'],
                [
                    'company_id' => $techcorp->id,
                    'title' => 'Senior Backend Developer',
                    'description' => 'We are looking for a Senior Backend Developer to join our team.',
                    'requirements' => '5+ years experience, PHP, Laravel, MySQL',
                    'benefits' => 'Health insurance, Flexible hours',
                    'industry_id' => 1,
                    'location' => 'Hà Nội',
                    'city' => 'Hà Nội',
                    'province' => 'Hà Nội',
                    'employment_type' => 'full_time',
                    'experience_level' => 'senior',
                    'min_salary' => 1000,
                    'max_salary' => 2000,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(30)->toDateString(),
                    'quantity' => 1,
                    'status' => 'approved',
                    'is_featured' => true,
                    'published_at' => now(),
                ]
            );

            JobPosting::updateOrCreate(
                ['slug' => 'frontend-developer-reactjs'],
                [
                    'company_id' => $techcorp->id,
                    'title' => 'Frontend Developer (ReactJS)',
                    'description' => 'Seeking a skilled Frontend Developer to build beautiful user interfaces.',
                    'requirements' => '2+ years experience with ReactJS, Redux, HTML, CSS, JavaScript',
                    'benefits' => 'Competitive salary, modern workspace, free lunch',
                    'industry_id' => 1,
                    'location' => 'Hà Nội',
                    'city' => 'Hà Nội',
                    'province' => 'Hà Nội',
                    'employment_type' => 'full_time',
                    'experience_level' => 'mid',
                    'min_salary' => 800,
                    'max_salary' => 1500,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(25)->toDateString(),
                    'quantity' => 2,
                    'status' => 'approved',
                    'is_featured' => false,
                    'published_at' => now(),
                ]
            );

            JobPosting::updateOrCreate(
                ['slug' => 'full-stack-developer'],
                [
                    'company_id' => $techcorp->id,
                    'title' => 'Full-Stack Developer',
                    'description' => 'Join our dynamic team as a Full-Stack Developer.',
                    'requirements' => 'Experience with both Laravel and VueJS/ReactJS stacks.',
                    'benefits' => 'Stock options, performance bonuses, 13th-month salary',
                    'industry_id' => 1,
                    'location' => 'Hồ Chí Minh',
                    'city' => 'Thành phố Hồ Chí Minh',
                    'province' => 'Thành phố Hồ Chí Minh',
                    'employment_type' => 'full_time',
                    'experience_level' => 'mid-senior',
                    'min_salary' => 1200,
                    'max_salary' => 2200,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(45)->toDateString(),
                    'quantity' => 1,
                    'status' => 'approved',
                    'is_featured' => true,
                    'published_at' => now(),
                ]
            );
        }

        // FPT Software Jobs
        $fpt = Company::where('company_slug', 'fpt-software')->first();
        if ($fpt) {
            JobPosting::updateOrCreate(
                ['slug' => 'java-software-engineer-fpt'],
                [
                    'company_id' => $fpt->id,
                    'title' => 'Java Software Engineer',
                    'description' => 'FPT Software đang tìm kiếm kỹ sư phần mềm Java có kinh nghiệm để tham gia các dự án quốc tế.',
                    'requirements' => '3+ years Java, Spring Boot, Microservices, Good English communication',
                    'benefits' => 'Lương tháng 13, Bảo hiểm sức khỏe cao cấp, Thưởng dự án',
                    'industry_id' => 1,
                    'location' => 'Hà Nội',
                    'city' => 'Hà Nội',
                    'province' => 'Hà Nội',
                    'employment_type' => 'full_time',
                    'experience_level' => 'mid',
                    'min_salary' => 1500,
                    'max_salary' => 2500,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(40)->toDateString(),
                    'quantity' => 3,
                    'status' => 'approved',
                    'is_featured' => true,
                    'published_at' => now(),
                ]
            );

            JobPosting::updateOrCreate(
                ['slug' => 'devops-engineer-fpt'],
                [
                    'company_id' => $fpt->id,
                    'title' => 'DevOps Engineer',
                    'description' => 'Tham gia team DevOps để xây dựng và duy trì hạ tầng CI/CD cho các dự án lớn.',
                    'requirements' => 'Docker, Kubernetes, AWS/Azure, Jenkins, Terraform',
                    'benefits' => 'Lương cao, Đào tạo chứng chỉ AWS/Azure, Du lịch hàng năm',
                    'industry_id' => 1,
                    'location' => 'Hà Nội',
                    'city' => 'Hà Nội',
                    'province' => 'Hà Nội',
                    'employment_type' => 'full_time',
                    'experience_level' => 'senior',
                    'min_salary' => 2000,
                    'max_salary' => 3500,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(35)->toDateString(),
                    'quantity' => 2,
                    'status' => 'approved',
                    'is_featured' => true,
                    'published_at' => now(),
                ]
            );

            JobPosting::updateOrCreate(
                ['slug' => 'mobile-developer-flutter-fpt'],
                [
                    'company_id' => $fpt->id,
                    'title' => 'Mobile Developer (Flutter)',
                    'description' => 'Phát triển ứng dụng di động đa nền tảng bằng Flutter cho các khách hàng Nhật Bản.',
                    'requirements' => '2+ years Flutter/Dart, RESTful API, Firebase, Japanese N3 is a plus',
                    'benefits' => 'Làm việc với khách hàng Nhật, Học tiếng Nhật miễn phí, Cơ hội onsite',
                    'industry_id' => 1,
                    'location' => 'Hà Nội',
                    'city' => 'Hà Nội',
                    'province' => 'Hà Nội',
                    'employment_type' => 'full_time',
                    'experience_level' => 'mid',
                    'min_salary' => 1200,
                    'max_salary' => 2000,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(30)->toDateString(),
                    'quantity' => 4,
                    'status' => 'approved',
                    'is_featured' => false,
                    'published_at' => now(),
                ]
            );
        }

        // VNG Corporation Jobs
        $vng = Company::where('company_slug', 'vng-corporation')->first();
        if ($vng) {
            JobPosting::updateOrCreate(
                ['slug' => 'game-developer-unity-vng'],
                [
                    'company_id' => $vng->id,
                    'title' => 'Game Developer (Unity)',
                    'description' => 'Tham gia phát triển các game mobile hấp dẫn với hàng triệu người chơi.',
                    'requirements' => '2+ years Unity3D, C#, Game optimization, Passion for gaming',
                    'benefits' => 'Lương cạnh tranh, Chơi game miễn phí, Team building định kỳ',
                    'industry_id' => 1,
                    'location' => 'TP.HCM',
                    'city' => 'TP.HCM',
                    'province' => 'TP.HCM',
                    'employment_type' => 'full_time',
                    'experience_level' => 'mid',
                    'min_salary' => 1500,
                    'max_salary' => 2800,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(50)->toDateString(),
                    'quantity' => 3,
                    'status' => 'approved',
                    'is_featured' => true,
                    'published_at' => now(),
                ]
            );

            JobPosting::updateOrCreate(
                ['slug' => 'data-scientist-vng'],
                [
                    'company_id' => $vng->id,
                    'title' => 'Data Scientist',
                    'description' => 'Phân tích dữ liệu người dùng để tối ưu hóa trải nghiệm và doanh thu game.',
                    'requirements' => 'Python, SQL, Machine Learning, TensorFlow/PyTorch, Statistics',
                    'benefits' => 'Lương cao, Làm việc với big data, Môi trường sáng tạo',
                    'industry_id' => 1,
                    'location' => 'TP.HCM',
                    'city' => 'TP.HCM',
                    'province' => 'TP.HCM',
                    'employment_type' => 'full_time',
                    'experience_level' => 'senior',
                    'min_salary' => 2500,
                    'max_salary' => 4000,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(45)->toDateString(),
                    'quantity' => 2,
                    'status' => 'approved',
                    'is_featured' => true,
                    'published_at' => now(),
                ]
            );

            JobPosting::updateOrCreate(
                ['slug' => 'product-manager-vng'],
                [
                    'company_id' => $vng->id,
                    'title' => 'Product Manager - Gaming',
                    'description' => 'Quản lý sản phẩm game từ ý tưởng đến ra mắt và vận hành.',
                    'requirements' => '3+ years Product Management, Gaming industry experience, Data-driven mindset',
                    'benefits' => 'Lương tháng 13, Stock options, Bonus theo hiệu suất sản phẩm',
                    'industry_id' => 1,
                    'location' => 'TP.HCM',
                    'city' => 'TP.HCM',
                    'province' => 'TP.HCM',
                    'employment_type' => 'full_time',
                    'experience_level' => 'senior',
                    'min_salary' => 2000,
                    'max_salary' => 3500,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(40)->toDateString(),
                    'quantity' => 1,
                    'status' => 'approved',
                    'is_featured' => false,
                    'published_at' => now(),
                ]
            );
        }

        // Tiki Jobs
        $tiki = Company::where('company_slug', 'tiki')->first();
        if ($tiki) {
            JobPosting::updateOrCreate(
                ['slug' => 'backend-engineer-golang-tiki'],
                [
                    'company_id' => $tiki->id,
                    'title' => 'Backend Engineer (Golang)',
                    'description' => 'Xây dựng và tối ưu hệ thống backend xử lý hàng triệu giao dịch mỗi ngày.',
                    'requirements' => '3+ years Golang, Microservices, Redis, PostgreSQL, RabbitMQ',
                    'benefits' => 'Lương cao, Mua hàng Tiki giảm giá, Bảo hiểm toàn diện',
                    'industry_id' => 1,
                    'location' => 'TP.HCM',
                    'city' => 'TP.HCM',
                    'province' => 'TP.HCM',
                    'employment_type' => 'full_time',
                    'experience_level' => 'mid-senior',
                    'min_salary' => 2000,
                    'max_salary' => 3500,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(35)->toDateString(),
                    'quantity' => 5,
                    'status' => 'approved',
                    'is_featured' => true,
                    'published_at' => now(),
                ]
            );

            JobPosting::updateOrCreate(
                ['slug' => 'frontend-engineer-vuejs-tiki'],
                [
                    'company_id' => $tiki->id,
                    'title' => 'Frontend Engineer (VueJS)',
                    'description' => 'Phát triển giao diện website và app Tiki với hàng triệu người dùng.',
                    'requirements' => '3+ years VueJS/Nuxt.js, TypeScript, Responsive design, Performance optimization',
                    'benefits' => 'Thưởng dự án, Flexible working hours, Gym membership',
                    'industry_id' => 1,
                    'location' => 'TP.HCM',
                    'city' => 'TP.HCM',
                    'province' => 'TP.HCM',
                    'employment_type' => 'full_time',
                    'experience_level' => 'mid',
                    'min_salary' => 1500,
                    'max_salary' => 2500,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(30)->toDateString(),
                    'quantity' => 4,
                    'status' => 'approved',
                    'is_featured' => true,
                    'published_at' => now(),
                ]
            );

            JobPosting::updateOrCreate(
                ['slug' => 'qa-automation-engineer-tiki'],
                [
                    'company_id' => $tiki->id,
                    'title' => 'QA Automation Engineer',
                    'description' => 'Xây dựng hệ thống test automation để đảm bảo chất lượng sản phẩm.',
                    'requirements' => 'Selenium, Appium, CI/CD, API testing, Performance testing',
                    'benefits' => 'Lương tháng 13, Training budget, Cơ hội thăng tiến',
                    'industry_id' => 1,
                    'location' => 'TP.HCM',
                    'city' => 'TP.HCM',
                    'province' => 'TP.HCM',
                    'employment_type' => 'full_time',
                    'experience_level' => 'mid',
                    'min_salary' => 1000,
                    'max_salary' => 1800,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(25)->toDateString(),
                    'quantity' => 3,
                    'status' => 'approved',
                    'is_featured' => false,
                    'published_at' => now(),
                ]
            );

            JobPosting::updateOrCreate(
                ['slug' => 'scrum-master-tiki'],
                [
                    'company_id' => $tiki->id,
                    'title' => 'Scrum Master',
                    'description' => 'Hỗ trợ các team áp dụng Agile/Scrum hiệu quả trong phát triển sản phẩm.',
                    'requirements' => 'Certified Scrum Master, 3+ years experience, Strong facilitation skills',
                    'benefits' => 'Lương cạnh tranh, Làm việc với nhiều team, Đào tạo Agile coach',
                    'industry_id' => 1,
                    'location' => 'TP.HCM',
                    'city' => 'TP.HCM',
                    'province' => 'TP.HCM',
                    'employment_type' => 'full_time',
                    'experience_level' => 'senior',
                    'min_salary' => 1800,
                    'max_salary' => 3000,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(40)->toDateString(),
                    'quantity' => 2,
                    'status' => 'approved',
                    'is_featured' => true,
                    'published_at' => now(),
                ]
            );
        }

        $totalJobs = JobPosting::count();
        $featuredJobs = JobPosting::where('is_featured', true)->count();
        $this->command->info("✓ Job postings seeded successfully!");
        $this->command->info("  Total jobs: {$totalJobs}");
        $this->command->info("  Featured jobs: {$featuredJobs}");
    }
}
