<?php

namespace App\Providers;

use App\Services\AuthService;
use App\Services\CsvImportService;
use App\Services\PasswordResetService;
use App\Services\TenantService;
use App\Services\TenantUserService;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuthService::class);
        $this->app->singleton(TenantService::class);
        $this->app->singleton(PasswordResetService::class);
        $this->app->singleton(CsvImportService::class);
        $this->app->singleton(TenantUserService::class);
    }

    public function boot(): void
    {
        Password::defaults(function () {
            return Password::min(8)->mixedCase()->numbers();
        });

        Queue::failing(function (JobFailed $event) {
            \Log::error('Queue job failed', [
                'job' => $event->job->getName(),
                'exception' => $event->exception->getMessage(),
            ]);
        });
    }
}
