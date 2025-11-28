<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use App\Models\User;
use App\Models\CandidateProfile;
use App\Models\JobPosting;
use App\Observers\UserObserver;
use App\Observers\CandidateProfileObserver;
use App\Observers\JobPostingObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 👇 Cấu hình thời gian hết hạn link xác thực (24 giờ)
        VerifyEmail::createUrlUsing(function ($notifiable) {
            return URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(config('auth.verification.expire', 1440)), // 1440 phút = 24h
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });
        Vite::prefetch(concurrency: 3);

        // Đăng ký Observers
        User::observe(UserObserver::class);
        CandidateProfile::observe(CandidateProfileObserver::class);
        JobPosting::observe(JobPostingObserver::class);
    }
}
