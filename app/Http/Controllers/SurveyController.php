<?php

namespace App\Http\Controllers;

use App\Http\Requests\SurveyAnswerRequest;
use App\Models\SurveyInvite;
use App\Services\SurveyResponseService;
use App\Support\HseItQuestionnaire;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class SurveyController extends Controller
{
    public function __construct(
        private readonly SurveyResponseService $responseService
    ) {}

    /**
     * Show LGPD consent page (entry point via magic link).
     */
    public function consentimento(string $token): Response|RedirectResponse
    {
        $invite = SurveyInvite::where('token', $token)->first();

        if (! $invite || $invite->foiRespondido()) {
            return Inertia::render('Pesquisa/Invalido', [
                'motivo' => $invite?->foiRespondido()
                    ? 'Esta pesquisa já foi respondida.'
                    : 'Link inválido ou expirado.',
            ]);
        }

        return Inertia::render('Pesquisa/Consentimento', [
            'token'    => $token,
            'campanha' => $invite->campaign->nome,
        ]);
    }

    /**
     * Show demographic + questionnaire page.
     */
    public function questionario(string $token): Response|RedirectResponse
    {
        $invite = SurveyInvite::where('token', $token)->first();

        if (! $invite || $invite->foiRespondido()) {
            return Inertia::render('Pesquisa/Invalido', [
                'motivo' => $invite?->foiRespondido()
                    ? 'Esta pesquisa já foi respondida.'
                    : 'Link inválido ou expirado.',
            ]);
        }

        return Inertia::render('Pesquisa/Questionario', [
            'token'     => $token,
            'campanha'  => $invite->campaign->nome,
            'perguntas' => HseItQuestionnaire::PERGUNTAS,
            'dimensoes' => HseItQuestionnaire::DIMENSOES,
            'opcoes'    => HseItQuestionnaire::OPCOES_RESPOSTA,
        ]);
    }

    /**
     * Store survey answers.
     */
    public function responder(SurveyAnswerRequest $request, string $token): Response|RedirectResponse
    {
        $invite = SurveyInvite::where('token', $token)->first();

        if (! $invite || $invite->foiRespondido()) {
            return Inertia::render('Pesquisa/Invalido', [
                'motivo' => 'Link inválido, expirado ou já utilizado.',
            ]);
        }

        $this->responseService->registrar(
            $invite,
            $request->only(['genero', 'faixa_etaria']),
            $request->respostas
        );

        return redirect()->route('pesquisa.concluido');
    }

    public function concluido(): Response
    {
        return Inertia::render('Pesquisa/Concluido');
    }
}
