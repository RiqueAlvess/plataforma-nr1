<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

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

        $tenantName = null;
        if ($user && $user->tenant_id) {
            try {
                $tenantName = \App\Models\Tenant::find($user->tenant_id)?->company_name;
            } catch (\Throwable) {
                // May fail if not in tenant context
                $tenantName = tenancy()->tenant?->company_name ?? null;
            }
        } elseif (app()->bound('tenant') && tenancy()->initialized) {
            try {
                $tenantName = tenancy()->tenant?->company_name ?? null;
            } catch (\Throwable) {
                // ignore
            }
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'email'       => $user->email,
                    'role'        => $user->role,
                    'tenant_id'   => $user->tenant_id,
                    'tenant_name' => $tenantName,
                    'is_active'   => $user->is_active,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'status'  => fn () => $request->session()->get('status'),
            ],
        ]);
    }
}
