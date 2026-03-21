<?php

use App\Http\Middleware\EnsureGlobalAdmin;
use App\Http\Middleware\EnsureRhOrAbove;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Stancl\Tenancy\Exceptions\TenantDatabaseDoesNotExistException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Redirect unauthenticated users to the correct login based on path context
        $middleware->redirectGuestsTo(function (Request $request) {
            $tenant = $request->route('tenant');
            if ($tenant) {
                return route('tenant.login', ['tenant' => $tenant]);
            }
            return route('login');
        });

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'global_admin' => EnsureGlobalAdmin::class,
            'rh_or_above' => EnsureRhOrAbove::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TenantDatabaseDoesNotExistException $e, Request $request) {
            return response()->view('errors.tenant-database-missing', [
                'tenantId' => tenancy()->initialized ? tenant('id') : null,
            ], 503);
        });
    })->create();
