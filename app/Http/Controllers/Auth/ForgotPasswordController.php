<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ForgotPasswordController extends Controller
{
    public function __construct(private readonly PasswordResetService $passwordResetService) {}

    public function show(): Response
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $this->passwordResetService->sendResetLink($request->email);

        return back()->with('status', 'Se um usuário com este email existir, você receberá um link de recuperação de senha em breve.');
    }
}
