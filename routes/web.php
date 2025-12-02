<?php

use App\Http\Controllers\Client\JobPostingController;
use App\Http\Controllers\Client\CompanyController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Candidate\PortfolioController;
use App\Http\Controllers\Candidate\DashboardController;
use App\Http\Controllers\Candidate\EducationController;
use App\Http\Controllers\Candidate\WorkExperienceController;
use App\Http\Controllers\Employer\DashboardController as EmployerDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Candidate\ProfileController;
use App\Http\Controllers\Candidate\ApplicationController;
use App\Http\Controllers\Candidate\SavedJobController;
use App\Http\Controllers\Candidate\FavoriteController;
use App\Http\Controllers\Candidate\ReportController as CandidateReportController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\SupportChatController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Employer\PostingController;
use App\Http\Controllers\Employer\EmployerCandidateReportController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Employer\CompanyController as EmployerCompanyController;
use App\Http\Controllers\Employer\ApplicationController as EmployerApplicationController;
use App\Http\Controllers\Employer\CandidateSearchController;
use App\Http\Controllers\Employer\InterviewController;
use App\Http\Controllers\Candidate\InterviewController as CandidateInterviewController;
use App\Http\Controllers\Admin\ServicePackageController;
use App\Http\Controllers\Admin\SubscriptionControllerV2;
use App\Http\Controllers\Client\BlogController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;

// Client Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Jobs listing & detail
Route::get('/jobs', [JobPostingController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job_posting}', [JobPostingController::class, 'show'])->name('jobs.show');

// Blog listing & detail
Route::get('/blog', [\App\Http\Controllers\Client\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\Client\BlogController::class, 'show'])->name('blog.show');



// Job Application Routes (require authentication)
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/jobs/{job_posting}/apply', [\App\Http\Controllers\Client\ApplicationController::class, 'create'])->name('jobs.apply');
    Route::post('/jobs/{job_posting}/apply', [\App\Http\Controllers\Client\ApplicationController::class, 'store'])->name('jobs.apply.store');
});

// Company Pages
Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
Route::get('/companies/{company:company_slug}', [CompanyController::class, 'show'])->name('companies.show');
Route::get('/industries', [App\Http\Controllers\Client\IndustryController::class, 'index'])->name('industries.index');
Route::get('/companies/{company}/jobs', [CompanyController::class, 'jobs'])->name('companies.jobs');

// Company Reviews (require authentication)
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/companies/{company}/reviews', [\App\Http\Controllers\Client\CompanyReviewController::class, 'store'])->name('companies.reviews.store');
    Route::put('/companies/{company}/reviews/{review}', [\App\Http\Controllers\Client\CompanyReviewController::class, 'update'])->name('companies.reviews.update');
    Route::delete('/companies/{company}/reviews/{review}', [\App\Http\Controllers\Client\CompanyReviewController::class, 'destroy'])->name('companies.reviews.destroy');
    Route::get('/companies/{company}/reviews/user', [\App\Http\Controllers\Client\CompanyReviewController::class, 'getUserReview'])->name('companies.reviews.user');
});

// Static Pages
Route::get('/about', function () {
    return Inertia::render('client/About');
})->name('about');

Route::get('/contact', function () {
    return Inertia::render('client/Contact');
})->name('contact');

Route::get('/terms', function () {
    return Inertia::render('client/Terms');
})->name('terms');

Route::get('/privacy', function () {
    return Inertia::render('client/Privacy');
})->name('privacy');

// Blog Pages
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    // Debug: Log user info
    Log::info('Dashboard access', [
        'user_id' => $user->id,
        'user_name' => $user->name,
        'roles' => $user->roles->pluck('name')->toArray()
    ]);

    // Redirect based on user role - check roles first
    if ($user->hasRole('Candidate')) {
        Log::info('Redirecting to candidate dashboard');
        return redirect()->route('candidate.dashboard');
    }

    if ($user->hasRole('Employer')) {
        Log::info('Redirecting to employer dashboard');
        return redirect()->route('employer.dashboard');
    }

    if ($user->hasRole('Admin')) {
        Log::info('Redirecting to admin dashboard');
        return redirect()->route('admin.dashboard');
    }

    // Default fallback
    Log::warning('No role found for authenticated user, using default dashboard');
    return Inertia::render('Dashboard');
})->middleware(['auth', 'active'])->name('dashboard');


