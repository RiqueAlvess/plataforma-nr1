<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TenantRequest;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Stancl\Tenancy\Jobs\MigrateDatabase;

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
        try {
            $tenant = $this->tenantService->create($request->validated());
        } catch (\Throwable $e) {
            \Log::error('Erro ao criar tenant', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao criar tenant: ' . $e->getMessage());
        }

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant criado com sucesso! Banco de dados provisionado.');
    }

    public function show(Tenant $tenant): Response
    {
        $tenant->load(['domains', 'users']);

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

    public function repairDatabase(Tenant $tenant): RedirectResponse
    {
        try {
            dispatch_sync(new CreateDatabase($tenant));
        } catch (\Throwable $e) {
            // Database may already exist, continue to migration
            \Log::info('CreateDatabase: ' . $e->getMessage());
        }

        try {
            dispatch_sync(new MigrateDatabase($tenant));
        } catch (\Throwable $e) {
            \Log::error('MigrateDatabase falhou', ['tenant' => $tenant->id, 'error' => $e->getMessage()]);

            return redirect()->route('admin.tenants.show', $tenant->id)
                ->with('error', 'Erro ao migrar banco: ' . $e->getMessage());
        }

        return redirect()->route('admin.tenants.show', $tenant->id)
            ->with('success', 'Banco de dados criado/reparado com sucesso!');
    }
}
