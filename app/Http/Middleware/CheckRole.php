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
            // Redirecionar para login correto conforme contexto
            $isTenant = app()->bound('currentTenant') && tenancy()->initialized;
            return redirect()->route($isTenant ? 'tenant.login' : 'login');
        }

        $requiredRole = UserRole::from(strtoupper($role));

        // GLOBAL_ADMIN tem acesso a tudo
        if ($user->role === UserRole::GLOBAL_ADMIN) {
            return $next($request);
        }

        if ($user->role !== $requiredRole) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
