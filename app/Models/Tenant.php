<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'company_name',
        'cnpj',
        'cnae',
        'responsible_email',
        'is_active',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'data' => 'array',
        ];
    }

    public static array $customColumns = [
        'id',
        'company_name',
        'cnpj',
        'cnae',
        'responsible_email',
        'is_active',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
