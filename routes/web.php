<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Central domain auth routes (use /admin/login to avoid conflict with tenant /login route)
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [Auth\LoginController::class, 'show'])->name('login');
    Route::post('/admin/login', [Auth\LoginController::class, 'store'])
        ->middleware('throttle:3,1')
        ->name('login.store');
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
