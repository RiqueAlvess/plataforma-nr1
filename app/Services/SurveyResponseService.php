<?php

namespace App\Services;

use App\Models\DimensionScore;
use App\Models\ResponseAnswer;
use App\Models\SurveyInvite;
use App\Models\SurveyResponse;
use App\Support\HseItQuestionnaire;

class SurveyResponseService
{
    public function registrar(
        SurveyInvite $invite,
        array $dados,
        array $respostas
    ): SurveyResponse {
        $response = SurveyResponse::create([
            'campaign_id'      => $invite->campaign_id,
            'survey_invite_id' => $invite->id,
            'genero'           => $dados['genero'] ?? null,
            'faixa_etaria'     => $dados['faixa_etaria'] ?? null,
            'consentimento_aceito' => true,
            'respondido_em'    => now(),
        ]);

        $this->salvarRespostas($response, $respostas);
        $this->calcularEsalvarScores($response, $respostas);

        // Mark invite as answered
        $invite->update(['resposta_status' => 'respondido']);

        return $response;
    }

    private function salvarRespostas(SurveyResponse $response, array $respostas): void
    {
        $perguntas = HseItQuestionnaire::PERGUNTAS;

        foreach ($perguntas as $pergunta) {
            $numero = $pergunta['numero'];
            $valor  = $respostas[$numero] ?? null;

            if ($valor === null) {
                continue;
            }

            ResponseAnswer::create([
                'survey_response_id' => $response->id,
                'questao_numero'     => $numero,
                'dimensao'           => $pergunta['dimensao'],
                'valor'              => (int) $valor,
            ]);
        }
    }

    private function calcularEsalvarScores(SurveyResponse $response, array $respostas): void
    {
        foreach (array_keys(HseItQuestionnaire::DIMENSOES) as $dimensao) {
            $score       = HseItQuestionnaire::calcularScore($dimensao, $respostas);
            $ehNegativa  = HseItQuestionnaire::isDimensaoNegativa($dimensao);
            $scoreRisco  = HseItQuestionnaire::calcularScoreRisco($dimensao, $score);
            $prob        = HseItQuestionnaire::calcularProbabilidade($scoreRisco);
            $sev         = HseItQuestionnaire::calcularSeveridade();
            $nr          = HseItQuestionnaire::calcularNR($prob, $sev);
            $classif     = HseItQuestionnaire::classificarRisco($nr);

            DimensionScore::create([
                'survey_response_id' => $response->id,
                'campaign_id'        => $response->campaign_id,
                'dimensao'           => $dimensao,
                'score'              => $score,
                'dimensao_negativa'  => $ehNegativa,
                'score_risco'        => $scoreRisco,
                'probabilidade'      => $prob,
                'severidade'         => $sev,
                'nr'                 => $nr,
                'classificacao_risco' => $classif,
                'genero'             => $response->genero,
                'faixa_etaria'       => $response->faixa_etaria,
            ]);
        }
    }
}
