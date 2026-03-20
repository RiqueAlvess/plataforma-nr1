<?php

namespace App\Services;

use App\Jobs\ProcessSurveyResponseJob;
use App\Models\SurveyAnswer;
use App\Models\SurveyInvite;
use App\Models\SurveyResponse;

class SurveyResponseService
{
    public function registrar(
        SurveyInvite $invite,
        array $demograficos,
        array $respostas
    ): SurveyResponse {
        $response = SurveyResponse::create([
            'campaign_id'          => $invite->campaign_id,
            'survey_invite_id'     => $invite->id,
            'genero'               => $demograficos['genero'] ?? null,
            'faixa_etaria'         => $demograficos['faixa_etaria'] ?? null,
            'consentimento_aceito' => true,
            'respondido_em'        => now(),
        ]);

        foreach ($respostas as $perguntaNumero => $resposta) {
            SurveyAnswer::create([
                'survey_response_id' => $response->id,
                'pergunta_numero'    => $perguntaNumero,
                'resposta'           => $resposta,
            ]);
        }

        ProcessSurveyResponseJob::dispatch($response);

        $invite->update(['resposta_status' => 'respondido']);

        return $response;
    }
}
