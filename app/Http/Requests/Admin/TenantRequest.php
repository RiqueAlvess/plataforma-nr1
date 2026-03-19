<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isGlobalAdmin() ?? false;
    }

    public function rules(): array
    {
        $tenantId = $this->route('tenant')?->id;

        return [
            'company_name' => ['required', 'string', 'max:255'],
            'cnpj' => ['required', 'string', 'size:18', 'unique:tenants,cnpj,' . $tenantId . ',id'],
            'cnae' => ['nullable', 'string', 'max:20'],
            'responsible_email' => ['required', 'email'],
            'is_active' => ['boolean'],
            'domain' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'O nome da empresa é obrigatório.',
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'cnpj.size' => 'O CNPJ deve estar no formato XX.XXX.XXX/XXXX-XX.',
            'responsible_email.required' => 'O email do responsável é obrigatório.',
            'responsible_email.email' => 'Digite um email válido.',
        ];
    }
}
