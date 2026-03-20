<template>
    <AppLayout :title="`Analytics – ${campaign.nome}`" subtitle="Dashboard analítico da campanha">
        <div class="space-y-6">

            <!-- Metrics cards -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <StatCard label="Convidados" :value="metricas.total_convidados" color="indigo" />
                <StatCard label="Respondidos" :value="metricas.total_respondidos" color="green" />
                <StatCard label="Taxa de Adesão" :value="metricas.taxa_adesao + '%'" color="blue" />
                <StatCard label="IGRP" :value="metricas.igrp + '%'" color="purple" />
                <StatCard label="Risco Alto (%)" :value="metricas.percentual_risco_alto + '%'" color="red" />
            </div>

            <!-- Row 1: Radar + Risk donut -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Radar chart -->
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Scores por Dimensão (Radar)</h3>
                    <div class="h-64 flex items-center justify-center">
                        <Radar v-if="hasData" :data="radarChartData" :options="radarOptions" />
                        <EmptyChart v-else />
                    </div>
                </div>

                <!-- Donut risk distribution -->
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Distribuição de Risco</h3>
                    <div class="h-64 flex items-center justify-center">
                        <Doughnut v-if="hasRiskData" :data="donutRiscoData" :options="donutOptions" />
                        <EmptyChart v-else />
                    </div>
                </div>
            </div>

            <!-- Row 2: Horizontal bars + gender pie -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Dimension scores bar -->
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Score Médio por Dimensão</h3>
                    <div class="h-64">
                        <Bar v-if="hasData" :data="barDimensoesData" :options="barHorizontalOptions" />
                        <EmptyChart v-else />
                    </div>
                </div>

                <!-- Gender pie -->
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Distribuição por Gênero</h3>
                    <div class="h-64 flex items-center justify-center">
                        <Pie v-if="hasGenderData" :data="generoData" :options="pieOptions" />
                        <EmptyChart v-else />
                    </div>
                </div>
            </div>

            <!-- Row 3: Age bar + scores by gender line -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Age distribution -->
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Distribuição por Faixa Etária</h3>
                    <div class="h-64">
                        <Bar v-if="hasFaixaData" :data="faixaData" :options="barOptions" />
                        <EmptyChart v-else />
                    </div>
                </div>

                <!-- Scores by gender line -->
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Scores por Gênero</h3>
                    <div class="h-64">
                        <Line v-if="hasGenderLineData" :data="generoLineData" :options="lineOptions" />
                        <EmptyChart v-else />
                    </div>
                </div>
            </div>

            <!-- Scores by age line -->
            <div class="card p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Scores por Faixa Etária</h3>
                <div class="h-72">
                    <Line v-if="hasFaixaLineData" :data="faixaLineData" :options="lineOptions" />
                    <EmptyChart v-else />
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import {
    Chart as ChartJS,
    RadialLinearScale,
    PointElement,
    LineElement,
    Filler,
    Tooltip,
    Legend,
    CategoryScale,
    LinearScale,
    BarElement,
    ArcElement,
} from 'chart.js';
import { Radar, Doughnut, Bar, Pie, Line } from 'vue-chartjs';

ChartJS.register(
    RadialLinearScale, PointElement, LineElement, Filler,
    Tooltip, Legend, CategoryScale, LinearScale, BarElement, ArcElement
);

const props = defineProps({
    campaign: Object,
    analytics: Object,
});

// ── shortcuts ──────────────────────────────────────────────────────────────
const metricas = computed(() => props.analytics.metricas);
const scoresDim = computed(() => props.analytics.scores_dimensoes ?? []);
const distRisco  = computed(() => props.analytics.distribuicao_risco ?? []);
const distGenero = computed(() => props.analytics.distribuicao_genero ?? []);
const distFaixa  = computed(() => props.analytics.distribuicao_faixa ?? []);
const scoresGenero = computed(() => props.analytics.scores_por_genero ?? { generos: [], dados: [] });
const scoresFaixa  = computed(() => props.analytics.scores_por_faixa ?? { faixas: [], dados: [] });

