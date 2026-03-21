<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Helper: check if current request is on a central domain
$isCentralDomain = function () {
    $host = request()->getHost();
    return in_array($host, config('tenancy.central_domains', []));
};

// Root redirect — detect tenant domain and redirect accordingly
Route::get('/', function () use ($isCentralDomain) {
    if ($isCentralDomain()) {
        return redirect()->route('login');
    }
    // On tenant domain, redirect to tenant login
    return redirect('/login');
});

// Named 'home' route used by Laravel's guest middleware when user is already authenticated
Route::get('/home', function () use ($isCentralDomain) {
    if (auth()->check()) {
        if (auth()->user()->isGlobalAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        // Authenticated non-admin: send to dashboard
        return redirect('/dashboard');
    }
    if (! $isCentralDomain()) {
        return redirect('/login');
    }
    return redirect()->route('login');
})->name('home');

// Central domain auth routes (use /admin/login to avoid conflict with tenant /login route)
Route::middleware('guest')->group(function () use ($isCentralDomain) {
    Route::get('/admin/login', function () use ($isCentralDomain) {
        if (! $isCentralDomain()) {
            return redirect('/login');
        }
        return app(Auth\LoginController::class)->show();
    })->name('login');

    Route::post('/admin/login', function (\App\Http\Requests\Auth\LoginRequest $request) use ($isCentralDomain) {
        if (! $isCentralDomain()) {
            return redirect('/login');
        }
        return app(Auth\LoginController::class)->store($request);
    })->middleware('throttle:3,1')->name('login.store');

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
        Route::post('tenants/{tenant}/repair-database', [Admin\TenantController::class, 'repairDatabase'])
            ->name('tenants.repair-database');
        Route::resource('users', Admin\UserController::class);
        Route::post('users/{user}/toggle-lock', [Admin\UserController::class, 'toggleLock'])
            ->name('users.toggle-lock');
    });
