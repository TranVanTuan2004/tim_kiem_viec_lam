<?php

use App\Http\Controllers\Client\JobPostingController;
use App\Http\Controllers\Client\CompanyController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\UserController;

// Client Homepage
Route::get('/', function () {
    return Inertia::render('client/Home');
})->name('home');

// Job Detail Page
Route::get('/jobs/{job_posting}', [JobPostingController::class, 'show'])->name('jobs.show');

// Company Pages
Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::resource('users', UserController::class);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
