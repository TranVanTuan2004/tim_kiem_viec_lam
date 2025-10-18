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
        $company = Company::where('company_slug', 'techcorp-vietnam')->first();

        if (! $company) {
            $this->command->error('Company "techcorp-vietnam" not found. Skipping JobPostingSeeder.');
            return;
        }

        // --- JOB POSTING #1: SENIOR BACKEND DEVELOPER ---
        JobPosting::updateOrCreate(
            ['slug' => 'senior-backend-developer'],
            [
                'company_id' => $company->id,
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

        // --- JOB POSTING #2: FRONTEND DEVELOPER (REACTJS) ---
        JobPosting::updateOrCreate(
            ['slug' => 'frontend-developer-reactjs'],
            [
                'company_id' => $company->id,
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

        // --- JOB POSTING #3: FULL-STACK DEVELOPER ---
        JobPosting::updateOrCreate(
            ['slug' => 'full-stack-developer'],
            [
                'company_id' => $company->id,
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

        $this->command->info('Job postings seeded successfully!');
    }
}
