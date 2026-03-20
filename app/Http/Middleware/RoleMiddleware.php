<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $allowedRoles = array_map(fn($r) => strtoupper($r), $roles);

        // GLOBAL_ADMIN sempre tem acesso
        if ($user->role === UserRole::GLOBAL_ADMIN) {
            return $next($request);
        }

        if (!in_array(strtoupper($user->role->value), $allowedRoles)) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
