<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Root redirect to admin login
Route::get('/', fn () => redirect()->route('login'));

// Central domain auth routes (use /admin/login to avoid conflict with tenant /login route)
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [Auth\LoginController::class, 'show'])->name('login');
    Route::post('/admin/login', [Auth\LoginController::class, 'store'])
        ->middleware('throttle:3,1')
        ->name('login.store');

    Route::get('/admin/forgot-password', [Auth\ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/admin/forgot-password', [Auth\ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/admin/reset-password', [Auth\ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/admin/reset-password', [Auth\ResetPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/admin/logout', [Auth\LoginController::class, 'destroy'])->name('logout');
});

// Admin routes (Global Admin only)
Route::middleware(['auth', 'role:global_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('tenants', Admin\TenantController::class);
        Route::resource('users', Admin\UserController::class);
        Route::post('users/{user}/toggle-lock', [Admin\UserController::class, 'toggleLock'])
            ->name('users.toggle-lock');
    });
