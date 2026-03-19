<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGlobalAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->isGlobalAdmin()) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
