<?php

namespace App\Jobs;

use App\Models\DimensionScore;
use App\Models\SurveyResponse;
use App\Support\HseItQuestionnaire;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSurveyResponseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly SurveyResponse $response
    ) {}

    public function handle(): void
    {
        $answers = $this->response->answers->keyBy('pergunta_numero');

        foreach (HseItQuestionnaire::DIMENSOES as $chave => $label) {
            $perguntas = HseItQuestionnaire::getPerguntasDaDimensao($chave);
            $respostasDim = $perguntas
                ->map(fn($p) => $answers->get($p['numero']))
                ->filter();

            if ($respostasDim->isEmpty()) {
                continue;
            }

            $score = round($respostasDim->avg('resposta'), 2);
            $isNegativa = in_array($chave, HseItQuestionnaire::DIMENSOES_NEGATIVAS);

            $scoreRisco = HseItQuestionnaire::calcularScoreRisco($chave, $score);
            $probabilidade = HseItQuestionnaire::calcularProbabilidade($scoreRisco);
            $severidade = HseItQuestionnaire::calcularSeveridade();
            $nr = HseItQuestionnaire::calcularNR($probabilidade, $severidade);
            $classificacao = HseItQuestionnaire::classificarRisco($nr);

            DimensionScore::create([
                'survey_response_id' => $this->response->id,
                'campaign_id'        => $this->response->campaign_id,
                'dimensao'           => $chave,
                'score'              => $score,
                'dimensao_negativa'  => $isNegativa,
                'score_risco'        => $scoreRisco,
                'probabilidade'      => $probabilidade,
                'severidade'         => $severidade,
                'nr'                 => $nr,
                'classificacao_risco' => $classificacao,
                'genero'             => $this->response->genero,
                'faixa_etaria'       => $this->response->faixa_etaria,
                'unidade_id'         => null,
                'setor_id'           => null,
            ]);
        }

        Log::info("Processed response {$this->response->id}");
    }
}
