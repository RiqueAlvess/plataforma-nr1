<?php

namespace App\Http\Controllers\Auth;

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

        // Detectar contexto pelo parâmetro de rota (path-based tenancy)
        $tenant = $request->route('tenant');

        if ($tenant) {
            return redirect()->intended(route('tenant.dashboard', ['tenant' => $tenant]));
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $tenant = $request->route('tenant');

        $this->authService->logout(auth()->user());

        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($tenant) {
            return redirect()->route('tenant.login', ['tenant' => $tenant]);
        }

        return redirect()->route('login');
    }
}
