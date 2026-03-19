<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class TenantService
{
    public function create(array $data): Tenant
    {
        $tenant = Tenant::create([
            'id' => Str::slug($data['company_name']) . '-' . Str::random(6),
            'company_name' => $data['company_name'],
            'cnpj' => $data['cnpj'],
            'cnae' => $data['cnae'] ?? null,
            'responsible_email' => $data['responsible_email'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        if (! empty($data['domain'])) {
            $tenant->domains()->create(['domain' => $data['domain']]);
        }

        return $tenant;
    }

    public function update(Tenant $tenant, array $data): Tenant
    {
        $tenant->update([
            'company_name' => $data['company_name'],
            'cnpj' => $data['cnpj'],
            'cnae' => $data['cnae'] ?? null,
            'responsible_email' => $data['responsible_email'],
            'is_active' => $data['is_active'] ?? $tenant->is_active,
        ]);

        return $tenant->fresh();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Tenant::with('domains')->paginate($perPage);
    }

    public function delete(Tenant $tenant): void
    {
        $tenant->delete();
    }
}
