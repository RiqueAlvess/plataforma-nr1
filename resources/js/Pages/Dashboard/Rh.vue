<template>
    <AppLayout title="Dashboard RH" subtitle="Visão consolidada de todas as campanhas">
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
                    <button type="submit" class="btn-primary text-sm px-5 py-2">
                        Filtrar
                    </button>
                    <button v-if="temFiltroAtivo" type="button" @click="limparFiltros" class="btn-secondary text-sm px-4 py-2">
                        Limpar
                    </button>
                </form>
            </div>

            <!-- Metric cards -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <MetricCard
                    label="Convidados"
                    :value="metricas.total_convidados"
                    color="indigo"
                    icon="users"
                />
                <MetricCard
                    label="Respondidos"
                    :value="metricas.total_respondidos"
                    color="green"
                    icon="check-circle"
                />
                <MetricCard
                    label="Taxa de Adesão"
                    :value="metricas.taxa_adesao + '%'"
                    color="blue"
                    icon="trending-up"
                />
                <MetricCard
                    label="IGRP"
                    :value="metricas.igrp + '%'"
                    color="purple"
                    icon="activity"
                />
                <MetricCard
                    label="Risco Alto"
                    :value="metricas.percentual_risco_alto + '%'"
                    color="red"
                    icon="alert-triangle"
                />
            </div>

            <!-- Row 1: Radar + Donut -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Scores por Dimensão</h3>
                    <p class="text-xs text-gray-400 mb-4">Média de risco por dimensão HSE-IT</p>
                    <div class="h-72 flex items-center justify-center">
                        <Radar v-if="hasData" :data="radarData" :options="radarOptions" />
                        <EmptyState v-else />
                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Distribuição de Risco</h3>
                    <p class="text-xs text-gray-400 mb-4">Classificação NR por nível de risco</p>
                    <div class="h-72 flex items-center justify-center">
                        <Doughnut v-if="hasRiskData" :data="donutData" :options="donutOptions" />
                        <EmptyState v-else />
                    </div>
                </div>
            </div>

            <!-- Row 2: Setores críticos bar + Gender pie -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Setores Críticos</h3>
                    <p class="text-xs text-gray-400 mb-4">Top 10 setores por risco médio</p>
                    <div class="h-72">
                        <Bar v-if="hasSetoresData" :data="setoresBarData" :options="barHorizOptions" />
                        <EmptyState v-else />
                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Distribuição por Gênero</h3>
                    <p class="text-xs text-gray-400 mb-4">Respondentes por gênero</p>
                    <div class="h-72 flex items-center justify-center">
                        <Pie v-if="hasGeneroData" :data="generoData" :options="pieOptions" />
                        <EmptyState v-else />
                    </div>
                </div>
            </div>

            <!-- Row 3: Dimension scores bar + Age bar -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Score Médio por Dimensão</h3>
                    <p class="text-xs text-gray-400 mb-4">Dimensões negativas destacadas em vermelho</p>
                    <div class="h-72">
                        <Bar v-if="hasData" :data="dimensoesBarData" :options="barHorizDimOptions" />
                        <EmptyState v-else />
                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Distribuição por Faixa Etária</h3>
                    <p class="text-xs text-gray-400 mb-4">Respondentes por faixa etária</p>
                    <div class="h-72">
                        <Bar v-if="hasFaixaData" :data="faixaBarData" :options="barVertOptions" />
                        <EmptyState v-else />
                    </div>
                </div>
            </div>

            <!-- Row 4: Line by gender + Line by age -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Scores por Gênero</h3>
                    <p class="text-xs text-gray-400 mb-4">Comparativo de scores entre gêneros por dimensão</p>
                    <div class="h-72">
                        <Line v-if="hasGeneroLineData" :data="generoLineData" :options="lineOptions" />
                        <EmptyState v-else />
                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Scores por Faixa Etária</h3>
                    <p class="text-xs text-gray-400 mb-4">Comparativo de scores entre faixas etárias por dimensão</p>
                    <div class="h-72">
                        <Line v-if="hasFaixaLineData" :data="faixaLineData" :options="lineOptions" />
                        <EmptyState v-else />
                    </div>
                </div>
            </div>

            <!-- Heatmap: Setor × Dimensão -->
            <div class="card p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">Heatmap: Setor × Dimensão</h3>
                <p class="text-xs text-gray-400 mb-4">Índice de risco (%) por setor e dimensão. Vermelho = maior risco.</p>
                <div v-if="hasHeatmapData" class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr>
                                <th class="text-left py-2 pr-4 font-semibold text-gray-600 min-w-[140px]">Dimensão</th>
                                <th
                                    v-for="setor in heatmap.setores"
                                    :key="setor.id"
                                    class="text-center py-2 px-2 font-semibold text-gray-600 min-w-[90px]"
                                >
                                    {{ setor.nome }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in heatmap.matrix" :key="row.dimensao" class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-medium text-gray-700">{{ row.label }}</td>
                                <td
                                    v-for="setor in heatmap.setores"
                                    :key="setor.id"
                                    class="text-center py-2 px-2 font-semibold rounded"
                                    :style="heatmapCellStyle(row['setor_' + setor.id])"
                                >
                                    {{ row['setor_' + setor.id] != null ? row['setor_' + setor.id] + '%' : '—' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <EmptyState v-else />
            </div>

            <!-- Top grupos críticos -->
            <div class="card p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">Top Grupos Críticos</h3>
                <p class="text-xs text-gray-400 mb-4">Combinações setor + dimensão com maior NR médio</p>
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
                            <tr
                                v-for="(grupo, i) in topGrupos"
                                :key="i"
                                class="border-t border-gray-100 hover:bg-gray-50"
                            >
                                <td class="py-2 text-gray-400 font-mono">{{ i + 1 }}</td>
                                <td class="py-2 text-gray-800 font-medium">{{ grupo.setor_nome }}</td>
                                <td class="py-2 text-gray-600">{{ grupo.dimensao }}</td>
                                <td class="py-2 text-right font-semibold" :class="nrColor(grupo.nr_medio)">
                                    {{ grupo.nr_medio.toFixed(1) }}
                                </td>
                                <td class="py-2 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold" :class="badgeClass(grupo.classificacao)">
                                        {{ grupo.label_classif }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <EmptyState v-else />
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

// ─── Props ──────────────────────────────────────────────────────────────────
const props = defineProps({
    analytics:       Object,
    unidades:        Array,
    setores:         Array,
    filtros:         Object,
    ultima_campanha: Object,
});

// ─── Filter state ────────────────────────────────────────────────────────────
const filtros = ref({
    unidade_id: props.filtros?.unidade_id ?? null,
    setor_id:   props.filtros?.setor_id ?? null,
});

const setoresFiltrados = computed(() => {
    if (!filtros.value.unidade_id) return props.setores ?? [];
    return (props.setores ?? []).filter(s => s.unidade_id === filtros.value.unidade_id);
});

const temFiltroAtivo = computed(() => filtros.value.unidade_id || filtros.value.setor_id);

const aplicarFiltros = () => {
    router.get(route('tenant.dashboard'), {
        unidade_id: filtros.value.unidade_id || undefined,
        setor_id:   filtros.value.setor_id || undefined,
    }, { preserveScroll: true, preserveState: false });
};

const limparFiltros = () => {
    filtros.value = { unidade_id: null, setor_id: null };
    router.get(route('tenant.dashboard'), {}, { preserveScroll: true });
};

// ─── Data shortcuts ──────────────────────────────────────────────────────────
const metricas    = computed(() => props.analytics?.metricas ?? {});
const scoresDim   = computed(() => props.analytics?.scores_dimensoes ?? []);
const distRisco   = computed(() => props.analytics?.distribuicao_risco ?? []);
const distGenero  = computed(() => props.analytics?.distribuicao_genero ?? []);
const distFaixa   = computed(() => props.analytics?.distribuicao_faixa ?? []);
const generoLine  = computed(() => props.analytics?.scores_por_genero ?? { generos: [], dados: [] });
const faixaLine   = computed(() => props.analytics?.scores_por_faixa ?? { faixas: [], dados: [] });
const setoresCrit = computed(() => props.analytics?.setores_criticos ?? []);
const heatmap     = computed(() => props.analytics?.heatmap ?? { setores: [], matrix: [] });
const topGrupos   = computed(() => props.analytics?.top_grupos_criticos ?? []);

// ─── Guards ──────────────────────────────────────────────────────────────────
const hasData         = computed(() => scoresDim.value.some(d => d.score > 0));
const hasRiskData     = computed(() => distRisco.value.length > 0);
const hasGeneroData   = computed(() => distGenero.value.length > 0);
const hasFaixaData    = computed(() => distFaixa.value.length > 0);
const hasGeneroLineData = computed(() => generoLine.value.generos?.length > 0);
const hasFaixaLineData  = computed(() => faixaLine.value.faixas?.length > 0);
const hasSetoresData  = computed(() => setoresCrit.value.length > 0);
const hasHeatmapData  = computed(() => heatmap.value.setores?.length > 0);
const hasTopGrupos    = computed(() => topGrupos.value.length > 0);

// ─── Palette ─────────────────────────────────────────────────────────────────
const COLORS = ['#6366f1', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#f97316'];
const RISK_COLORS = { aceitavel: '#22c55e', moderado: '#f59e0b', importante: '#f97316', critico: '#ef4444' };

// ─── Radar ───────────────────────────────────────────────────────────────────
const radarData = computed(() => ({
    labels: scoresDim.value.map(d => d.label),
    datasets: [{
        label: 'Score médio',
        data: scoresDim.value.map(d => d.score),
        backgroundColor: 'rgba(99,102,241,0.2)',
        borderColor: '#6366f1',
        pointBackgroundColor: '#6366f1',
    }],
}));

const radarOptions = {
    responsive: true, maintainAspectRatio: false,
    scales: { r: { min: 0, max: 4, ticks: { stepSize: 1, font: { size: 10 } } } },
    plugins: { legend: { display: false } },
};

// ─── Donut ───────────────────────────────────────────────────────────────────
const donutData = computed(() => ({
    labels: distRisco.value.map(r => r.label),
    datasets: [{
        data: distRisco.value.map(r => r.total),
        backgroundColor: distRisco.value.map(r => RISK_COLORS[r.classificacao] ?? '#94a3b8'),
    }],
}));

const donutOptions = {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { position: 'right', labels: { font: { size: 11 } } } },
};

// ─── Setores críticos bar ────────────────────────────────────────────────────
const setoresBarData = computed(() => ({
    labels: setoresCrit.value.map(s => s.setor_nome),
    datasets: [{
        label: 'Risco Médio (%)',
        data: setoresCrit.value.map(s => s.risco_medio),
        backgroundColor: setoresCrit.value.map(s => {
            if (s.risco_medio >= 75) return '#ef4444';
            if (s.risco_medio >= 50) return '#f97316';
            if (s.risco_medio >= 25) return '#f59e0b';
            return '#22c55e';
        }),
    }],
}));

// ─── Dimension bar ───────────────────────────────────────────────────────────
const dimensoesBarData = computed(() => ({
    labels: scoresDim.value.map(d => d.label),
    datasets: [{
        label: 'Score',
        data: scoresDim.value.map(d => d.score),
        backgroundColor: scoresDim.value.map(d => d.negativa ? '#ef4444' : '#6366f1'),
    }],
}));

const barHorizOptions = {
    indexAxis: 'y', responsive: true, maintainAspectRatio: false,
    scales: { x: { min: 0, max: 100, ticks: { font: { size: 10 } } }, y: { ticks: { font: { size: 10 }, maxRotation: 0 } } },
    plugins: { legend: { display: false } },
};

const barHorizDimOptions = {
    indexAxis: 'y', responsive: true, maintainAspectRatio: false,
    scales: { x: { min: 0, max: 4, ticks: { font: { size: 10 } } }, y: { ticks: { font: { size: 10 } } } },
    plugins: { legend: { display: false } },
};

// ─── Gender pie ──────────────────────────────────────────────────────────────
const generoData = computed(() => ({
    labels: distGenero.value.map(g => g.label),
    datasets: [{ data: distGenero.value.map(g => g.total), backgroundColor: COLORS }],
}));

const pieOptions = {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { position: 'right', labels: { font: { size: 11 } } } },
};

// ─── Age bar ─────────────────────────────────────────────────────────────────
const faixaBarData = computed(() => ({
    labels: distFaixa.value.map(f => f.faixa),
    datasets: [{ label: 'Respondentes', data: distFaixa.value.map(f => f.total), backgroundColor: '#6366f1' }],
}));

const barVertOptions = {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true }, x: { ticks: { font: { size: 10 } } } },
};

// ─── Line gender ─────────────────────────────────────────────────────────────
const generoLineData = computed(() => ({
    labels: generoLine.value.dados?.map(d => d.label) ?? [],
    datasets: (generoLine.value.generos ?? []).map((g, i) => ({
        label: g,
        data: (generoLine.value.dados ?? []).map(d => d[g] ?? null),
        borderColor: COLORS[i % COLORS.length],
        backgroundColor: COLORS[i % COLORS.length] + '33',
        tension: 0.3,
    })),
}));

// ─── Line age ────────────────────────────────────────────────────────────────
const faixaLineData = computed(() => ({
    labels: faixaLine.value.dados?.map(d => d.label) ?? [],
    datasets: (faixaLine.value.faixas ?? []).map((f, i) => ({
        label: f,
        data: (faixaLine.value.dados ?? []).map(d => d[f] ?? null),
        borderColor: COLORS[i % COLORS.length],
        backgroundColor: COLORS[i % COLORS.length] + '33',
        tension: 0.3,
    })),
}));

const lineOptions = {
    responsive: true, maintainAspectRatio: false,
    scales: { y: { min: 0, max: 4 }, x: { ticks: { font: { size: 10 } } } },
    plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } },
};

