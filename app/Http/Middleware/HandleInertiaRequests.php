<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        $tenantId = null;
        $tenantName = null;

        if (tenancy()->initialized) {
            $tenantId = tenancy()->tenant->id;
            $tenantName = tenancy()->tenant->company_name ?? null;
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'email'       => $user->email,
                    'role'        => $user->role->value,
                    'tenant_id'   => $user->tenant_id ?? $tenantId,
                    'tenant_name' => $tenantName,
                    'is_active'   => $user->is_active,
                ] : null,
            ],
            'isTenantContext' => tenancy()->initialized,
            'currentTenant'   => $tenantId,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'status'  => fn () => $request->session()->get('status'),
            ],
            // Ziggy com defaults de tenant para que route() funcione sem passar {tenant} explicitamente
            'ziggy' => fn () => array_merge((new Ziggy)->toArray(), [
                'location' => $request->url(),
                'defaults' => ['tenant' => $tenantId],
            ]),
        ]);
    }
}
