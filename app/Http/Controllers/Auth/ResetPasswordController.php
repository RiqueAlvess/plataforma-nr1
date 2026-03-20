<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResetPasswordController extends Controller
{
    public function __construct(private readonly PasswordResetService $passwordResetService) {}

    public function show(Request $request): Response
    {
        $isTenant = app(\Stancl\Tenancy\Tenancy::class)->initialized;

        return Inertia::render('Auth/ResetPassword', [
            'token'              => $request->token,
            'email'              => $request->email,
            'passwordUpdatePath' => $isTenant ? route('tenant.password.update') : route('password.update'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $success = $this->passwordResetService->reset(
            $request->email,
            $request->token,
            $request->password
        );

        if (! $success) {
            return back()->withErrors(['email' => 'Token inválido ou expirado.']);
        }

        $loginRoute = app(\Stancl\Tenancy\Tenancy::class)->initialized ? 'tenant.login' : 'login';

        return redirect()->route($loginRoute)->with('status', 'Senha redefinida com sucesso! Faça login com sua nova senha.');
    }
}
