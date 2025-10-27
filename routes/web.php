<?php

use App\Http\Controllers\Client\JobPostingController;
use App\Http\Controllers\Client\CompanyController;
use App\Http\Controllers\Client\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\SupportChatController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\DashboardController;

// Client Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Job Pages
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

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes - Using Spatie Permission
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
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

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
