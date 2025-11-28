<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả admin users
        $admins = User::role('Admin')->get();

        if ($admins->isEmpty()) {
            $this->command->warn('Không tìm thấy admin nào. Vui lòng chạy UserSeeder trước.');
            return;
        }

        $notifications = [];

        // Tạo thông báo mẫu cho từng admin
        foreach ($admins as $admin) {
            $now = now();
            
            // Thông báo chưa đọc (admin_notification)
            $notifications[] = [
                'user_id' => $admin->id,
                'type' => 'admin_notification',
                'title' => 'Người dùng mới đăng ký',
                'message' => 'Có Ứng viên mới đăng ký: Nguyễn Văn A (nguyenvana@example.com)',
                'data' => json_encode([
                    'type' => 'new_user',
                    'user_id' => 1,
                    'user_name' => 'Nguyễn Văn A',
                    'user_email' => 'nguyenvana@example.com',
                    'role' => 'Candidate',
                    'created_at' => $now->subHours(1)->toDateTimeString(),
                ]),
                'is_read' => false,
                'created_at' => $now->subHours(1),
                'updated_at' => $now->subHours(1),
            ];

            $notifications[] = [
                'user_id' => $admin->id,
                'type' => 'admin_notification',
                'title' => 'Đơn tuyển dụng mới',
                'message' => 'Có đơn tuyển dụng mới: Senior PHP Developer từ TechCorp Vietnam',
                'data' => json_encode([
                    'type' => 'new_job_posting',
                    'job_posting_id' => 1,
                    'job_title' => 'Senior PHP Developer',
                    'company_id' => 1,
                    'company_name' => 'TechCorp Vietnam',
                    'status' => 'pending',
                    'location' => 'Hà Nội',
                    'created_at' => $now->subHours(2)->toDateTimeString(),
                ]),
                'is_read' => false,
                'created_at' => $now->subHours(2),
                'updated_at' => $now->subHours(2),
            ];

            $notifications[] = [
                'user_id' => $admin->id,
                'type' => 'admin_notification',
                'title' => 'Đơn tuyển dụng cần duyệt',
                'message' => 'Đơn tuyển dụng \'Frontend Developer\' từ FPT Software đang chờ duyệt',
                'data' => json_encode([
                    'type' => 'job_posting_pending',
                    'job_posting_id' => 2,
                    'job_title' => 'Frontend Developer',
                    'company_id' => 2,
                    'company_name' => 'FPT Software',
                    'status' => 'pending',
                    'updated_at' => $now->subHours(3)->toDateTimeString(),
                ]),
                'is_read' => false,
                'created_at' => $now->subHours(3),
                'updated_at' => $now->subHours(3),
            ];

            $notifications[] = [
                'user_id' => $admin->id,
                'type' => 'admin_notification',
                'title' => 'CV mới được tải lên',
                'message' => 'Ứng viên Trần Thị B đã tải lên CV mới',
                'data' => json_encode([
                    'type' => 'new_cv',
                    'candidate_profile_id' => 1,
                    'user_id' => 2,
                    'user_name' => 'Trần Thị B',
                    'user_email' => 'tranthib@example.com',
                    'cv_file' => 'cv/tranthib.pdf',
                    'created_at' => $now->subHours(4)->toDateTimeString(),
                ]),
                'is_read' => false,
                'created_at' => $now->subHours(4),
                'updated_at' => $now->subHours(4),
            ];

            // Thông báo hệ thống chưa đọc
            $notifications[] = [
                'user_id' => $admin->id,
                'type' => 'system_notification',
                'title' => 'Bảo trì hệ thống',
                'message' => 'Hệ thống sẽ được bảo trì vào ngày mai từ 2:00 - 4:00 sáng',
                'data' => json_encode([
                    'type' => 'maintenance',
                    'scheduled_at' => $now->addDay()->toDateTimeString(),
                ]),
                'is_read' => false,
                'created_at' => $now->subHours(5),
                'updated_at' => $now->subHours(5),
            ];

            // Thông báo đã đọc (để test)
            $notifications[] = [
                'user_id' => $admin->id,
                'type' => 'admin_notification',
                'title' => 'Người dùng mới đăng ký',
                'message' => 'Có Nhà tuyển dụng mới đăng ký: Công ty ABC (abc@example.com)',
                'data' => json_encode([
                    'type' => 'new_user',
                    'user_id' => 3,
                    'user_name' => 'Công ty ABC',
                    'user_email' => 'abc@example.com',
                    'role' => 'Employer',
                    'created_at' => $now->subDays(1)->toDateTimeString(),
                ]),
                'is_read' => true,
                'created_at' => $now->subDays(1),
                'updated_at' => $now->subDays(1)->addMinutes(30),
            ];

            $notifications[] = [
                'user_id' => $admin->id,
                'type' => 'admin_notification',
                'title' => 'CV được cập nhật',
                'message' => 'Ứng viên Lê Văn C đã cập nhật CV',
                'data' => json_encode([
                    'type' => 'updated_cv',
                    'candidate_profile_id' => 2,
                    'user_id' => 4,
                    'user_name' => 'Lê Văn C',
                    'user_email' => 'levanc@example.com',
                    'cv_file' => 'cv/levanc.pdf',
                    'updated_at' => $now->subDays(2)->toDateTimeString(),
                ]),
                'is_read' => true,
                'created_at' => $now->subDays(2),
                'updated_at' => $now->subDays(2)->addHours(1),
            ];

            $notifications[] = [
                'user_id' => $admin->id,
                'type' => 'system_notification',
                'title' => 'Cập nhật hệ thống',
                'message' => 'Phiên bản mới của hệ thống đã được triển khai thành công',
                'data' => json_encode([
                    'type' => 'system_update',
                    'version' => '2.0.0',
                ]),
                'is_read' => true,
                'created_at' => $now->subDays(3),
                'updated_at' => $now->subDays(3)->addHours(2),
            ];
        }

        // Insert tất cả thông báo
        if (!empty($notifications)) {
            // Chia nhỏ thành các batch để tránh lỗi
            $chunks = array_chunk($notifications, 100);
            foreach ($chunks as $chunk) {
                Notification::insert($chunk);
            }
        }

        $totalCount = count($notifications);
        $this->command->info("✓ Đã tạo {$totalCount} thông báo test cho " . $admins->count() . " admin(s)");
    }
}



