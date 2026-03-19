<?php

declare(strict_types=1);

use App\Http\Controllers\Auth;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    // Auth routes (guest only)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [Auth\LoginController::class, 'show'])->name('tenant.login');
        Route::post('/login', [Auth\LoginController::class, 'store'])
            ->middleware('throttle:3,1')
            ->name('tenant.login.store');

        Route::get('/forgot-password', [Auth\ForgotPasswordController::class, 'show'])->name('tenant.password.request');
        Route::post('/forgot-password', [Auth\ForgotPasswordController::class, 'store'])->name('tenant.password.email');

        Route::get('/reset-password', [Auth\ResetPasswordController::class, 'show'])->name('tenant.password.reset');
        Route::post('/reset-password', [Auth\ResetPasswordController::class, 'store'])->name('tenant.password.update');
    });

    // Authenticated routes
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [Auth\LoginController::class, 'destroy'])->name('tenant.logout');
        Route::get('/', fn () => redirect()->route('tenant.dashboard'));
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');
    });
});
