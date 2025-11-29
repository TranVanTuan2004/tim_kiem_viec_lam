<?php

namespace App\Observers;

use App\Models\JobPosting;
use App\Services\NotificationService;

class JobPostingObserver
{
    /**
     * Handle the JobPosting "created" event.
     */
    public function created(JobPosting $jobPosting): void
    {
        $company = $jobPosting->company;
        $companyName = $company ? $company->company_name : 'N/A';

        // Sử dụng NotificationService để gửi thông báo cho admin
        $notificationService = app(NotificationService::class);
        $notificationService->sendAdminNotification(
            'Đơn tuyển dụng mới',
            "Có đơn tuyển dụng mới: {$jobPosting->title} từ {$companyName}",
            [
                'type' => 'new_job_posting',
                'job_posting_id' => $jobPosting->id,
                'job_title' => $jobPosting->title,
                'company_id' => $jobPosting->company_id,
                'company_name' => $companyName,
                'status' => $jobPosting->status,
                'location' => $jobPosting->location,
                'created_at' => $jobPosting->created_at->toDateTimeString(),
            ]
        );
    }

    /**
     * Handle the JobPosting "updated" event.
     */
    public function updated(JobPosting $jobPosting): void
    {
        // Chỉ thông báo khi status thay đổi sang 'pending' (cần duyệt)
        if (!$jobPosting->isDirty('status') || $jobPosting->status !== 'pending') {
            return;
        }

        $company = $jobPosting->company;
        $companyName = $company ? $company->company_name : 'N/A';

        // Sử dụng NotificationService để gửi thông báo cho admin
        $notificationService = app(NotificationService::class);
        $notificationService->sendAdminNotification(
            'Đơn tuyển dụng cần duyệt',
            "Đơn tuyển dụng '{$jobPosting->title}' từ {$companyName} đang chờ duyệt",
            [
                'type' => 'job_posting_pending',
                'job_posting_id' => $jobPosting->id,
                'job_title' => $jobPosting->title,
                'company_id' => $jobPosting->company_id,
                'company_name' => $companyName,
                'status' => $jobPosting->status,
                'updated_at' => $jobPosting->updated_at->toDateTimeString(),
            ]
        );
    }
}

