<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Gửi thông báo hệ thống cho nhiều users
     *
     * @param Collection|array $users
     * @param string $title
     * @param string $message
     * @param array|null $data
     * @return int Số lượng thông báo đã gửi
     */
    public function sendSystemNotification(
        Collection|array $users,
        string $title,
        string $message,
        ?array $data = null
    ): int {
        if (empty($users)) {
            return 0;
        }

        // Chuyển đổi array thành Collection nếu cần
        if (is_array($users)) {
            $users = collect($users);
        }

        $notifications = [];
        $now = now();

        foreach ($users as $user) {
            $userId = $user instanceof User ? $user->id : $user;
            
            $notifications[] = [
                'user_id' => $userId,
                'type' => 'system_notification',
                'title' => $title,
                'message' => $message,
                'data' => $data ? json_encode($data) : null,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert hàng loạt để tối ưu hiệu suất
        if (!empty($notifications)) {
            // Chia nhỏ thành các batch để tránh lỗi khi có quá nhiều records
            $chunks = array_chunk($notifications, 500);
            
            foreach ($chunks as $chunk) {
                DB::table('notifications')->insert($chunk);
            }
        }

        return count($notifications);
    }

    /**
     * Gửi thông báo hệ thống cho tất cả users
     *
     * @param string $title
     * @param string $message
     * @param array|null $data
     * @return int
     */
    public function sendToAllUsers(string $title, string $message, ?array $data = null): int
    {
        $users = User::all();
        return $this->sendSystemNotification($users, $title, $message, $data);
    }

    /**
     * Gửi thông báo hệ thống cho users theo role
     *
     * @param string|array $roles
     * @param string $title
     * @param string $message
     * @param array|null $data
     * @return int
     */
    public function sendToRole(string|array $roles, string $title, string $message, ?array $data = null): int
    {
        $roles = is_array($roles) ? $roles : [$roles];
        $users = User::role($roles)->get();
        return $this->sendSystemNotification($users, $title, $message, $data);
    }

    /**
     * Gửi thông báo hệ thống cho tất cả admin
     *
     * @param string $title
     * @param string $message
     * @param array|null $data
     * @return int Số lượng thông báo đã gửi
     */
    public function sendToAdmin(string $title, string $message, ?array $data = null): int
    {
        $admins = User::role('Admin')->get();
        return $this->sendSystemNotification($admins, $title, $message, $data);
    }

    /**
     * Gửi thông báo quản trị cho tất cả admin (type: admin_notification)
     *
     * @param string $title
     * @param string $message
     * @param array|null $data
     * @return int Số lượng thông báo đã gửi
     */
    public function sendAdminNotification(string $title, string $message, ?array $data = null): int
    {
        $admins = User::role('Admin')->get();
        
        if ($admins->isEmpty()) {
            return 0;
        }

        $notifications = [];
        $now = now();

        foreach ($admins as $admin) {
            $notifications[] = [
                'user_id' => $admin->id,
                'type' => 'admin_notification',
                'title' => $title,
                'message' => $message,
                'data' => $data ? json_encode($data) : null,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert hàng loạt để tối ưu hiệu suất
        if (!empty($notifications)) {
            $chunks = array_chunk($notifications, 500);
            
            foreach ($chunks as $chunk) {
                DB::table('notifications')->insert($chunk);
            }
        }

        return count($notifications);
    }

    /**
     * Gửi thông báo hệ thống cho các users cụ thể
     *
     * @param array $userIds
     * @param string $title
     * @param string $message
     * @param array|null $data
     * @return int
     */
    public function sendToUsers(array $userIds, string $title, string $message, ?array $data = null): int
    {
        $users = User::whereIn('id', $userIds)->get();
        return $this->sendSystemNotification($users, $title, $message, $data);
    }

    /**
     * Gửi thông báo hệ thống cho một user
     *
     * @param int|User $user
     * @param string $title
     * @param string $message
     * @param array|null $data
     * @return Notification
     */
    public function sendToUser(int|User $user, string $title, string $message, ?array $data = null): Notification
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::create([
            'user_id' => $userId,
            'type' => 'system_notification',
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    /**
     * Đánh dấu thông báo là đã đọc
     *
     * @param int $notificationId
     * @param int|null $userId
     * @return bool
     */
    public function markAsRead(int $notificationId, ?int $userId = null): bool
    {
        $query = Notification::where('id', $notificationId);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $notification = $query->first();
        
        if (!$notification) {
            return false;
        }

        $notification->markAsRead();
        return true;
    }

    /**
     * Đánh dấu tất cả thông báo của user là đã đọc
     *
     * @param int $userId
     * @param string|null $type
     * @return int Số lượng thông báo đã đánh dấu
     */
    public function markAllAsRead(int $userId, ?string $type = null): int
    {
        $query = Notification::where('user_id', $userId)
            ->where('is_read', false);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->update(['is_read' => true]);
    }

    /**
     * Xóa thông báo
     *
     * @param int $notificationId
     * @param int|null $userId
     * @return bool
     */
    public function delete(int $notificationId, ?int $userId = null): bool
    {
        $query = Notification::where('id', $notificationId);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $notification = $query->first();
        
        if (!$notification) {
            return false;
        }

        return $notification->delete();
    }

    /**
     * Lấy số lượng thông báo chưa đọc của user
     *
     * @param int $userId
     * @param string|null $type
     * @return int
     */
    public function getUnreadCount(int $userId, ?string $type = null): int
    {
        $query = Notification::where('user_id', $userId)
            ->where('is_read', false);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->count();
    }
}

