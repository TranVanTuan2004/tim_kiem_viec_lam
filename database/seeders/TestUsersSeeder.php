<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CandidateProfile;
use App\Models\Company;
use Illuminate\Database\Seeder;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🔄 Creating test users...\n";

        // ========================================
        // CANDIDATE ACCOUNTS
        // ========================================

        // Candidate 1: Nguyễn Văn An
        $candidate1 = User::firstOrCreate(
            ['email' => 'nguyenvanan@example.com'],
            [
                'name' => 'Nguyễn Văn An',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone' => '0901234567',
                'is_active' => true,
            ]
        );
        if (!$candidate1->hasRole('Candidate')) {
            $candidate1->assignRole('Candidate');
        }

        // Create profile for Candidate 1
        CandidateProfile::updateOrCreate(
            ['user_id' => $candidate1->id],
            [
                'birth_date' => '1995-05-15',
                'gender' => 'male',
                'address' => '123 Nguyễn Huệ',
                'city' => 'Hồ Chí Minh',
                'province' => 'Hồ Chí Minh',
                'summary' => 'Lập trình viên Full-stack với 3 năm kinh nghiệm phát triển web. Thành thạo Laravel, Vue.js và React.',
                'current_position' => 'Senior Developer',
                'current_company' => 'Tech Solutions Vietnam',
                'expected_salary' => 25000000,
                'experience_level' => 'senior',
                'is_available' => true,
                'job_alert_enabled' => true,
                'preferred_locations' => ['Hồ Chí Minh', 'Hà Nội'],
            ]
        );

        echo "✓ Candidate 1 created: nguyenvanan@example.com / password\n";

        // Candidate 2: Trần Thị Bình
        $candidate2 = User::firstOrCreate(
            ['email' => 'tranthibinh@example.com'],
            [
                'name' => 'Trần Thị Bình',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone' => '0907654321',
                'is_active' => true,
            ]
        );
        if (!$candidate2->hasRole('Candidate')) {
            $candidate2->assignRole('Candidate');
        }

        // Create profile for Candidate 2
        CandidateProfile::updateOrCreate(
            ['user_id' => $candidate2->id],
            [
                'birth_date' => '1998-08-20',
                'gender' => 'female',
                'address' => '456 Lê Lợi',
                'city' => 'Hà Nội',
                'province' => 'Hà Nội',
                'summary' => 'UI/UX Designer với đam mê sáng tạo. Có kinh nghiệm thiết kế giao diện cho các ứng dụng mobile và web.',
                'current_position' => 'UI/UX Designer',
                'current_company' => 'Creative Agency',
                'expected_salary' => 18000000,
                'experience_level' => 'junior',
                'is_available' => true,
                'job_alert_enabled' => true,
                'preferred_locations' => ['Hà Nội', 'Đà Nẵng'],
            ]
        );

        echo "✓ Candidate 2 created: tranthibinh@example.com / password\n";

        // ========================================
        // EMPLOYER ACCOUNTS (using existing companies)
        // ========================================

        // Find existing companies
        $fptSoftware = Company::where('company_slug', 'fpt-software')->first();
        $vngCorp = Company::where('company_slug', 'vng-corporation')->first();

        if (!$fptSoftware || !$vngCorp) {
            echo "⚠️  Warning: Some companies not found. Please run CompanySeeder first.\n";
            echo "   Run: php artisan db:seed --class=CompanySeeder\n";
            return;
        }

        // Employer 1: HR Manager at FPT Software
        $employer1 = User::firstOrCreate(
            ['email' => 'hr.fpt@example.com'],
            [
                'name' => 'Nguyễn Minh Tuấn',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone' => '0281234567',
                'is_active' => true,
            ]
        );
        if (!$employer1->hasRole('Employer')) {
            $employer1->assignRole('Employer');
        }

        // Update company to link with this employer
        $fptSoftware->update(['user_id' => $employer1->id]);

        echo "✓ Employer 1 created: hr.fpt@example.com / password (FPT Software)\n";

        // Employer 2: Recruiter at VNG Corporation
        $employer2 = User::firstOrCreate(
            ['email' => 'recruit.vng@example.com'],
            [
                'name' => 'Lê Thị Hương',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone' => '0289876543',
                'is_active' => true,
            ]
        );
        if (!$employer2->hasRole('Employer')) {
            $employer2->assignRole('Employer');
        }

        // Update company to link with this employer
        $vngCorp->update(['user_id' => $employer2->id]);

        echo "✓ Employer 2 created: recruit.vng@example.com / password (VNG Corporation)\n";

        echo "\n✅ Test users seeding completed!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📋 SUMMARY:\n";
        echo "  Candidates:\n";
        echo "    - nguyenvanan@example.com (Nguyễn Văn An - Senior Developer)\n";
        echo "    - tranthibinh@example.com (Trần Thị Bình - UI/UX Designer)\n";
        echo "  Employers:\n";
        echo "    - hr.fpt@example.com (FPT Software)\n";
        echo "    - recruit.vng@example.com (VNG Corporation)\n";
        echo "  Default password: password\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    }
}
