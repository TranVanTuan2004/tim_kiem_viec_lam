<?php

namespace App\Observers;

use App\Models\User;
use App\Services\NotificationService;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Chỉ tạo thông báo cho admin khi user mới được tạo (không phải admin)
        if ($user->hasRole('Admin')) {
            return;
        }

        // Xác định role của user mới
        $roleName = $user->getRoleNames()->first() ?? 'User';
        $roleLabel = match($roleName) {
            'Candidate' => 'Ứng viên',
            'Employer' => 'Nhà tuyển dụng',
            default => 'Người dùng',
        };

        // Sử dụng NotificationService để gửi thông báo cho admin
        $notificationService = app(NotificationService::class);
        $notificationService->sendAdminNotification(
            'Người dùng mới đăng ký',
            "Có {$roleLabel} mới đăng ký: {$user->name} ({$user->email})",
            [
                'type' => 'new_user',
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'role' => $roleName,
                'created_at' => $user->created_at->toDateTimeString(),
            ]
        );
    }
}

