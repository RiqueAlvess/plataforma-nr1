<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $requiredRole = UserRole::from(strtoupper($role));

        if ($user->role !== $requiredRole) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
