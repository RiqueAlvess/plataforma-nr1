<template>
    <AppLayout :title="campaign.nome" subtitle="Gerenciamento de convites">
        <Alert v-if="page.props.flash.success" type="success" :message="page.props.flash.success" class="mb-6" />
        <Alert v-if="page.props.flash.error" type="error" :message="page.props.flash.error" class="mb-6" />

        <!-- Header cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <StatCard label="Convidados" :value="metricas.total_convidados" color="indigo" />
            <StatCard label="Respondidos" :value="metricas.total_respondidos" color="green" />
            <StatCard :label="`Taxa de Adesão`" :value="metricas.taxa_adesao + '%'" color="blue" />
        </div>

        <!-- Campaign info + actions -->
        <div class="card mb-6">
            <div class="card-header flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <Badge :variant="statusVariant(campaign.status)">{{ statusLabel(campaign.status) }}</Badge>
                    <span class="text-sm text-gray-500">{{ campaign.descricao ?? 'Sem descrição' }}</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link :href="route('tenant.campanhas.edit', campaign.id)">
                        <Button size="sm" variant="secondary">Editar</Button>
                    </Link>
                    <Link v-if="campaign.responses_count > 0" :href="route('tenant.campanhas.analytics', campaign.id)">
                        <Button size="sm" variant="secondary">Ver Analytics</Button>
                    </Link>
                    <Button
                        v-if="campaign.status !== 'encerrada'"
                        size="sm"
                        :variant="campaign.status === 'ativa' ? 'warning' : 'success'"
                        :loading="togglingStatus"
                        @click="toggleStatus"
                    >
                        {{ campaign.status === 'ativa' ? 'Encerrar' : 'Ativar' }}
                    </Button>
                    <Button
                        v-if="campaign.status === 'ativa'"
                        size="sm"
                        variant="primary"
                        :loading="preparando"
                        @click="prepararConvites"
                    >
                        Preparar Convites
                    </Button>
                </div>
            </div>
        </div>

        <!-- Invites table -->
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">
                    Convites ({{ invites.total }})
                </h3>
                <div v-if="selectedIds.length > 0" class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">{{ selectedIds.length }} selecionado(s)</span>
                    <Button
                        size="sm"
                        variant="primary"
                        :loading="enviando"
                        :disabled="campaign.status !== 'ativa'"
                        @click="enviarSelecionados"
                    >
                        Enviar Selecionados
                    </Button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" :checked="allSelected" @change="toggleAll" class="rounded border-gray-300 text-indigo-600" />
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Hash do Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status Envio</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status Resposta</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Enviado em</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <tr v-if="invites.data.length === 0">
                            <td colspan="5" class="px-4 py-12 text-center text-gray-400 text-sm">
                                Nenhum convite preparado. Clique em "Preparar Convites" para gerar a lista.
                            </td>
                        </tr>
                        <tr v-for="invite in invites.data" :key="invite.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <input
                                    type="checkbox"
                                    :value="invite.id"
                                    v-model="selectedIds"
                                    :disabled="invite.resposta_status === 'respondido'"
                                    class="rounded border-gray-300 text-indigo-600"
                                />
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600">
                                {{ invite.email_hash.substring(0, 16) }}…
                            </td>
                            <td class="px-4 py-3">
                                <Badge :variant="envioVariant(invite.envio_status)">{{ envioLabel(invite.envio_status) }}</Badge>
                            </td>
                            <td class="px-4 py-3">
                                <Badge :variant="respostaVariant(invite.resposta_status)">{{ respostaLabel(invite.resposta_status) }}</Badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ invite.enviado_em ? formatDate(invite.enviado_em) : '—' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="invites.total > 0" class="px-4 py-3 border-t border-gray-100 flex items-center justify-between text-sm text-gray-600">
                <span>{{ invites.total }} convite(s)</span>
                <div class="flex gap-2">
                    <Link v-if="invites.prev_page_url" :href="invites.prev_page_url">
                        <Button size="sm" variant="secondary">← Anterior</Button>
                    </Link>
                    <Link v-if="invites.next_page_url" :href="invites.next_page_url">
                        <Button size="sm" variant="secondary">Próxima →</Button>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import Alert from '@/Components/Alert.vue';
import StatCard from '@/Components/StatCard.vue';

const props = defineProps({
    campaign: Object,
    invites: Object,
    metricas: Object,
});

const page = usePage();
const selectedIds = ref([]);
const preparando = ref(false);
const enviando = ref(false);
const togglingStatus = ref(false);

const allSelected = computed(() =>
    props.invites.data.length > 0 &&
    props.invites.data
        .filter(i => i.resposta_status === 'pendente')
        .every(i => selectedIds.value.includes(i.id))
);

const toggleAll = (e) => {
    if (e.target.checked) {
        selectedIds.value = props.invites.data
            .filter(i => i.resposta_status === 'pendente')
            .map(i => i.id);
    } else {
        selectedIds.value = [];
    }
};

const prepararConvites = () => {
    preparando.value = true;
    router.post(route('tenant.campanhas.convites.preparar', props.campaign.id), {}, {
        onFinish: () => { preparando.value = false; },
    });
};

const enviarSelecionados = () => {
    if (selectedIds.value.length === 0) return;
    enviando.value = true;
    router.post(route('tenant.campanhas.convites.enviar', props.campaign.id), {
        invite_ids: selectedIds.value,
    }, {
        onFinish: () => { enviando.value = false; selectedIds.value = []; },
    });
};

const toggleStatus = () => {
    togglingStatus.value = true;
    router.post(route('tenant.campanhas.toggle-status', props.campaign.id), {}, {
        onFinish: () => { togglingStatus.value = false; },
    });
};

const statusVariant = (s) => ({ rascunho: 'warning', ativa: 'success', encerrada: 'info' }[s] ?? 'info');
const statusLabel = (s) => ({ rascunho: 'Rascunho', ativa: 'Ativa', encerrada: 'Encerrada' }[s] ?? s);

const envioVariant = (s) => ({ pendente: 'warning', enviado: 'success', erro: 'danger' }[s] ?? 'info');
const envioLabel = (s) => ({ pendente: 'Pendente', enviado: 'Enviado', erro: 'Erro' }[s] ?? s);

const respostaVariant = (s) => ({ pendente: 'warning', respondido: 'success' }[s] ?? 'info');
const respostaLabel = (s) => ({ pendente: 'Pendente', respondido: 'Respondido' }[s] ?? s);

const formatDate = (d) => d ? new Date(d).toLocaleString('pt-BR') : '—';
</script>
