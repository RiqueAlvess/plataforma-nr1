<?php

namespace App\Http\Requests;

use App\Support\HseItQuestionnaire;
use Illuminate\Foundation\Http\FormRequest;

class SurveyAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $numeros = array_column(HseItQuestionnaire::PERGUNTAS, 'numero');
        $rules = [
            'genero'      => ['nullable', 'in:masculino,feminino,outro,nao_informado'],
            'faixa_etaria' => ['nullable', 'string', 'max:20'],
            'respostas'   => ['required', 'array'],
        ];

        foreach ($numeros as $n) {
            $rules["respostas.{$n}"] = ['required', 'integer', 'min:0', 'max:4'];
        }

        return $rules;
    }
}