// Employer Routes
Route::prefix('employer')->name('employer.')->middleware(['auth', 'active', 'role:Employer'])->group(function () {
    // Dashboard
    Route::get('dashboard', [EmployerDashboardController::class, 'index'])->name('dashboard');

    // Subscriptions - Sử dụng controller và page của admin
    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
    Route::post('subscriptions/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
    Route::post('subscriptions/vnpay_payment', [SubscriptionController::class, 'vnpayPayment'])->name('vnpay.payment');
    Route::post('subscriptions/upgrade', [SubscriptionController::class, 'upgrade'])->name('upgrade');
    Route::post('subscriptions/renew', [SubscriptionController::class, 'renew'])->name('renew');
    Route::post('subscriptions/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
    Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
});
// Admin Routes - Using Spatie Permission
Route::prefix('admin')->name('admin.')->middleware(['auth', 'active', 'role:Admin'])->group(function () {
    // Dashboard
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management - Only admin
    Route::resource('users', UserController::class)
        ->middleware('permission:view users');

    // Chat routes - All authenticated users
    Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('chat/messages/{user}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('chat/send/{user}', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('chat/mark-as-read/{message}', [ChatController::class, 'markAsRead'])->name('chat.mark-read');
    Route::get('chat/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unread');

    Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])
        ->name('users.toggle-active');

    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.reset-password');

    Route::post('users/{user}/send-reset-link', [UserController::class, 'sendResetLink'])
        ->name('users.send-reset-link');

    Route::get('users/{user}/activity-logs', [UserController::class, 'activityLogs'])
        ->name('users.activity-logs');

    // Banner Management
    Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class);
    Route::post('banners/{banner}/toggle-active', [\App\Http\Controllers\Admin\BannerController::class, 'toggleActive'])
        ->name('banners.toggle-active');
    Route::post('banners/update-order', [\App\Http\Controllers\Admin\BannerController::class, 'updateOrder'])
        ->name('banners.update-order');

    // Homepage Section Management
    Route::get('homepage', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'index'])
        ->name('homepage.index');
    Route::patch('homepage/{section}', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'update'])
        ->name('homepage.update');
    Route::post('homepage/{section}/toggle-active', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'toggleActive'])
        ->name('homepage.toggle-active');
    Route::post('homepage/update-order', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'updateOrder'])
        ->name('homepage.update-order');

    // Blog Management
    Route::resource('blogs', AdminBlogController::class);
    Route::post('blogs/{blog}/toggle-featured', [AdminBlogController::class, 'toggleFeatured'])
        ->name('blogs.toggle-featured');

    // Interview Management
    Route::get('interviews', [\App\Http\Controllers\Admin\InterviewController::class, 'index'])
        ->name('interviews.index');
    Route::get('interviews/{id}', [\App\Http\Controllers\Admin\InterviewController::class, 'show'])
        ->name('interviews.show');
    Route::delete('interviews/{id}', [\App\Http\Controllers\Admin\InterviewController::class, 'destroy'])
        ->name('interviews.destroy');
    Route::post('companies/{companyId}/toggle-interview-block', [\App\Http\Controllers\Admin\InterviewController::class, 'toggleBlock'])
        ->name('companies.toggle-interview-block');

    // System Notifications Management
    Route::get('notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
    Route::post('notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('notifications.store');
    Route::get('notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('notifications.show');
    Route::post('notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('notifications/destroy-multiple', [\App\Http\Controllers\Admin\NotificationController::class, 'destroyMultiple'])->name('notifications.destroy-multiple');
    Route::get('notifications/stats', [\App\Http\Controllers\Admin\NotificationController::class, 'stats'])->name('notifications.stats');
});

// Admin Routes - Subscription Management (for Employers)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'active'])->group(function () {
    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
    Route::post('subscriptions/subscribe', [SubscriptionController::class, 'subscribe'])->middleware('permission:manage subscriptions')->name('subscribe');
    Route::post('subscriptions/vnpay_payment', [SubscriptionController::class, 'vnpayPayment'])->name('vnpay.payment');
    Route::post('subscriptions/upgrade', [SubscriptionController::class, 'upgrade'])->middleware('permission:manage subscriptions')->name('upgrade');
    Route::post('subscriptions/renew', [SubscriptionController::class, 'renew'])->middleware('permission:manage subscriptions')->name('renew');
    Route::post('subscriptions/cancel', [SubscriptionController::class, 'cancel'])->middleware('permission:manage subscriptions')->name('cancel');
    Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::post('subscriptions/simulate-payment', [SubscriptionController::class, 'simulatePayment'])->name('subscriptions.simulate-payment');
    Route::get('subscriptions/vnpay-demo', [SubscriptionController::class, 'vnpayDemo'])->name('subscriptions.vnpay-demo');
    Route::post('subscriptions/test-vnpay', [SubscriptionController::class, 'testVNPay'])->name('subscriptions.test-vnpay');
    Route::post('subscriptions/simulate-vnpay-payment', [SubscriptionController::class, 'simulateVNPayPayment'])->name('subscriptions.simulate-vnpay-payment');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
});


