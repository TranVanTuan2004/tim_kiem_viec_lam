<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\SupportChatController;

// Client Homepage
Route::get('/', function () {
    return Inertia::render('client/Home');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

// Support Chat Widget - Available for all authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('support/messages', [SupportChatController::class, 'messages'])->name('support.messages');
    Route::post('support/send', [SupportChatController::class, 'send'])->name('support.send');
    Route::post('support/messages/{message}/read', [SupportChatController::class, 'markAsRead'])->name('support.mark-read');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
