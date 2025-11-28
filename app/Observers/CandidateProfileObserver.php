<?php

namespace App\Observers;

use App\Models\CandidateProfile;
use App\Services\NotificationService;

class CandidateProfileObserver
{
    /**
     * Handle the CandidateProfile "created" event.
     */
    public function created(CandidateProfile $candidateProfile): void
    {
        // Chỉ thông báo khi CV được tạo lần đầu hoặc có file CV
        if (!$candidateProfile->cv_file) {
            return;
        }

        $user = $candidateProfile->user;

        // Sử dụng NotificationService để gửi thông báo cho admin
        $notificationService = app(NotificationService::class);
        $notificationService->sendAdminNotification(
            'CV mới được tải lên',
            "Ứng viên {$user->name} đã tải lên CV mới",
            [
                'type' => 'new_cv',
                'candidate_profile_id' => $candidateProfile->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'cv_file' => $candidateProfile->cv_file,
                'created_at' => $candidateProfile->created_at->toDateTimeString(),
            ]
        );
    }

    /**
     * Handle the CandidateProfile "updated" event.
     */
    public function updated(CandidateProfile $candidateProfile): void
    {
        // Chỉ thông báo khi CV file mới được upload (thay đổi từ null hoặc file khác)
        if (!$candidateProfile->isDirty('cv_file') || !$candidateProfile->cv_file) {
            return;
        }

        $user = $candidateProfile->user;

        // Sử dụng NotificationService để gửi thông báo cho admin
        $notificationService = app(NotificationService::class);
        $notificationService->sendAdminNotification(
            'CV được cập nhật',
            "Ứng viên {$user->name} đã cập nhật CV",
            [
                'type' => 'updated_cv',
                'candidate_profile_id' => $candidateProfile->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'cv_file' => $candidateProfile->cv_file,
                'updated_at' => $candidateProfile->updated_at->toDateTimeString(),
            ]
        );
    }
}