// SERVICE PACKAGE ROUTE
// ----------------------

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('service-packages', [ServicePackageController::class, 'index'])->name('service-packages.index');
    Route::get('service-packages/create', [ServicePackageController::class, 'create'])->name('service-packages.create');
    Route::post('service-packages', [ServicePackageController::class, 'store'])->name('service-packages.store');
    Route::get('service-packages/{package:slug}', [ServicePackageController::class, 'show'])->name('service-packages.show');
    Route::get('service-packages/{package:slug}/edit', [ServicePackageController::class, 'edit'])->name('service-packages.edit');
    Route::put('service-packages/{package:slug}', [ServicePackageController::class, 'update'])->name('service-packages.update');
    Route::delete('service-packages/{package:slug}', [ServicePackageController::class, 'destroy'])->name('service-packages.destroy');
    Route::post('service-packages/{package:slug}/toggle', [ServicePackageController::class, 'toggle'])->name('service-packages.toggle');
});



// Payment Callbacks (không cần auth)
Route::prefix('admin/subscriptions/payment')->group(function () {
    Route::post('callback', [SubscriptionController::class, 'zalopayCallback'])->name('zalopay.callback');
    Route::get('return', [SubscriptionController::class, 'zalopayReturn'])->name('zalopay.return');
});

// VNPay Payment Callbacks (không cần auth)
Route::prefix('admin/subscriptions/vnpay')->group(function () {
    Route::post('callback', [SubscriptionController::class, 'vnpayCallback'])->name('vnpay.callback');
    Route::get('return', [SubscriptionController::class, 'vnpayReturn'])->name('vnpay.return');
});



// Support Chat Widget - Available for all authenticated users
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('support/messages', [SupportChatController::class, 'messages'])->name('support.messages');
    Route::post('support/send', [SupportChatController::class, 'send'])->name('support.send');
    Route::post('support/messages/{message}/read', [SupportChatController::class, 'markAsRead'])->name('support.mark-read');
});

