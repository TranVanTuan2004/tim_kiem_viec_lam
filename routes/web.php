<?php

use App\Http\Controllers\Client\JobPostingController;
use App\Http\Controllers\Client\CompanyController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Candidate\PortfolioController;
use App\Http\Controllers\Candidate\DashboardController;
use App\Http\Controllers\Employer\DashboardController as EmployerDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Candidate\ProfileController;
use App\Http\Controllers\Candidate\ApplicationController;
use App\Http\Controllers\Candidate\SavedJobController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\SupportChatController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Employer\PostingController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Employer\CompanyController as EmployerCompanyController;

// Client Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Jobs listing & detail
Route::get('/jobs', [JobPostingController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job_posting}', [JobPostingController::class, 'show'])->name('jobs.show');



// Job Application Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/jobs/{job_posting}/apply', [\App\Http\Controllers\Client\ApplicationController::class, 'create'])->name('jobs.apply');
    Route::post('/jobs/{job_posting}/apply', [\App\Http\Controllers\Client\ApplicationController::class, 'store'])->name('jobs.apply.store');
});

// Company Pages
Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
Route::get('/companies/{company}/jobs', [CompanyController::class, 'jobs'])->name('companies.jobs');

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

Route::get('dashboard', function () {
    $user = auth()->user();

    // Debug: Log user info
    Log::info('Dashboard access', [
        'user_id' => $user->id,
        'user_name' => $user->name,
        'roles' => $user->roles->pluck('name')->toArray()
    ]);

    // Redirect based on user role
    // if ($user->hasRole('Candidate')) {
    //     Log::info('Redirecting to candidate dashboard');
    //     return redirect()->route('candidate.dashboard');
    // } elseif ($user->hasRole('Employer')) {
    //     Log::info('Redirecting to employer dashboard');
    //     return redirect()->route('employer.dashboard');
    // } elseif ($user->hasRole('Admin')) {
    //     Log::info('Redirecting to admin dashboard');
    //     return redirect()->route('admin.dashboard');
    // }

    // Default fallback
    Log::info('No role found, using default dashboard');
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Employer Routes
Route::prefix('employer')->name('employer.')->middleware(['auth', 'role:Employer'])->group(function () {
    // Dashboard
    Route::get('dashboard', [EmployerDashboardController::class, 'index'])->name('dashboard');
    
});

// Admin Routes - Using Spatie Permission
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management - Only admin
    Route::resource('users', UserController::class)->middleware('permission:view users');
    
    // Chat routes - All authenticated users
    Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('chat/messages/{user}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('chat/send/{user}', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('chat/mark-as-read/{message}', [ChatController::class, 'markAsRead'])->name('chat.mark-read');
    Route::get('chat/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unread');
});

// Admin Routes - Subscription Management (for Employers)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'permission:view subscriptions'])->group(function () {
    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
    Route::post('subscriptions/subscribe', [SubscriptionController::class, 'subscribe'])->middleware('permission:manage subscriptions')->name('subscribe');
    Route::post('subscriptions/upgrade', [SubscriptionController::class, 'upgrade'])->middleware('permission:manage subscriptions')->name('upgrade');
    Route::post('subscriptions/renew', [SubscriptionController::class, 'renew'])->middleware('permission:manage subscriptions')->name('renew');
    Route::post('subscriptions/cancel', [SubscriptionController::class, 'cancel'])->middleware('permission:manage subscriptions')->name('cancel');
    Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::get('subscriptions/qr/data', [SubscriptionController::class, 'getPaymentData'])->name('subscriptions.payment');
    Route::get('subscriptions/zalopay-demo', [SubscriptionController::class, 'zaloPayDemo'])->name('subscriptions.zalopay-demo');
    Route::post('subscriptions/test-zalopay', [SubscriptionController::class, 'testZaloPay'])->name('subscriptions.test-zalopay');
    Route::post('subscriptions/simulate-payment', [SubscriptionController::class, 'simulatePayment'])->name('subscriptions.simulate-payment');
    Route::get('subscriptions/vnpay-demo', [SubscriptionController::class, 'vnpayDemo'])->name('subscriptions.vnpay-demo');
    Route::post('subscriptions/test-vnpay', [SubscriptionController::class, 'testVNPay'])->name('subscriptions.test-vnpay');
    Route::post('subscriptions/simulate-vnpay-payment', [SubscriptionController::class, 'simulateVNPayPayment'])->name('subscriptions.simulate-vnpay-payment');
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

// Test route để kiểm tra
Route::get('test-zalopay', function () {
    return 'ZaloPay test route works!';
});

Route::get('test-vnpay', function () {
    return 'VNPay test route works!';
});

// Support Chat Widget - Available for all authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('support/messages', [SupportChatController::class, 'messages'])->name('support.messages');
    Route::post('support/send', [SupportChatController::class, 'send'])->name('support.send');
    Route::post('support/messages/{message}/read', [SupportChatController::class, 'markAsRead'])->name('support.mark-read');
});

// Candidate Routes - All candidate features
Route::prefix('candidate')->name('candidate.')->middleware(['auth', 'role:Candidate'])->group(function () {
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
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
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

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/statistics', [ActivityLogController::class, 'statistics'])->name('activity-logs.statistics');
    Route::get('activity-logs/recent', [ActivityLogController::class, 'recent'])->name('activity-logs.recent');
    Route::get('activity-logs/top-users', [ActivityLogController::class, 'topUsers'])->name('activity-logs.top-users');
    Route::get('activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
    Route::post('activity-logs/clean', [ActivityLogController::class, 'clean'])->name('activity-logs.clean');
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
