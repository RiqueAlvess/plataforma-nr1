<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isGlobalAdmin() ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $role = $this->input('role');
        $requiresTenant = in_array($role, [UserRole::RH->value, UserRole::LEADER->value]);

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('tenant_id', $this->input('tenant_id'));
                })->ignore($userId),
            ],
            'password' => $isUpdate
                ? ['nullable', Password::defaults()]
                : ['required', Password::defaults()],
            'role' => ['required', Rule::enum(UserRole::class)],
            'tenant_id' => $requiresTenant
                ? ['required', 'string', 'exists:tenants,id']
                : ['nullable', 'string', 'exists:tenants,id'],
            'is_active' => ['boolean'],
        ];
    }
}