// ── guards ──────────────────────────────────────────────────────────────────
const hasData        = computed(() => scoresDim.value.some(d => d.score > 0));
const hasRiskData    = computed(() => distRisco.value.length > 0);
const hasGenderData  = computed(() => distGenero.value.length > 0);
const hasFaixaData   = computed(() => distFaixa.value.length > 0);
const hasGenderLineData = computed(() => scoresGenero.value.generos.length > 0);
const hasFaixaLineData  = computed(() => scoresFaixa.value.faixas.length > 0);

// ── palette ─────────────────────────────────────────────────────────────────
const COLORS = ['#6366f1', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'];
const RISK_COLORS = {
    aceitavel: '#22c55e',
    moderado:  '#f59e0b',
    importante: '#f97316',
    critico:   '#ef4444',
};

// ── Radar ───────────────────────────────────────────────────────────────────
const radarChartData = computed(() => ({
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
    responsive: true,
    maintainAspectRatio: false,
    scales: { r: { min: 0, max: 4, ticks: { stepSize: 1, font: { size: 10 } } } },
    plugins: { legend: { display: false } },
};

// ── Donut risk ───────────────────────────────────────────────────────────────
const donutRiscoData = computed(() => ({
    labels: distRisco.value.map(r => r.label),
    datasets: [{
        data: distRisco.value.map(r => r.total),
        backgroundColor: distRisco.value.map(r => RISK_COLORS[r.classificacao] ?? '#94a3b8'),
    }],
}));

const donutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { position: 'right' } },
};

// ── Bar dimensoes ────────────────────────────────────────────────────────────
const barDimensoesData = computed(() => ({
    labels: scoresDim.value.map(d => d.label),
    datasets: [{
        label: 'Score',
        data: scoresDim.value.map(d => d.score),
        backgroundColor: scoresDim.value.map(d => d.negativa ? '#ef4444' : '#6366f1'),
    }],
}));

const barHorizontalOptions = {
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: false,
    scales: { x: { min: 0, max: 4 } },
    plugins: { legend: { display: false } },
};

const barOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
};

// ── Pie gender ───────────────────────────────────────────────────────────────
const generoData = computed(() => ({
    labels: distGenero.value.map(g => g.label),
    datasets: [{
        data: distGenero.value.map(g => g.total),
        backgroundColor: COLORS,
    }],
}));

const pieOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { position: 'right' } },
};

// ── Bar faixa ────────────────────────────────────────────────────────────────
const faixaData = computed(() => ({
    labels: distFaixa.value.map(f => f.faixa),
    datasets: [{
        label: 'Respondentes',
        data: distFaixa.value.map(f => f.total),
        backgroundColor: '#6366f1',
    }],
}));

// ── Line scores by gender ────────────────────────────────────────────────────
const generoLineData = computed(() => ({
    labels: scoresGenero.value.dados.map(d => d.label),
    datasets: scoresGenero.value.generos.map((g, i) => ({
        label: g,
        data: scoresGenero.value.dados.map(d => d[g] ?? null),
        borderColor: COLORS[i % COLORS.length],
        backgroundColor: COLORS[i % COLORS.length] + '33',
        tension: 0.3,
    })),
}));

// ── Line scores by faixa ─────────────────────────────────────────────────────
const faixaLineData = computed(() => ({
    labels: scoresFaixa.value.dados.map(d => d.label),
    datasets: scoresFaixa.value.faixas.map((f, i) => ({
        label: f,
        data: scoresFaixa.value.dados.map(d => d[f] ?? null),
        borderColor: COLORS[i % COLORS.length],
        backgroundColor: COLORS[i % COLORS.length] + '33',
        tension: 0.3,
    })),
}));

const lineOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: { y: { min: 0, max: 4 } },
    plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } },
};

// ── Empty chart placeholder ───────────────────────────────────────────────────
const EmptyChart = {
    template: `<div class="flex flex-col items-center justify-center h-full text-gray-300 gap-2">
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <p class="text-sm">Sem dados suficientes</p>
    </div>`,
};
</script>
