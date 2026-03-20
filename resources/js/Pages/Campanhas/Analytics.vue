<template>
    <AppLayout :title="`Analytics – ${campaign.nome}`" subtitle="Dashboard analítico da campanha">
        <div class="space-y-6">

            <!-- Filter bar -->
            <div class="card p-4">
                <form @submit.prevent="aplicarFiltros" class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Unidade</label>
                        <select v-model="filtros.unidade_id" class="input-field text-sm" @change="filtros.setor_id = null">
                            <option :value="null">Todas as unidades</option>
                            <option v-for="u in unidades" :key="u.id" :value="u.id">{{ u.nome }}</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Setor</label>
                        <select v-model="filtros.setor_id" class="input-field text-sm">
                            <option :value="null">Todos os setores</option>
                            <option v-for="s in setoresFiltrados" :key="s.id" :value="s.id">{{ s.nome }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary text-sm px-5 py-2">Filtrar</button>
                    <button v-if="temFiltroAtivo" type="button" @click="limparFiltros" class="btn-secondary text-sm px-4 py-2">Limpar</button>
                </form>
            </div>

            <!-- Metrics cards -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="card p-4">
                    <p class="text-xs font-medium text-gray-500 mb-1">Convidados</p>
                    <p class="text-2xl font-bold text-gray-900">{{ metricas.total_convidados }}</p>
                </div>
                <div class="card p-4">
                    <p class="text-xs font-medium text-gray-500 mb-1">Respondidos</p>
                    <p class="text-2xl font-bold text-green-600">{{ metricas.total_respondidos }}</p>
                </div>
                <div class="card p-4">
                    <p class="text-xs font-medium text-gray-500 mb-1">Taxa de Adesão</p>
                    <p class="text-2xl font-bold text-blue-600">{{ metricas.taxa_adesao }}%</p>
                </div>
                <div class="card p-4">
                    <p class="text-xs font-medium text-gray-500 mb-1">IGRP</p>
                    <p class="text-2xl font-bold text-purple-600">{{ metricas.igrp }}%</p>
                </div>
                <div class="card p-4">
                    <p class="text-xs font-medium text-gray-500 mb-1">Risco Alto</p>
                    <p class="text-2xl font-bold text-red-600">{{ metricas.percentual_risco_alto }}%</p>
                </div>
            </div>

            <!-- Row 1: Radar + Risk donut -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Scores por Dimensão (Radar)</h3>
                    <div class="h-64 flex items-center justify-center">
                        <Radar v-if="hasData" :data="radarChartData" :options="radarOptions" />
                        <EmptyChart v-else />
                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Distribuição de Risco</h3>
                    <div class="h-64 flex items-center justify-center">
                        <Doughnut v-if="hasRiskData" :data="donutRiscoData" :options="donutOptions" />
                        <EmptyChart v-else />
                    </div>
                </div>
            </div>

            <!-- Row 2: Setores críticos + gender pie -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Setores Críticos</h3>
                    <div class="h-64">
                        <Bar v-if="hasSetoresData" :data="setoresBarData" :options="barHorizSetoresOptions" />
                        <EmptyChart v-else />
                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Distribuição por Gênero</h3>
                    <div class="h-64 flex items-center justify-center">
                        <Pie v-if="hasGenderData" :data="generoData" :options="pieOptions" />
                        <EmptyChart v-else />
                    </div>
                </div>
            </div>

            <!-- Row 3: Horizontal bars (dim) + age bar -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Score Médio por Dimensão</h3>
                    <div class="h-64">
                        <Bar v-if="hasData" :data="barDimensoesData" :options="barHorizontalOptions" />
                        <EmptyChart v-else />
                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Distribuição por Faixa Etária</h3>
                    <div class="h-64">
                        <Bar v-if="hasFaixaData" :data="faixaData" :options="barOptions" />
                        <EmptyChart v-else />
                    </div>
                </div>
            </div>

            <!-- Row 4: Line by gender + line by age -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Scores por Gênero</h3>
                    <div class="h-64">
                        <Line v-if="hasGenderLineData" :data="generoLineData" :options="lineOptions" />
                        <EmptyChart v-else />
                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Scores por Faixa Etária</h3>
                    <div class="h-64">
                        <Line v-if="hasFaixaLineData" :data="faixaLineData" :options="lineOptions" />
                        <EmptyChart v-else />
                    </div>
                </div>
            </div>

            <!-- Heatmap -->
            <div class="card p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Heatmap: Setor × Dimensão</h3>
                <div v-if="hasHeatmapData" class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr>
                                <th class="text-left py-2 pr-4 font-semibold text-gray-600 min-w-[140px]">Dimensão</th>
                                <th v-for="setor in heatmap.setores" :key="setor.id" class="text-center py-2 px-2 font-semibold text-gray-600 min-w-[90px]">
                                    {{ setor.nome }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in heatmap.matrix" :key="row.dimensao" class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-medium text-gray-700">{{ row.label }}</td>
                                <td v-for="setor in heatmap.setores" :key="setor.id" class="text-center py-2 px-2 font-semibold rounded" :style="heatmapStyle(row['setor_' + setor.id])">
                                    {{ row['setor_' + setor.id] != null ? row['setor_' + setor.id] + '%' : '—' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <EmptyChart v-else />
            </div>

            <!-- Top grupos críticos -->
            <div class="card p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Top Grupos Críticos</h3>
                <div v-if="hasTopGrupos" class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 font-semibold text-gray-600">#</th>
                                <th class="text-left py-2 font-semibold text-gray-600">Setor</th>
                                <th class="text-left py-2 font-semibold text-gray-600">Dimensão</th>
                                <th class="text-right py-2 font-semibold text-gray-600">NR Médio</th>
                                <th class="text-center py-2 font-semibold text-gray-600">Classificação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(g, i) in topGrupos" :key="i" class="border-t border-gray-100">
                                <td class="py-2 text-gray-400">{{ i+1 }}</td>
                                <td class="py-2 text-gray-800 font-medium">{{ g.setor_nome }}</td>
                                <td class="py-2 text-gray-600">{{ g.dimensao }}</td>
                                <td class="py-2 text-right font-semibold" :class="nrColor(g.nr_medio)">{{ g.nr_medio.toFixed(1) }}</td>
                                <td class="py-2 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold" :class="badgeClass(g.classificacao)">{{ g.label_classif }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <EmptyChart v-else />
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    Chart as ChartJS,
    RadialLinearScale, PointElement, LineElement, Filler,
    Tooltip, Legend, CategoryScale, LinearScale, BarElement, ArcElement,
} from 'chart.js';
import { Radar, Doughnut, Bar, Pie, Line } from 'vue-chartjs';

ChartJS.register(
    RadialLinearScale, PointElement, LineElement, Filler,
    Tooltip, Legend, CategoryScale, LinearScale, BarElement, ArcElement
);

const props = defineProps({
    campaign:  Object,
    analytics: Object,
    unidades:  Array,
    setores:   Array,
    filtros:   Object,
});

// ─── Filters ─────────────────────────────────────────────────────────────────
const filtros = ref({ unidade_id: props.filtros?.unidade_id ?? null, setor_id: props.filtros?.setor_id ?? null });
const setoresFiltrados = computed(() => {
    if (!filtros.value.unidade_id) return props.setores ?? [];
    return (props.setores ?? []).filter(s => s.unidade_id === filtros.value.unidade_id);
});
const temFiltroAtivo = computed(() => filtros.value.unidade_id || filtros.value.setor_id);

const aplicarFiltros = () => {
    router.get(route('tenant.campanhas.analytics', props.campaign.id), {
        unidade_id: filtros.value.unidade_id || undefined,
        setor_id:   filtros.value.setor_id || undefined,
    }, { preserveScroll: true });
};
const limparFiltros = () => {
    filtros.value = { unidade_id: null, setor_id: null };
    router.get(route('tenant.campanhas.analytics', props.campaign.id), {}, { preserveScroll: true });
};

// ─── Shortcuts ────────────────────────────────────────────────────────────────
const metricas    = computed(() => props.analytics.metricas);
const scoresDim   = computed(() => props.analytics.scores_dimensoes ?? []);
const distRisco   = computed(() => props.analytics.distribuicao_risco ?? []);
const distGenero  = computed(() => props.analytics.distribuicao_genero ?? []);
const distFaixa   = computed(() => props.analytics.distribuicao_faixa ?? []);
const scoresGenero = computed(() => props.analytics.scores_por_genero ?? { generos: [], dados: [] });
const scoresFaixa  = computed(() => props.analytics.scores_por_faixa ?? { faixas: [], dados: [] });
const setoresCrit  = computed(() => props.analytics.setores_criticos ?? []);
const heatmap      = computed(() => props.analytics.heatmap ?? { setores: [], matrix: [] });
const topGrupos    = computed(() => props.analytics.top_grupos_criticos ?? []);

const hasData         = computed(() => scoresDim.value.some(d => d.score > 0));
const hasRiskData     = computed(() => distRisco.value.length > 0);
const hasGenderData   = computed(() => distGenero.value.length > 0);
const hasFaixaData    = computed(() => distFaixa.value.length > 0);
const hasGenderLineData = computed(() => scoresGenero.value.generos?.length > 0);
const hasFaixaLineData  = computed(() => scoresFaixa.value.faixas?.length > 0);
const hasSetoresData    = computed(() => setoresCrit.value.length > 0);
const hasHeatmapData    = computed(() => heatmap.value.setores?.length > 0);
const hasTopGrupos      = computed(() => topGrupos.value.length > 0);

const COLORS = ['#6366f1', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'];
const RISK_COLORS = { aceitavel: '#22c55e', moderado: '#f59e0b', importante: '#f97316', critico: '#ef4444' };

const radarChartData = computed(() => ({
    labels: scoresDim.value.map(d => d.label),
    datasets: [{ label: 'Score médio', data: scoresDim.value.map(d => d.score), backgroundColor: 'rgba(99,102,241,0.2)', borderColor: '#6366f1', pointBackgroundColor: '#6366f1' }],
}));
const radarOptions = { responsive: true, maintainAspectRatio: false, scales: { r: { min: 0, max: 4, ticks: { stepSize: 1, font: { size: 10 } } } }, plugins: { legend: { display: false } } };

const donutRiscoData = computed(() => ({
    labels: distRisco.value.map(r => r.label),
    datasets: [{ data: distRisco.value.map(r => r.total), backgroundColor: distRisco.value.map(r => RISK_COLORS[r.classificacao] ?? '#94a3b8') }],
}));
const donutOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } };

const setoresBarData = computed(() => ({
    labels: setoresCrit.value.map(s => s.setor_nome),
    datasets: [{ label: 'Risco (%)', data: setoresCrit.value.map(s => s.risco_medio), backgroundColor: setoresCrit.value.map(s => s.risco_medio >= 75 ? '#ef4444' : s.risco_medio >= 50 ? '#f97316' : s.risco_medio >= 25 ? '#f59e0b' : '#22c55e') }],
}));
const barHorizSetoresOptions = { indexAxis: 'y', responsive: true, maintainAspectRatio: false, scales: { x: { min: 0, max: 100 } }, plugins: { legend: { display: false } } };

const barDimensoesData = computed(() => ({
    labels: scoresDim.value.map(d => d.label),
    datasets: [{ label: 'Score', data: scoresDim.value.map(d => d.score), backgroundColor: scoresDim.value.map(d => d.negativa ? '#ef4444' : '#6366f1') }],
}));
const barHorizontalOptions = { indexAxis: 'y', responsive: true, maintainAspectRatio: false, scales: { x: { min: 0, max: 4 } }, plugins: { legend: { display: false } } };
const barOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } };

const generoData = computed(() => ({
    labels: distGenero.value.map(g => g.label),
    datasets: [{ data: distGenero.value.map(g => g.total), backgroundColor: COLORS }],
}));
const pieOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } };

