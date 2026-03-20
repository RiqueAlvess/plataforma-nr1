<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\DimensionScore;
use App\Models\Setor;
use App\Models\SurveyInvite;
use App\Support\HseItQuestionnaire;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Analytics for a specific campaign (used in campaign analytics page).
     */
    public function getDashboardData(Campaign $campaign, ?int $unidadeId = null, ?int $setorId = null): array
    {
        $campaignId = $campaign->id;

        return [
            'metricas'             => $this->getMetricas($campaign, $unidadeId, $setorId),
            'scores_dimensoes'     => $this->getScoresPorDimensao($campaignId, $unidadeId, $setorId),
            'distribuicao_risco'   => $this->getDistribuicaoRisco($campaignId, $unidadeId, $setorId),
            'distribuicao_genero'  => $this->getDistribuicaoGenero($campaignId, $unidadeId, $setorId),
            'distribuicao_faixa'   => $this->getDistribuicaoFaixa($campaignId, $unidadeId, $setorId),
            'scores_por_genero'    => $this->getScoresPorGenero($campaignId, $unidadeId, $setorId),
            'scores_por_faixa'     => $this->getScoresPorFaixa($campaignId, $unidadeId, $setorId),
            'setores_criticos'     => $this->getSetoresCriticos($campaignId, $unidadeId, $setorId),
            'heatmap'              => $this->getHeatmapSetorDimensao($campaignId, $unidadeId, $setorId),
            'top_grupos_criticos'  => $this->getTopGruposCriticos($campaignId, $unidadeId, $setorId),
        ];
    }

    /**
     * Aggregate data across all campaigns for the main RH/Leader dashboards.
     */
    public function getAggregateDashboardData(
        int $tenantId,
        ?int $unidadeId = null,
        ?int $setorId = null,
        ?array $allowedUnidadeIds = null,
        ?array $allowedSetorIds = null
    ): array {
        // Get all campaign IDs for this tenant
        $campaignIds = Campaign::pluck('id')->toArray();

        if (empty($campaignIds)) {
            return $this->emptyDashboard();
        }

        return [
            'metricas'            => $this->getMetricasAgregadas($campaignIds, $unidadeId, $setorId, $allowedUnidadeIds, $allowedSetorIds),
            'scores_dimensoes'    => $this->getScoresPorDimensaoAgregados($campaignIds, $unidadeId, $setorId, $allowedUnidadeIds, $allowedSetorIds),
            'distribuicao_risco'  => $this->getDistribuicaoRiscoAgregada($campaignIds, $unidadeId, $setorId, $allowedUnidadeIds, $allowedSetorIds),
            'distribuicao_genero' => $this->getDistribuicaoGeneroAgregada($campaignIds, $unidadeId, $setorId, $allowedUnidadeIds, $allowedSetorIds),
            'distribuicao_faixa'  => $this->getDistribuicaoFaixaAgregada($campaignIds, $unidadeId, $setorId, $allowedUnidadeIds, $allowedSetorIds),
            'scores_por_genero'   => $this->getScoresPorGeneroAgregados($campaignIds, $unidadeId, $setorId, $allowedUnidadeIds, $allowedSetorIds),
            'scores_por_faixa'    => $this->getScoresPorFaixaAgregados($campaignIds, $unidadeId, $setorId, $allowedUnidadeIds, $allowedSetorIds),
            'setores_criticos'    => $this->getSetoresCriticosAgregados($campaignIds, $unidadeId, $setorId, $allowedUnidadeIds, $allowedSetorIds),
            'heatmap'             => $this->getHeatmapAgregado($campaignIds, $unidadeId, $setorId, $allowedUnidadeIds, $allowedSetorIds),
            'top_grupos_criticos' => $this->getTopGruposCriticosAgregados($campaignIds, $unidadeId, $setorId, $allowedUnidadeIds, $allowedSetorIds),
        ];
    }

    // ───────────────────────── Scoped query builders ──────────────────────────

    private function baseQuery(array $campaignIds, ?int $unidadeId, ?int $setorId, ?array $allowedUnidades, ?array $allowedSetores): Builder
    {
        $q = DimensionScore::whereIn('campaign_id', $campaignIds);

        if ($unidadeId !== null) {
            $q->where('unidade_id', $unidadeId);
        } elseif ($allowedUnidades !== null) {
            $q->whereIn('unidade_id', $allowedUnidades);
        }

        if ($setorId !== null) {
            $q->where('setor_id', $setorId);
        } elseif ($allowedSetores !== null) {
            $q->whereIn('setor_id', $allowedSetores);
        }

        return $q;
    }

    private function singleBaseQuery(int $campaignId, ?int $unidadeId, ?int $setorId): Builder
    {
        $q = DimensionScore::where('campaign_id', $campaignId);

        if ($unidadeId !== null) {
            $q->where('unidade_id', $unidadeId);
        }

        if ($setorId !== null) {
            $q->where('setor_id', $setorId);
        }

        return $q;
    }

    // ───────────────────────── Single-campaign helpers ────────────────────────

    private function getMetricas(Campaign $campaign, ?int $unidadeId, ?int $setorId): array
    {
        $campaignId = $campaign->id;

        $inviteQuery = SurveyInvite::where('campaign_id', $campaignId);
        $totalConvidados = $inviteQuery->count();
        $totalRespondidos = SurveyInvite::where('campaign_id', $campaignId)
            ->where('resposta_status', 'respondido')->count();

        $taxaAdesao = $totalConvidados > 0
            ? round(($totalRespondidos / $totalConvidados) * 100, 1)
            : 0.0;

        $base = $this->singleBaseQuery($campaignId, $unidadeId, $setorId);

        $igrp = (clone $base)->avg('score_risco');
        $igrp = $igrp ? round($igrp * 100, 1) : 0.0;

        $totalScores = (clone $base)->count();
        $altosRisco = (clone $base)->whereIn('classificacao_risco', ['importante', 'critico'])->count();
        $percentualRiscoAlto = $totalScores > 0
            ? round(($altosRisco / $totalScores) * 100, 1)
            : 0.0;

        return [
            'total_convidados'      => $totalConvidados,
            'total_respondidos'     => $totalRespondidos,
            'taxa_adesao'           => $taxaAdesao,
            'igrp'                  => $igrp,
            'percentual_risco_alto' => $percentualRiscoAlto,
        ];
    }

    private function getScoresPorDimensao(int $campaignId, ?int $unidadeId, ?int $setorId): array
    {
        $rows = $this->singleBaseQuery($campaignId, $unidadeId, $setorId)
            ->select('dimensao', DB::raw('ROUND(AVG(score)::numeric, 2) as score_medio'))
            ->groupBy('dimensao')
            ->get()
            ->keyBy('dimensao');

        return $this->buildDimensoesResult($rows);
    }

    private function getDistribuicaoRisco(int $campaignId, ?int $unidadeId, ?int $setorId): array
    {
        return $this->singleBaseQuery($campaignId, $unidadeId, $setorId)
            ->select('classificacao_risco', DB::raw('COUNT(*) as total'))
            ->groupBy('classificacao_risco')
            ->get()
            ->map(fn ($r) => [
                'classificacao' => $r->classificacao_risco,
                'label'         => HseItQuestionnaire::getLabelClassificacao($r->classificacao_risco),
                'total'         => (int) $r->total,
            ])
            ->values()->toArray();
    }

    private function getDistribuicaoGenero(int $campaignId, ?int $unidadeId, ?int $setorId): array
    {
        return $this->singleBaseQuery($campaignId, $unidadeId, $setorId)
            ->whereNotNull('genero')
            ->select('genero', DB::raw('COUNT(DISTINCT survey_response_id) as total'))
            ->groupBy('genero')
            ->get()
            ->map(fn ($r) => [
                'genero' => $r->genero,
                'label'  => $this->labelGenero($r->genero),
                'total'  => (int) $r->total,
            ])
            ->values()->toArray();
    }

    private function getDistribuicaoFaixa(int $campaignId, ?int $unidadeId, ?int $setorId): array
    {
        return $this->singleBaseQuery($campaignId, $unidadeId, $setorId)
            ->whereNotNull('faixa_etaria')
            ->select('faixa_etaria', DB::raw('COUNT(DISTINCT survey_response_id) as total'))
            ->groupBy('faixa_etaria')
            ->orderBy('faixa_etaria')
            ->get()
            ->map(fn ($r) => ['faixa' => $r->faixa_etaria, 'total' => (int) $r->total])
            ->values()->toArray();
    }

    private function getScoresPorGenero(int $campaignId, ?int $unidadeId, ?int $setorId): array
    {
        $rows = $this->singleBaseQuery($campaignId, $unidadeId, $setorId)
            ->whereNotNull('genero')
            ->select('genero', 'dimensao', DB::raw('ROUND(AVG(score)::numeric, 2) as score_medio'))
            ->groupBy('genero', 'dimensao')
            ->get();

        return $this->buildScoresCrossTab($rows, 'genero', 'generos');
    }

    private function getScoresPorFaixa(int $campaignId, ?int $unidadeId, ?int $setorId): array
    {
        $rows = $this->singleBaseQuery($campaignId, $unidadeId, $setorId)
            ->whereNotNull('faixa_etaria')
            ->select('faixa_etaria', 'dimensao', DB::raw('ROUND(AVG(score)::numeric, 2) as score_medio'))
            ->groupBy('faixa_etaria', 'dimensao')
            ->get();

        return $this->buildScoresCrossTab($rows, 'faixa_etaria', 'faixas');
    }

    private function getSetoresCriticos(int $campaignId, ?int $unidadeId, ?int $setorId): array
    {
        return $this->singleBaseQuery($campaignId, $unidadeId, $setorId)
            ->whereNotNull('setor_id')
            ->select('setor_id', DB::raw('ROUND(AVG(score_risco)::numeric, 3) as risco_medio'), DB::raw('COUNT(*) as total_scores'))
            ->groupBy('setor_id')
            ->orderByDesc('risco_medio')
            ->limit(10)
            ->get()
            ->map(function ($r) {
                $setor = Setor::find($r->setor_id);
                return [
                    'setor_id'    => $r->setor_id,
                    'setor_nome'  => $setor?->nome ?? 'Desconhecido',
                    'risco_medio' => round((float) $r->risco_medio * 100, 1),
                    'total'       => (int) $r->total_scores,
                ];
            })
            ->values()->toArray();
    }

    private function getHeatmapSetorDimensao(int $campaignId, ?int $unidadeId, ?int $setorId): array
    {
        $rows = $this->singleBaseQuery($campaignId, $unidadeId, $setorId)
            ->whereNotNull('setor_id')
            ->select('setor_id', 'dimensao', DB::raw('ROUND(AVG(score_risco)::numeric, 3) as risco_medio'))
            ->groupBy('setor_id', 'dimensao')
            ->get();

        return $this->buildHeatmapData($rows);
    }

    private function getTopGruposCriticos(int $campaignId, ?int $unidadeId, ?int $setorId): array
    {
        return $this->singleBaseQuery($campaignId, $unidadeId, $setorId)
            ->whereIn('classificacao_risco', ['importante', 'critico'])
            ->whereNotNull('setor_id')
            ->select('setor_id', 'dimensao', DB::raw('ROUND(AVG(nr)::numeric, 1) as nr_medio'), DB::raw('COUNT(*) as total'), 'classificacao_risco')
            ->groupBy('setor_id', 'dimensao', 'classificacao_risco')
            ->orderByDesc('nr_medio')
            ->limit(10)
            ->get()
            ->map(function ($r) {
                $setor = Setor::find($r->setor_id);
                return [
                    'setor_nome'     => $setor?->nome ?? 'Desconhecido',
                    'dimensao'       => HseItQuestionnaire::DIMENSOES[$r->dimensao] ?? $r->dimensao,
                    'nr_medio'       => (float) $r->nr_medio,
                    'total'          => (int) $r->total,
                    'classificacao'  => $r->classificacao_risco,
                    'label_classif'  => HseItQuestionnaire::getLabelClassificacao($r->classificacao_risco),
                ];
            })
            ->values()->toArray();
    }

    // ───────────────────────── Aggregate (multi-campaign) helpers ─────────────

    private function getMetricasAgregadas(array $campaignIds, ?int $unidadeId, ?int $setorId, ?array $allowedUnidades, ?array $allowedSetores): array
    {
        $totalConvidados = SurveyInvite::whereIn('campaign_id', $campaignIds)->count();
        $totalRespondidos = SurveyInvite::whereIn('campaign_id', $campaignIds)
            ->where('resposta_status', 'respondido')->count();

        $taxaAdesao = $totalConvidados > 0
            ? round(($totalRespondidos / $totalConvidados) * 100, 1)
            : 0.0;

        $base = $this->baseQuery($campaignIds, $unidadeId, $setorId, $allowedUnidades, $allowedSetores);

        $igrp = (clone $base)->avg('score_risco');
        $igrp = $igrp ? round($igrp * 100, 1) : 0.0;

        $totalScores = (clone $base)->count();
        $altosRisco = (clone $base)->whereIn('classificacao_risco', ['importante', 'critico'])->count();
        $percentualRiscoAlto = $totalScores > 0
            ? round(($altosRisco / $totalScores) * 100, 1)
            : 0.0;

        return [
            'total_convidados'      => $totalConvidados,
            'total_respondidos'     => $totalRespondidos,
            'taxa_adesao'           => $taxaAdesao,
            'igrp'                  => $igrp,
            'percentual_risco_alto' => $percentualRiscoAlto,
        ];
    }

    private function getScoresPorDimensaoAgregados(array $campaignIds, ?int $unidadeId, ?int $setorId, ?array $allowedUnidades, ?array $allowedSetores): array
    {
        $rows = $this->baseQuery($campaignIds, $unidadeId, $setorId, $allowedUnidades, $allowedSetores)
            ->select('dimensao', DB::raw('ROUND(AVG(score)::numeric, 2) as score_medio'))
            ->groupBy('dimensao')
            ->get()
            ->keyBy('dimensao');

        return $this->buildDimensoesResult($rows);
    }

    private function getDistribuicaoRiscoAgregada(array $campaignIds, ?int $unidadeId, ?int $setorId, ?array $allowedUnidades, ?array $allowedSetores): array
    {
        return $this->baseQuery($campaignIds, $unidadeId, $setorId, $allowedUnidades, $allowedSetores)
            ->select('classificacao_risco', DB::raw('COUNT(*) as total'))
            ->groupBy('classificacao_risco')
            ->get()
            ->map(fn ($r) => [
                'classificacao' => $r->classificacao_risco,
                'label'         => HseItQuestionnaire::getLabelClassificacao($r->classificacao_risco),
                'total'         => (int) $r->total,
            ])
            ->values()->toArray();
    }

    private function getDistribuicaoGeneroAgregada(array $campaignIds, ?int $unidadeId, ?int $setorId, ?array $allowedUnidades, ?array $allowedSetores): array
    {
        return $this->baseQuery($campaignIds, $unidadeId, $setorId, $allowedUnidades, $allowedSetores)
            ->whereNotNull('genero')
            ->select('genero', DB::raw('COUNT(DISTINCT survey_response_id) as total'))
            ->groupBy('genero')
            ->get()
            ->map(fn ($r) => [
                'genero' => $r->genero,
                'label'  => $this->labelGenero($r->genero),
                'total'  => (int) $r->total,
            ])
            ->values()->toArray();
    }

    private function getDistribuicaoFaixaAgregada(array $campaignIds, ?int $unidadeId, ?int $setorId, ?array $allowedUnidades, ?array $allowedSetores): array
    {
        return $this->baseQuery($campaignIds, $unidadeId, $setorId, $allowedUnidades, $allowedSetores)
            ->whereNotNull('faixa_etaria')
            ->select('faixa_etaria', DB::raw('COUNT(DISTINCT survey_response_id) as total'))
            ->groupBy('faixa_etaria')
            ->orderBy('faixa_etaria')
            ->get()
            ->map(fn ($r) => ['faixa' => $r->faixa_etaria, 'total' => (int) $r->total])
            ->values()->toArray();
    }

    private function getScoresPorGeneroAgregados(array $campaignIds, ?int $unidadeId, ?int $setorId, ?array $allowedUnidades, ?array $allowedSetores): array
    {
        $rows = $this->baseQuery($campaignIds, $unidadeId, $setorId, $allowedUnidades, $allowedSetores)
            ->whereNotNull('genero')
            ->select('genero', 'dimensao', DB::raw('ROUND(AVG(score)::numeric, 2) as score_medio'))
            ->groupBy('genero', 'dimensao')
            ->get();

        return $this->buildScoresCrossTab($rows, 'genero', 'generos');
    }

    private function getScoresPorFaixaAgregados(array $campaignIds, ?int $unidadeId, ?int $setorId, ?array $allowedUnidades, ?array $allowedSetores): array
    {
        $rows = $this->baseQuery($campaignIds, $unidadeId, $setorId, $allowedUnidades, $allowedSetores)
            ->whereNotNull('faixa_etaria')
            ->select('faixa_etaria', 'dimensao', DB::raw('ROUND(AVG(score)::numeric, 2) as score_medio'))
            ->groupBy('faixa_etaria', 'dimensao')
            ->get();

        return $this->buildScoresCrossTab($rows, 'faixa_etaria', 'faixas');
    }

    private function getSetoresCriticosAgregados(array $campaignIds, ?int $unidadeId, ?int $setorId, ?array $allowedUnidades, ?array $allowedSetores): array
    {
        return $this->baseQuery($campaignIds, $unidadeId, $setorId, $allowedUnidades, $allowedSetores)
            ->whereNotNull('setor_id')
            ->select('setor_id', DB::raw('ROUND(AVG(score_risco)::numeric, 3) as risco_medio'), DB::raw('COUNT(*) as total_scores'))
            ->groupBy('setor_id')
            ->orderByDesc('risco_medio')
            ->limit(10)
            ->get()
            ->map(function ($r) {
                $setor = Setor::find($r->setor_id);
                return [
                    'setor_id'    => $r->setor_id,
                    'setor_nome'  => $setor?->nome ?? 'Desconhecido',
                    'risco_medio' => round((float) $r->risco_medio * 100, 1),
                    'total'       => (int) $r->total_scores,
                ];
            })
            ->values()->toArray();
    }

    private function getHeatmapAgregado(array $campaignIds, ?int $unidadeId, ?int $setorId, ?array $allowedUnidades, ?array $allowedSetores): array
    {
        $rows = $this->baseQuery($campaignIds, $unidadeId, $setorId, $allowedUnidades, $allowedSetores)
            ->whereNotNull('setor_id')
            ->select('setor_id', 'dimensao', DB::raw('ROUND(AVG(score_risco)::numeric, 3) as risco_medio'))
            ->groupBy('setor_id', 'dimensao')
            ->get();

        return $this->buildHeatmapData($rows);
    }

    private function getTopGruposCriticosAgregados(array $campaignIds, ?int $unidadeId, ?int $setorId, ?array $allowedUnidades, ?array $allowedSetores): array
    {
        return $this->baseQuery($campaignIds, $unidadeId, $setorId, $allowedUnidades, $allowedSetores)
            ->whereIn('classificacao_risco', ['importante', 'critico'])
            ->whereNotNull('setor_id')
            ->select('setor_id', 'dimensao', DB::raw('ROUND(AVG(nr)::numeric, 1) as nr_medio'), DB::raw('COUNT(*) as total'), 'classificacao_risco')
            ->groupBy('setor_id', 'dimensao', 'classificacao_risco')
            ->orderByDesc('nr_medio')
            ->limit(10)
            ->get()
            ->map(function ($r) {
                $setor = Setor::find($r->setor_id);
                return [
                    'setor_nome'    => $setor?->nome ?? 'Desconhecido',
                    'dimensao'      => HseItQuestionnaire::DIMENSOES[$r->dimensao] ?? $r->dimensao,
                    'nr_medio'      => (float) $r->nr_medio,
                    'total'         => (int) $r->total,
                    'classificacao' => $r->classificacao_risco,
                    'label_classif' => HseItQuestionnaire::getLabelClassificacao($r->classificacao_risco),
                ];
            })
            ->values()->toArray();
    }

    // ───────────────────────── Shared helpers ────────────────────────────────

    private function buildDimensoesResult($rows): array
    {
        $result = [];
        foreach (HseItQuestionnaire::DIMENSOES as $key => $label) {
            $result[] = [
                'dimensao' => $key,
                'label'    => $label,
                'score'    => $rows->has($key) ? (float) $rows[$key]->score_medio : 0.0,
                'negativa' => HseItQuestionnaire::isDimensaoNegativa($key),
            ];
        }
        return $result;
    }

    private function buildScoresCrossTab($rows, string $groupField, string $resultKey): array
    {
        $groups = $rows->pluck($groupField)->unique()->sort()->values()->toArray();
        $result = [];

        foreach (array_keys(HseItQuestionnaire::DIMENSOES) as $dimensao) {
            $entry = ['dimensao' => $dimensao, 'label' => HseItQuestionnaire::DIMENSOES[$dimensao]];

            foreach ($groups as $group) {
                $match = $rows->first(fn ($r) => $r->$groupField === $group && $r->dimensao === $dimensao);
                $entry[$group] = $match ? (float) $match->score_medio : null;
            }

            $result[] = $entry;
        }

        return [$resultKey => $groups, 'dados' => $result];
    }

    private function buildHeatmapData($rows): array
    {
        $setorIds = $rows->pluck('setor_id')->unique()->toArray();
        $dimensoes = array_keys(HseItQuestionnaire::DIMENSOES);
        $setores = [];
        $matrix = [];

        foreach ($setorIds as $setorId) {
            $setor = Setor::find($setorId);
            $setores[] = ['id' => $setorId, 'nome' => $setor?->nome ?? 'Desconhecido'];
        }

        foreach ($dimensoes as $dim) {
            $row = ['dimensao' => $dim, 'label' => HseItQuestionnaire::DIMENSOES[$dim]];
            foreach ($setorIds as $setorId) {
                $match = $rows->first(fn ($r) => $r->setor_id === $setorId && $r->dimensao === $dim);
                $row['setor_' . $setorId] = $match ? round((float) $match->risco_medio * 100, 1) : null;
            }
            $matrix[] = $row;
        }

        return ['setores' => $setores, 'dimensoes' => $dimensoes, 'matrix' => $matrix];
    }

    private function emptyDashboard(): array
    {
        return [
            'metricas'            => ['total_convidados' => 0, 'total_respondidos' => 0, 'taxa_adesao' => 0, 'igrp' => 0, 'percentual_risco_alto' => 0],
            'scores_dimensoes'    => [],
            'distribuicao_risco'  => [],
            'distribuicao_genero' => [],
            'distribuicao_faixa'  => [],
            'scores_por_genero'   => ['generos' => [], 'dados' => []],
            'scores_por_faixa'    => ['faixas' => [], 'dados' => []],
            'setores_criticos'    => [],
            'heatmap'             => ['setores' => [], 'dimensoes' => [], 'matrix' => []],
            'top_grupos_criticos' => [],
        ];
    }

    private function labelGenero(string $genero): string
    {
        return match ($genero) {
            'masculino'     => 'Masculino',
            'feminino'      => 'Feminino',
            'outro'         => 'Outro',
            'nao_informado' => 'Prefiro não dizer',
            default         => $genero,
        };
    }
}