// Candidate Routes - All candidate features
Route::prefix('candidate')->name('candidate.')->middleware(['auth', 'active', 'role:Candidate'])->group(function () {
    // Redirect /candidate to /candidate/dashboard
    Route::get('/', function () {
        return redirect()->route('candidate.dashboard');
    });

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('profile/create', [ProfileController::class, 'create'])->name('profile.create');
    Route::post('profile', [ProfileController::class, 'store'])->name('profile.store');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/toggle-availability', [ProfileController::class, 'toggleAvailability'])->name('profile.toggle-availability');

    // Applications Management
    Route::get('applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::post('applications/{application}/withdraw', [ApplicationController::class, 'withdraw'])->name('applications.withdraw');

    // Saved Jobs Management
    Route::get('saved-jobs', [SavedJobController::class, 'index'])->name('saved-jobs.index');
    Route::post('saved-jobs/{job}/toggle', [SavedJobController::class, 'toggle'])->name('saved-jobs.toggle');
    Route::delete('saved-jobs/{job}', [SavedJobController::class, 'destroy'])->name('saved-jobs.destroy');

    // Portfolio Management
    Route::resource('portfolios', PortfolioController::class);
    Route::post('portfolios/reorder', [PortfolioController::class, 'reorder'])->name('portfolios.reorder');
    Route::post('portfolios/{portfolio}/toggle-featured', [PortfolioController::class, 'toggleFeatured'])->name('portfolios.toggle-featured');
    Route::post('portfolios/{portfolio}/toggle-public', [PortfolioController::class, 'togglePublic'])->name('portfolios.toggle-public');

    // CV Management
    Route::resource('cvs', \App\Http\Controllers\Candidate\CvController::class)->only(['index', 'store', 'destroy']);
    Route::post('cvs/{id}/default', [\App\Http\Controllers\Candidate\CvController::class, 'setDefault'])->name('cvs.default');


    // Favorite
    Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('favorites/toggle/{job}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::delete('favorites/clear', [FavoriteController::class, 'clear'])->name('favorites.clear');

    // Education Management
    Route::resource('educations', EducationController::class);

    // Work Experience Management
    Route::resource('work-experiences', WorkExperienceController::class);

    // Interview Management
    Route::get('interviews', [CandidateInterviewController::class, 'index'])->name('interviews.index');
    Route::get('interviews/{id}', [CandidateInterviewController::class, 'show'])->name('interviews.show');
    Route::post('interviews/{id}/confirm', [CandidateInterviewController::class, 'confirm'])->name('interviews.confirm');
    Route::post('interviews/{id}/decline', [CandidateInterviewController::class, 'decline'])->name('interviews.decline');
    Route::post('interviews/{id}/propose-reschedule', [CandidateInterviewController::class, 'proposeReschedule'])->name('interviews.propose-reschedule');

    // Notifications Management
    Route::get('notifications', [\App\Http\Controllers\Candidate\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [\App\Http\Controllers\Candidate\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [\App\Http\Controllers\Candidate\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('notifications/unread-count', [\App\Http\Controllers\Candidate\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::delete('notifications/{id}', [\App\Http\Controllers\Candidate\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Reports Management
    Route::get('reports', [CandidateReportController::class, 'index'])->name('reports.index');
    Route::post('reports', [CandidateReportController::class, 'store'])->name('reports.store');
    Route::get('reports/{report}', [CandidateReportController::class, 'show'])->name('reports.show');
});

Route::post('candidate/favorites/toggle/{job}', [FavoriteController::class, 'toggle'])->name('candidate.favorites.toggle');

// Admin Reports
Route::prefix('admin')->name('admin.')->middleware(['auth', 'active', 'role:Admin'])->group(function () {
    Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])
        ->name('reports.index')
        ->middleware('permission:view reports');

    Route::get('reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'show'])
        ->name('reports.show')
        ->middleware('permission:view reports');

    Route::patch('reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'update'])
        ->name('reports.update')
        ->middleware('permission:edit reports');

    Route::delete('reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'destroy'])
        ->name('reports.destroy')
        ->middleware('permission:delete reports');
});

// Employer Routes
Route::prefix('employer')->name('employer.')->middleware(['auth', 'role:Employer'])->group(function () {
    Route::get('dashboard', [EmployerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/applications', [EmployerApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{id}', [EmployerApplicationController::class, 'show'])->name('applications.show');
    Route::patch('/applications/{id}/status', [EmployerApplicationController::class, 'updateStatus'])->name('applications.update-status');
    Route::get('candidates/search', [CandidateSearchController::class, 'index'])->name('employer.candidates.search');

    // Interview Management
    Route::resource('interviews', InterviewController::class)->except(['edit']);
    Route::post('interviews/{id}/complete', [InterviewController::class, 'complete'])->name('interviews.complete');
    Route::post('interviews/{id}/reschedule', [InterviewController::class, 'reschedule'])->name('interviews.reschedule');
    Route::post('interviews/{id}/reschedule/accept', [InterviewController::class, 'acceptReschedule'])->name('interviews.reschedule.accept');
    Route::post('interviews/{id}/reschedule/decline', [InterviewController::class, 'declineReschedule'])->name('interviews.reschedule.decline');

    // Employer Candidate Report
    Route::post('reports', [\App\Http\Controllers\Employer\EmployerReportController::class, 'store']);
    Route::get('reports', [\App\Http\Controllers\Employer\EmployerReportController::class, 'index'])
        ->name('reports.index');

    Route::get('reports/{report}', [\App\Http\Controllers\Employer\EmployerReportController::class, 'show'])
        ->name('reports.show');
});
Route::prefix('employer')->name('employer.')->group(function () {
    // Danh sách tin tuyển dụng
    Route::get('posting', [PostingController::class, 'index'])->name('postings.index');
    // Tạo tin mới
    Route::get('posting/create', [PostingController::class, 'create'])->name('postings.create');
    Route::post('posting', [PostingController::class, 'store'])->name('postings.store');
    // Xem & chỉnh sửa tin
    Route::get('posting/{id}', [PostingController::class, 'show'])->name('postings.show');
    Route::get('posting/{id}/edit', [PostingController::class, 'edit'])->name('postings.edit');
    Route::put('posting/{id}', [PostingController::class, 'update'])->name('postings.update');
    //Xóa
    Route::delete('posting/{id}', [PostingController::class, 'destroy'])->name('postings.destroy');
    //Ẩn , hiện tin tuyển dụng
    Route::patch('posting/{id}/toggle', [PostingController::class, 'toggle'])->name('postings.toggle');
    // Cài đặt công ty
    Route::get('/settings/company', [EmployerCompanyController::class, 'edit'])->name('company.edit');
    Route::patch('/settings/company', [EmployerCompanyController::class, 'update'])->name('company.update');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'active', 'verified'])->group(function () {
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/statistics', [ActivityLogController::class, 'statistics'])->name('activity-logs.statistics');
    Route::get('activity-logs/recent', [ActivityLogController::class, 'recent'])->name('activity-logs.recent');
    Route::get('activity-logs/top-users', [ActivityLogController::class, 'topUsers'])->name('activity-logs.top-users');
    Route::get('activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
    Route::post('activity-logs/clean', [ActivityLogController::class, 'clean'])->name('activity-logs.clean');

    // Application Management (Admin)
    Route::get('/applications', [\App\Http\Controllers\Admin\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [\App\Http\Controllers\Admin\ApplicationController::class, 'show'])->name('applications.show');
    Route::delete('/applications/{application}', [\App\Http\Controllers\Admin\ApplicationController::class, 'destroy'])->name('applications.destroy');
    Route::patch('/applications/{application}/status', [\App\Http\Controllers\Admin\ApplicationController::class, 'updateStatus'])->name('applications.update-status');

    // Company Reviews Management
    Route::get('company-reviews', [\App\Http\Controllers\Admin\CompanyReviewController::class, 'index'])->name('company-reviews.index');
    Route::post('company-reviews/{review}/approve', [\App\Http\Controllers\Admin\CompanyReviewController::class, 'approve'])->name('company-reviews.approve');
    Route::post('company-reviews/{review}/reject', [\App\Http\Controllers\Admin\CompanyReviewController::class, 'reject'])->name('company-reviews.reject');
    Route::delete('company-reviews/{review}', [\App\Http\Controllers\Admin\CompanyReviewController::class, 'destroy'])->name('company-reviews.destroy');
    Route::post('company-reviews/bulk-approve', [\App\Http\Controllers\Admin\CompanyReviewController::class, 'bulkApprove'])->name('company-reviews.bulk-approve');
    Route::post('company-reviews/bulk-reject', [\App\Http\Controllers\Admin\CompanyReviewController::class, 'bulkReject'])->name('company-reviews.bulk-reject');

    // Job Posting Management
    Route::get('job-postings', [\App\Http\Controllers\Admin\JobPostingController::class, 'index'])->name('job-postings.index');
    Route::post('job-postings/{jobPosting:id}/approve', [\App\Http\Controllers\Admin\JobPostingController::class, 'approve'])->name('job-postings.approve');
    Route::post('job-postings/{jobPosting:id}/reject', [\App\Http\Controllers\Admin\JobPostingController::class, 'reject'])->name('job-postings.reject');
    Route::delete('job-postings/{jobPosting:id}', [\App\Http\Controllers\Admin\JobPostingController::class, 'destroy'])->name('job-postings.destroy');

    // Reports Management
    Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'show'])->name('reports.show');
    Route::patch('reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'update'])->name('reports.update');
    Route::delete('reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'destroy'])->name('reports.destroy');
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
