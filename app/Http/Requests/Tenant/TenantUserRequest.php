<?php

namespace App\Http\Requests\Tenant;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class TenantUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isRh();
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario');
        $isCreating = $this->isMethod('post');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($usuario?->id),
            ],
            'password' => [
                $isCreating ? 'required' : 'nullable',
                'string',
                Password::min(8)->mixedCase()->numbers(),
            ],
            'role' => [
                'required',
                Rule::in([UserRole::RH->value, UserRole::LEADER->value]),
            ],
            'is_active' => ['boolean'],
            'permissoes' => ['nullable', 'array'],
            'permissoes.*.unidade_id' => ['required_with:permissoes', 'integer', 'exists:unidades,id'],
            'permissoes.*.setor_id' => ['nullable', 'integer', 'exists:setores,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Informe um email válido.',
            'email.unique' => 'Este email já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'role.required' => 'O perfil é obrigatório.',
            'role.in' => 'Perfil inválido. Os valores permitidos são: RH e LEADER.',
            'permissoes.*.unidade_id.required_with' => 'A unidade é obrigatória em cada permissão.',
            'permissoes.*.unidade_id.exists' => 'Unidade não encontrada.',
            'permissoes.*.setor_id.exists' => 'Setor não encontrado.',
        ];
    }
}
