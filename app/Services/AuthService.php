<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AccountLockedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function attempt(string $email, string $password): User
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        if ($user->isLocked()) {
            throw ValidationException::withMessages([
                'email' => ['Sua conta está bloqueada. Verifique seu email para desbloquear.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Sua conta está desativada.'],
            ]);
        }

        if (! Hash::check($password, $user->password)) {
            $user->incrementFailedAttempts();

            if ($user->isLocked()) {
                $user->notify(new AccountLockedNotification());
            }

            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $user->resetFailedAttempts();

        return $user;
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
