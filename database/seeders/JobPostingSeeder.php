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

        // Get Industries
        $industries = \App\Models\Industry::all()->pluck('id', 'slug');

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
                    'industry_id' => $industries['backend-developer'] ?? 1,
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
                    'industry_id' => $industries['frontend-developer'] ?? 1,
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
                    'industry_id' => $industries['full-stack-developer'] ?? 1,
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
                    'industry_id' => $industries['backend-developer'] ?? 1,
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
                    'industry_id' => $industries['devops-engineer'] ?? 1,
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
                    'industry_id' => $industries['mobile-developer'] ?? 1,
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
                    'industry_id' => $industries['mobile-developer'] ?? 1,
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
                    'industry_id' => $industries['data-engineer'] ?? 1,
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
                    'industry_id' => $industries['product-manager'] ?? 1,
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
                    'industry_id' => $industries['backend-developer'] ?? 1,
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
                    'industry_id' => $industries['frontend-developer'] ?? 1,
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
                    'industry_id' => $industries['qa-qc-engineer'] ?? 1,
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
                    'industry_id' => $industries['product-manager'] ?? 1,
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

        // Viettel Solutions Jobs
        $viettel = Company::where('company_slug', 'viettel-solutions')->first();
        if ($viettel) {
            JobPosting::updateOrCreate(
                ['slug' => 'cloud-architect-viettel'],
                [
                    'company_id' => $viettel->id,
                    'title' => 'Cloud Architect',
                    'description' => 'Thiết kế và triển khai các giải pháp Cloud Computing cho khách hàng doanh nghiệp lớn.',
                    'requirements' => '5+ years Cloud (AWS/Azure/GCP), Kubernetes, Microservices architecture',
                    'benefits' => 'Lương hấp dẫn, Thưởng dự án lớn, Cơ hội làm việc với công nghệ mới nhất',
                    'industry_id' => $industries['cloud-architect'] ?? 1,
                    'location' => 'Hà Nội',
                    'city' => 'Hà Nội',
                    'province' => 'Hà Nội',
                    'employment_type' => 'full_time',
                    'experience_level' => 'senior',
                    'min_salary' => 2500,
                    'max_salary' => 4500,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(45)->toDateString(),
                    'quantity' => 2,
                    'status' => 'approved',
                    'is_featured' => true,
                    'published_at' => now(),
                ]
            );

            JobPosting::updateOrCreate(
                ['slug' => 'security-engineer-viettel'],
                [
                    'company_id' => $viettel->id,
                    'title' => 'Security Engineer',
                    'description' => 'Đảm bảo an toàn thông tin cho hệ thống hạ tầng và ứng dụng của Viettel.',
                    'requirements' => 'CISSP/CEH, Network Security, Pentesting, Incident Response',
                    'benefits' => 'Phụ cấp đặc thù, Đào tạo chứng chỉ quốc tế, Môi trường chuyên nghiệp',
                    'industry_id' => $industries['security-engineer'] ?? 1,
                    'location' => 'Hà Nội',
                    'city' => 'Hà Nội',
                    'province' => 'Hà Nội',
                    'employment_type' => 'full_time',
                    'experience_level' => 'mid-senior',
                    'min_salary' => 1800,
                    'max_salary' => 3000,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(30)->toDateString(),
                    'quantity' => 3,
                    'status' => 'approved',
                    'is_featured' => false,
                    'published_at' => now(),
                ]
            );

            JobPosting::updateOrCreate(
                ['slug' => 'business-analyst-viettel'],
                [
                    'company_id' => $viettel->id,
                    'title' => 'Business Analyst',
                    'description' => 'Phân tích nghiệp vụ và tư vấn giải pháp chuyển đổi số cho khối chính phủ và doanh nghiệp.',
                    'requirements' => '3+ years BA experience, UML/BPMN, Good communication, English fluency',
                    'benefits' => 'Thưởng theo dự án, Chế độ phúc lợi tập đoàn, Cơ hội thăng tiến',
                    'industry_id' => $industries['business-analyst'] ?? 1,
                    'location' => 'Hà Nội',
                    'city' => 'Hà Nội',
                    'province' => 'Hà Nội',
                    'employment_type' => 'full_time',
                    'experience_level' => 'mid',
                    'min_salary' => 1200,
                    'max_salary' => 2000,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(20)->toDateString(),
                    'quantity' => 5,
                    'status' => 'approved',
                    'is_featured' => false,
                    'published_at' => now(),
                ]
            );

            JobPosting::updateOrCreate(
                ['slug' => 'system-admin-viettel'],
                [
                    'company_id' => $viettel->id,
                    'title' => 'System Administrator',
                    'description' => 'Quản trị và vận hành hệ thống máy chủ Linux/Windows quy mô lớn.',
                    'requirements' => 'Linux (RHEL/CentOS), Windows Server, Virtualization (VMware), Scripting',
                    'benefits' => 'Lương tháng 13, Thưởng lễ tết, Du lịch hàng năm',
                    'industry_id' => $industries['devops-engineer'] ?? 1,
                    'location' => 'Đà Nẵng',
                    'city' => 'Đà Nẵng',
                    'province' => 'Đà Nẵng',
                    'employment_type' => 'full_time',
                    'experience_level' => 'mid',
                    'min_salary' => 1000,
                    'max_salary' => 1800,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(25)->toDateString(),
                    'quantity' => 2,
                    'status' => 'approved',
                    'is_featured' => false,
                    'published_at' => now(),
                ]
            );
        }

        // NashTech Vietnam Jobs
        $nashtech = Company::where('company_slug', 'nashtech-vietnam')->first();
        if ($nashtech) {
            JobPosting::updateOrCreate(
                ['slug' => 'dotnet-developer-nashtech'],
                [
                    'company_id' => $nashtech->id,
                    'title' => '.NET Developer',
                    'description' => 'Develop enterprise applications using .NET Core and Azure services.',
                    'requirements' => '3+ years .NET Core, C#, Azure, SQL Server, English communication',
                    'benefits' => '13th month salary, Premium healthcare, English training',
                    'industry_id' => $industries['backend-developer'] ?? 1,
                    'location' => 'TP.HCM',
                    'city' => 'TP.HCM',
                    'province' => 'TP.HCM',
                    'employment_type' => 'full_time',
                    'experience_level' => 'mid',
                    'min_salary' => 1500,
                    'max_salary' => 2500,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(40)->toDateString(),
                    'quantity' => 10,
                    'status' => 'approved',
                    'is_featured' => true,
                    'published_at' => now(),
                ]
            );

            JobPosting::updateOrCreate(
                ['slug' => 'qa-manual-nashtech'],
                [
                    'company_id' => $nashtech->id,
                    'title' => 'Senior QA Manual',
                    'description' => 'Ensure software quality through rigorous manual testing and test planning.',
                    'requirements' => '5+ years testing experience, Test strategy, Defect management, Agile',
                    'benefits' => 'Performance bonus, Flexible working hours, Hybrid working',
                    'industry_id' => $industries['qa-qc-engineer'] ?? 1,
                    'location' => 'TP.HCM',
                    'city' => 'TP.HCM',
                    'province' => 'TP.HCM',
                    'employment_type' => 'full_time',
                    'experience_level' => 'senior',
                    'min_salary' => 1200,
                    'max_salary' => 2000,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(30)->toDateString(),
                    'quantity' => 5,
                    'status' => 'approved',
                    'is_featured' => false,
                    'published_at' => now(),
                ]
            );

            JobPosting::updateOrCreate(
                ['slug' => 'react-native-nashtech'],
                [
                    'company_id' => $nashtech->id,
                    'title' => 'React Native Developer',
                    'description' => 'Build cross-platform mobile apps for global clients.',
                    'requirements' => 'React Native, Redux, iOS/Android deployment, REST APIs',
                    'benefits' => 'MacBook Pro provided, Annual trip, Team building',
                    'industry_id' => $industries['mobile-developer'] ?? 1,
                    'location' => 'TP.HCM',
                    'city' => 'TP.HCM',
                    'province' => 'TP.HCM',
                    'employment_type' => 'full_time',
                    'experience_level' => 'mid',
                    'min_salary' => 1400,
                    'max_salary' => 2400,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(35)->toDateString(),
                    'quantity' => 3,
                    'status' => 'approved',
                    'is_featured' => true,
                    'published_at' => now(),
                ]
            );

            JobPosting::updateOrCreate(
                ['slug' => 'project-manager-nashtech'],
                [
                    'company_id' => $nashtech->id,
                    'title' => 'Project Manager',
                    'description' => 'Lead software development projects and manage client expectations.',
                    'requirements' => 'PMP/Scrum Master certified, 5+ years PM experience, Excellent English',
                    'benefits' => 'Competitive package, Leadership training, Global career opportunities',
                    'industry_id' => $industries['product-manager'] ?? 1,
                    'location' => 'Hà Nội',
                    'city' => 'Hà Nội',
                    'province' => 'Hà Nội',
                    'employment_type' => 'full_time',
                    'experience_level' => 'lead',
                    'min_salary' => 2500,
                    'max_salary' => 4500,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(50)->toDateString(),
                    'quantity' => 2,
                    'status' => 'approved',
                    'is_featured' => true,
                    'published_at' => now(),
                ]
            );
        }

        // More Jobs for existing companies to reach +20 total
        if ($techcorp) {
             JobPosting::updateOrCreate(
                ['slug' => 'ai-engineer-techcorp'],
                [
                    'company_id' => $techcorp->id,
                    'title' => 'AI Engineer',
                    'description' => 'Research and develop AI models for computer vision applications.',
                    'requirements' => 'Python, TensorFlow, OpenCV, Deep Learning, Master/PhD is a plus',
                    'benefits' => 'High salary, Research budget, Patent bonuses',
                    'industry_id' => $industries['data-engineer'] ?? 1,
                    'location' => 'Hà Nội',
                    'city' => 'Hà Nội',
                    'province' => 'Hà Nội',
                    'employment_type' => 'full_time',
                    'experience_level' => 'senior',
                    'min_salary' => 3000,
                    'max_salary' => 5000,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(60)->toDateString(),
                    'quantity' => 2,
                    'status' => 'approved',
                    'is_featured' => true,
                    'published_at' => now(),
                ]
            );
             JobPosting::updateOrCreate(
                ['slug' => 'intern-developer-techcorp'],
                [
                    'company_id' => $techcorp->id,
                    'title' => 'Fresher/Intern Java Developer',
                    'description' => 'Cơ hội học tập và làm việc trong môi trường chuyên nghiệp cho sinh viên mới ra trường.',
                    'requirements' => 'Basic Java knowledge, OOP, SQL, Willing to learn',
                    'benefits' => 'Trợ cấp thực tập, Cơ hội trở thành nhân viên chính thức, Mentor tận tâm',
                    'industry_id' => $industries['backend-developer'] ?? 1,
                    'location' => 'Hà Nội',
                    'city' => 'Hà Nội',
                    'province' => 'Hà Nội',
                    'employment_type' => 'internship',
                    'experience_level' => 'fresher',
                    'min_salary' => 200,
                    'max_salary' => 500,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(15)->toDateString(),
                    'quantity' => 10,
                    'status' => 'approved',
                    'is_featured' => false,
                    'published_at' => now(),
                ]
            );
        }

        if ($fpt) {
             JobPosting::updateOrCreate(
                ['slug' => 'embedded-engineer-fpt'],
                [
                    'company_id' => $fpt->id,
                    'title' => 'Embedded Engineer (C/C++)',
                    'description' => 'Phát triển phần mềm nhúng cho thiết bị Automotive.',
                    'requirements' => 'C/C++, Microcontrollers, RTOS, Automotive protocols (CAN/LIN)',
                    'benefits' => 'Onsite Japan/Korea opportunities, Project bonus, Language allowance',
                    'industry_id' => $industries['backend-developer'] ?? 1, // Mapping to backend as closest or need new category
                    'location' => 'Đà Nẵng',
                    'city' => 'Đà Nẵng',
                    'province' => 'Đà Nẵng',
                    'employment_type' => 'full_time',
                    'experience_level' => 'mid',
                    'min_salary' => 1200,
                    'max_salary' => 2200,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(30)->toDateString(),
                    'quantity' => 5,
                    'status' => 'approved',
                    'is_featured' => false,
                    'published_at' => now(),
                ]
            );
        }

        if ($tiki) {
             JobPosting::updateOrCreate(
                ['slug' => 'ui-ux-designer-tiki'],
                [
                    'company_id' => $tiki->id,
                    'title' => 'Senior UI/UX Designer',
                    'description' => 'Thiết kế trải nghiệm người dùng cho ứng dụng Tiki.',
                    'requirements' => 'Figma, Sketch, User Research, Prototyping, Portfolio required',
                    'benefits' => 'Môi trường sáng tạo, MacBook Pro, Thưởng cuối năm',
                    'industry_id' => $industries['frontend-developer'] ?? 1, // Closest match
                    'location' => 'TP.HCM',
                    'city' => 'TP.HCM',
                    'province' => 'TP.HCM',
                    'employment_type' => 'full_time',
                    'experience_level' => 'senior',
                    'min_salary' => 1500,
                    'max_salary' => 2800,
                    'salary_type' => 'monthly',
                    'application_deadline' => now()->addDays(25)->toDateString(),
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
