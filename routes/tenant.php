<?php

declare(strict_types=1);

use App\Http\Controllers\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\Tenant\CampaignController;
use App\Http\Controllers\Tenant\CsvImportController;
use App\Http\Controllers\Tenant\SurveyInviteController;
use App\Http\Controllers\Tenant\UserController as TenantUserController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    // Public survey routes (no auth required)
    Route::prefix('pesquisa')->name('pesquisa.')->group(function () {
        Route::get('/concluido', [SurveyController::class, 'concluido'])->name('concluido');
        Route::get('/{token}', [SurveyController::class, 'consentimento'])->name('consentimento');
        Route::get('/{token}/questionario', [SurveyController::class, 'questionario'])->name('questionario');
        Route::post('/{token}/responder', [SurveyController::class, 'responder'])->name('responder');
    });

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

        // Dashboard: accessible by all authenticated roles (RH, LEADER, GLOBAL_ADMIN)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');

        // Rotas exclusivas para RH
        Route::middleware('role:rh')->group(function () {

            // Módulo de Importação CSV
            Route::prefix('importacao')->name('tenant.importacao.')->group(function () {
                Route::get('/', [CsvImportController::class, 'index'])->name('index');
                Route::get('/nova', [CsvImportController::class, 'create'])->name('create');
                Route::post('/', [CsvImportController::class, 'store'])->name('store');
                Route::get('/{import}', [CsvImportController::class, 'show'])->name('show');
                Route::delete('/{import}', [CsvImportController::class, 'destroy'])->name('destroy');
            });

            // Campanhas de Pesquisa
            Route::prefix('campanhas')->name('tenant.campanhas.')->group(function () {
                Route::get('/', [CampaignController::class, 'index'])->name('index');
                Route::get('/nova', [CampaignController::class, 'create'])->name('create');
                Route::post('/', [CampaignController::class, 'store'])->name('store');
                Route::get('/{campaign}', [CampaignController::class, 'show'])->name('show');
                Route::get('/{campaign}/editar', [CampaignController::class, 'edit'])->name('edit');
                Route::put('/{campaign}', [CampaignController::class, 'update'])->name('update');
                Route::delete('/{campaign}', [CampaignController::class, 'destroy'])->name('destroy');
                Route::post('/{campaign}/toggle-status', [CampaignController::class, 'toggleStatus'])->name('toggle-status');
                Route::get('/{campaign}/analytics', [CampaignController::class, 'analytics'])->name('analytics');
                // Survey invites
                Route::post('/{campaign}/convites/preparar', [SurveyInviteController::class, 'preparar'])->name('convites.preparar');
                Route::post('/{campaign}/convites/enviar', [SurveyInviteController::class, 'enviar'])->name('convites.enviar');
            });

            // Gestão de Usuários do Tenant
            Route::prefix('usuarios')->name('tenant.usuarios.')->group(function () {
                Route::get('/', [TenantUserController::class, 'index'])->name('index');
                Route::get('/novo', [TenantUserController::class, 'create'])->name('create');
                Route::post('/', [TenantUserController::class, 'store'])->name('store');
                Route::get('/{usuario}/editar', [TenantUserController::class, 'edit'])->name('edit');
                Route::put('/{usuario}', [TenantUserController::class, 'update'])->name('update');
                Route::delete('/{usuario}', [TenantUserController::class, 'destroy'])->name('destroy');
                Route::post('/{usuario}/toggle-lock', [TenantUserController::class, 'toggleLock'])->name('toggle-lock');
            });
        });
    });
});
