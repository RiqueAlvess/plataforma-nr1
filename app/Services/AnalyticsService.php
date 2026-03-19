<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\DimensionScore;
use App\Models\SurveyInvite;
use App\Support\HseItQuestionnaire;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getDashboardData(Campaign $campaign): array
    {
        $campaignId = $campaign->id;

        return [
            'metricas'          => $this->getMetricas($campaign),
            'scores_dimensoes'  => $this->getScoresPorDimensao($campaignId),
            'distribuicao_risco' => $this->getDistribuicaoRisco($campaignId),
            'distribuicao_genero' => $this->getDistribuicaoGenero($campaignId),
            'distribuicao_faixa' => $this->getDistribuicaoFaixa($campaignId),
            'scores_por_genero' => $this->getScoresPorGenero($campaignId),
            'scores_por_faixa'  => $this->getScoresPorFaixa($campaignId),
        ];
    }

    private function getMetricas(Campaign $campaign): array
    {
        $campaignId = $campaign->id;

        $totalConvidados = SurveyInvite::where('campaign_id', $campaignId)->count();
        $totalRespondidos = SurveyInvite::where('campaign_id', $campaignId)
            ->where('resposta_status', 'respondido')->count();

        $taxaAdesao = $totalConvidados > 0
            ? round(($totalRespondidos / $totalConvidados) * 100, 1)
            : 0.0;

        // IGRP: average of all dimension risk scores (0–100%)
        $igrp = DimensionScore::where('campaign_id', $campaignId)
            ->avg('score_risco');
        $igrp = $igrp ? round($igrp * 100, 1) : 0.0;

        // % of dimension scores classified as 'importante' or 'critico'
        $totalScores = DimensionScore::where('campaign_id', $campaignId)->count();
        $altosRisco = DimensionScore::where('campaign_id', $campaignId)
            ->whereIn('classificacao_risco', ['importante', 'critico'])
            ->count();

        $percentualRiscoAlto = $totalScores > 0
            ? round(($altosRisco / $totalScores) * 100, 1)
            : 0.0;

        return [
            'total_convidados'    => $totalConvidados,
            'total_respondidos'   => $totalRespondidos,
            'taxa_adesao'         => $taxaAdesao,
            'igrp'                => $igrp,
            'percentual_risco_alto' => $percentualRiscoAlto,
        ];
    }

    private function getScoresPorDimensao(int $campaignId): array
    {
        $rows = DimensionScore::where('campaign_id', $campaignId)
            ->select('dimensao', DB::raw('ROUND(AVG(score)::numeric, 2) as score_medio'))
            ->groupBy('dimensao')
            ->get()
            ->keyBy('dimensao');

        $result = [];

        foreach (HseItQuestionnaire::DIMENSOES as $key => $label) {
            $result[] = [
                'dimensao'  => $key,
                'label'     => $label,
                'score'     => $rows->has($key) ? (float) $rows[$key]->score_medio : 0.0,
                'negativa'  => HseItQuestionnaire::isDimensaoNegativa($key),
            ];
        }

        return $result;
    }

    private function getDistribuicaoRisco(int $campaignId): array
    {
        return DimensionScore::where('campaign_id', $campaignId)
            ->select('classificacao_risco', DB::raw('COUNT(*) as total'))
            ->groupBy('classificacao_risco')
            ->get()
            ->map(fn ($r) => [
                'classificacao' => $r->classificacao_risco,
                'label'         => HseItQuestionnaire::getLabelClassificacao($r->classificacao_risco),
                'total'         => (int) $r->total,
            ])
            ->values()
            ->toArray();
    }

    private function getDistribuicaoGenero(int $campaignId): array
    {
        return DimensionScore::where('campaign_id', $campaignId)
            ->whereNotNull('genero')
            ->select('genero', DB::raw('COUNT(DISTINCT survey_response_id) as total'))
            ->groupBy('genero')
            ->get()
            ->map(fn ($r) => [
                'genero' => $r->genero,
                'label'  => $this->labelGenero($r->genero),
                'total'  => (int) $r->total,
            ])
            ->values()
            ->toArray();
    }

    private function getDistribuicaoFaixa(int $campaignId): array
    {
        return DimensionScore::where('campaign_id', $campaignId)
            ->whereNotNull('faixa_etaria')
            ->select('faixa_etaria', DB::raw('COUNT(DISTINCT survey_response_id) as total'))
            ->groupBy('faixa_etaria')
            ->orderBy('faixa_etaria')
            ->get()
            ->map(fn ($r) => [
                'faixa' => $r->faixa_etaria,
                'total' => (int) $r->total,
            ])
            ->values()
            ->toArray();
    }

    private function getScoresPorGenero(int $campaignId): array
    {
        $rows = DimensionScore::where('campaign_id', $campaignId)
            ->whereNotNull('genero')
            ->select('genero', 'dimensao', DB::raw('ROUND(AVG(score)::numeric, 2) as score_medio'))
            ->groupBy('genero', 'dimensao')
            ->get();

        $generos = $rows->pluck('genero')->unique()->values()->toArray();
        $result  = [];

        foreach (array_keys(HseItQuestionnaire::DIMENSOES) as $dimensao) {
            $entry = ['dimensao' => $dimensao, 'label' => HseItQuestionnaire::DIMENSOES[$dimensao]];

            foreach ($generos as $genero) {
                $match = $rows->first(fn ($r) => $r->genero === $genero && $r->dimensao === $dimensao);
                $entry[$genero] = $match ? (float) $match->score_medio : null;
            }

            $result[] = $entry;
        }

        return ['generos' => $generos, 'dados' => $result];
    }

    private function getScoresPorFaixa(int $campaignId): array
    {
        $rows = DimensionScore::where('campaign_id', $campaignId)
            ->whereNotNull('faixa_etaria')
            ->select('faixa_etaria', 'dimensao', DB::raw('ROUND(AVG(score)::numeric, 2) as score_medio'))
            ->groupBy('faixa_etaria', 'dimensao')
            ->get();

        $faixas = $rows->pluck('faixa_etaria')->unique()->sort()->values()->toArray();
        $result = [];

        foreach (array_keys(HseItQuestionnaire::DIMENSOES) as $dimensao) {
            $entry = ['dimensao' => $dimensao, 'label' => HseItQuestionnaire::DIMENSOES[$dimensao]];

            foreach ($faixas as $faixa) {
                $match = $rows->first(fn ($r) => $r->faixa_etaria === $faixa && $r->dimensao === $dimensao);
                $entry[$faixa] = $match ? (float) $match->score_medio : null;
            }

            $result[] = $entry;
        }

        return ['faixas' => $faixas, 'dados' => $result];
    }

    private function labelGenero(string $genero): string
    {
        return match ($genero) {
            'masculino'    => 'Masculino',
            'feminino'     => 'Feminino',
            'outro'        => 'Outro',
            'nao_informado' => 'Prefiro não dizer',
            default        => $genero,
        };
    }
}
