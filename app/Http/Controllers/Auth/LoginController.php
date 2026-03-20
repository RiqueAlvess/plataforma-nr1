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
        return Inertia::render('Auth/Login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $user = $this->authService->attempt(
            $request->email,
            $request->password
        );

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