const faixaData = computed(() => ({
    labels: distFaixa.value.map(f => f.faixa),
    datasets: [{ label: 'Respondentes', data: distFaixa.value.map(f => f.total), backgroundColor: '#6366f1' }],
}));

const generoLineData = computed(() => ({
    labels: scoresGenero.value.dados?.map(d => d.label) ?? [],
    datasets: (scoresGenero.value.generos ?? []).map((g, i) => ({
        label: g,
        data: (scoresGenero.value.dados ?? []).map(d => d[g] ?? null),
        borderColor: COLORS[i % COLORS.length],
        backgroundColor: COLORS[i % COLORS.length] + '33',
        tension: 0.3,
    })),
}));

const faixaLineData = computed(() => ({
    labels: scoresFaixa.value.dados?.map(d => d.label) ?? [],
    datasets: (scoresFaixa.value.faixas ?? []).map((f, i) => ({
        label: f,
        data: (scoresFaixa.value.dados ?? []).map(d => d[f] ?? null),
        borderColor: COLORS[i % COLORS.length],
        backgroundColor: COLORS[i % COLORS.length] + '33',
        tension: 0.3,
    })),
}));
const lineOptions = { responsive: true, maintainAspectRatio: false, scales: { y: { min: 0, max: 4 } }, plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } } };

const heatmapStyle = (value) => {
    if (value == null) return { background: '#f8fafc', color: '#94a3b8' };
    const v = Math.min(100, Math.max(0, value));
    const r = Math.round(255 * (v / 100));
    const g = Math.round(255 * (1 - v / 100));
    const alpha = 0.15 + (v / 100) * 0.65;
    return { background: `rgba(${r}, ${g}, 60, ${alpha})`, color: v > 60 ? '#fff' : '#1e293b' };
};

const badgeClass = (classif) => ({ aceitavel: 'bg-green-100 text-green-700', moderado: 'bg-yellow-100 text-yellow-700', importante: 'bg-orange-100 text-orange-700', critico: 'bg-red-100 text-red-700' }[classif] ?? 'bg-gray-100 text-gray-600');
const nrColor = (nr) => nr > 9 ? 'text-red-600' : nr > 6 ? 'text-orange-500' : nr > 3 ? 'text-yellow-500' : 'text-green-600';

const EmptyChart = {
    template: `<div class="flex flex-col items-center justify-center h-full text-gray-300 gap-2 py-4">
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <p class="text-sm">Sem dados suficientes</p>
    </div>`,
};
</script>
