<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TenantRequest;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TenantController extends Controller
{
    public function __construct(private readonly TenantService $tenantService) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Tenants/Index', [
            'tenants' => $this->tenantService->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Tenants/Create');
    }

    public function store(TenantRequest $request): RedirectResponse
    {
        $this->tenantService->create($request->validated());

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant criado com sucesso!');
    }

    public function show(Tenant $tenant): Response
    {
        $tenant->load('domains');

        return Inertia::render('Admin/Tenants/Show', [
            'tenant' => $tenant,
        ]);
    }

    public function edit(Tenant $tenant): Response
    {
        $tenant->load('domains');

        return Inertia::render('Admin/Tenants/Edit', [
            'tenant' => $tenant,
        ]);
    }

    public function update(TenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $this->tenantService->update($tenant, $request->validated());

        return redirect()->route('admin.tenants.show', $tenant->id)
            ->with('success', 'Tenant atualizado com sucesso!');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $this->tenantService->delete($tenant);

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant removido com sucesso!');
    }
}
