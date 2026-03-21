<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [Auth\LoginController::class, 'show'])->name('login');
    Route::post('/login', [Auth\LoginController::class, 'store'])
        ->middleware('throttle:3,1')
        ->name('login.store');

    Route::get('/forgot-password', [Auth\ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [Auth\ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password', [Auth\ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [Auth\ResetPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [Auth\LoginController::class, 'destroy'])->name('logout');
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Admin routes (Global Admin only)
Route::middleware(['auth', 'role:GLOBAL_ADMIN'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [Auth\LoginController::class, 'destroy'])->name('logout');

        Route::resource('tenants', Admin\TenantController::class);
        Route::post('tenants/{tenant}/repair-database', [Admin\TenantController::class, 'repairDatabase'])
            ->name('tenants.repair-database');
        Route::resource('users', Admin\UserController::class);
        Route::post('users/{user}/toggle-lock', [Admin\UserController::class, 'toggleLock'])
            ->name('users.toggle-lock');
    });
