<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class CsvImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isRh();
    }

    public function rules(): array
    {
        return [
            'arquivo' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'arquivo.required' => 'O arquivo CSV é obrigatório.',
            'arquivo.file' => 'O campo deve ser um arquivo.',
            'arquivo.mimes' => 'O arquivo deve ser do tipo CSV.',
            'arquivo.max' => 'O arquivo não pode ser maior que 10MB.',
        ];
    }
}
