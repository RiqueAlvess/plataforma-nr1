<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function show(): Response
    {
        $isTenant = app(\Stancl\Tenancy\Tenancy::class)->initialized;

        return Inertia::render('Auth/Login', [
            'loginStorePath'     => $isTenant ? route('tenant.login.store') : route('login.store'),
            'forgotPasswordPath' => $isTenant ? route('tenant.password.request') : route('password.request'),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $isTenant = app(\Stancl\Tenancy\Tenancy::class)->initialized;

        $user = $this->authService->attempt(
            $request->email,
            $request->password
        );

        // Admin login is only for global admins on the central domain
        if (! $isTenant && $user->role !== UserRole::GLOBAL_ADMIN) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => ['Esta página de login é exclusiva para administradores globais.'],
            ]);
        }

        $request->session()->regenerate();

        auth()->login($user, $request->boolean('remember'));

        $dashboardRoute = $user->role === UserRole::GLOBAL_ADMIN
            ? route('admin.dashboard')
            : route('tenant.dashboard');

        return redirect()->intended($dashboardRoute);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->authService->logout(auth()->user());

        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to tenant login if in tenant context, otherwise admin login
        $loginRoute = app(\Stancl\Tenancy\Tenancy::class)->initialized
            ? route('tenant.login')
            : route('login');

        return redirect($loginRoute);
    }
}
