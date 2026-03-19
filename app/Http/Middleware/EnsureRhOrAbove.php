<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRhOrAbove
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user || ! in_array($user->role, [UserRole::GLOBAL_ADMIN, UserRole::RH])) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