// ─── Heatmap helpers ─────────────────────────────────────────────────────────
const heatmapCellStyle = (value) => {
    if (value == null) return { background: '#f8fafc', color: '#94a3b8' };
    const v = Math.min(100, Math.max(0, value));
    const r = Math.round(255 * (v / 100));
    const g = Math.round(255 * (1 - v / 100));
    const alpha = 0.15 + (v / 100) * 0.65;
    return {
        background: `rgba(${r}, ${g}, 60, ${alpha})`,
        color: v > 60 ? '#fff' : '#1e293b',
    };
};

// ─── Badge & color helpers ───────────────────────────────────────────────────
const badgeClass = (classif) => ({
    aceitavel:  'bg-green-100 text-green-700',
    moderado:   'bg-yellow-100 text-yellow-700',
    importante: 'bg-orange-100 text-orange-700',
    critico:    'bg-red-100 text-red-700',
}[classif] ?? 'bg-gray-100 text-gray-600');

const nrColor = (nr) => {
    if (nr > 9) return 'text-red-600';
    if (nr > 6) return 'text-orange-500';
    if (nr > 3) return 'text-yellow-500';
    return 'text-green-600';
};

// ─── Shared components ───────────────────────────────────────────────────────
const MetricCard = {
    props: ['label', 'value', 'color', 'icon'],
    template: `
        <div class="card p-4">
            <p class="text-xs font-medium text-gray-500 mb-1">{{ label }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ value ?? '—' }}</p>
        </div>
    `,
};

const EmptyState = {
    template: `
        <div class="flex flex-col items-center justify-center h-full w-full text-gray-300 gap-2 py-8">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <p class="text-sm">Sem dados disponíveis</p>
        </div>
    `,
};
</script>
