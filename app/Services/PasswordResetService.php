<?php

namespace App\Services;

use App\Jobs\SendPasswordResetEmail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PasswordResetService
{
    public function sendResetLink(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return;
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => hash('sha256', $token), 'created_at' => now()]
        );

        SendPasswordResetEmail::dispatch($user, $token);
    }

    public function reset(string $email, string $token, string $password): bool
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (! $record) {
            return false;
        }

        if (! hash_equals($record->token, hash('sha256', $token))) {
            return false;
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            return false;
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return false;
        }

        $user->update(['password' => $password]);
        $user->resetFailedAttempts();

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return true;
    }
}
