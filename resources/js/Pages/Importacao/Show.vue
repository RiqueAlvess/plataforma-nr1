<template>
    <AppLayout :title="import_.nome_arquivo" subtitle="Detalhes da importação">
        <Alert v-if="page.props.flash.success" type="success" :message="page.props.flash.success" class="mb-6" />
        <Alert v-if="page.props.flash.error" type="error" :message="page.props.flash.error" class="mb-6" />

        <!-- Resumo -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="card p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</p>
                <div class="mt-1">
                    <Badge :variant="statusVariant(import_.status)">{{ statusLabel(import_.status) }}</Badge>
                </div>
            </div>
            <div class="card p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total de Registros</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ import_.total_registros }}</p>
            </div>
            <div class="card p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Importados</p>
                <p class="mt-1 text-2xl font-semibold text-green-600">{{ import_.registros_importados }}</p>
            </div>
            <div class="card p-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Com Erro</p>
                <p class="mt-1 text-2xl font-semibold text-red-600">{{ import_.registros_com_erro }}</p>
            </div>
        </div>

        <!-- Mensagem de erro -->
        <div v-if="import_.mensagem_erro" class="rounded-lg bg-red-50 border border-red-200 p-4 mb-6">
            <h4 class="text-sm font-semibold text-red-800 mb-1">Erro na importação</h4>
            <p class="text-sm text-red-700">{{ import_.mensagem_erro }}</p>
        </div>

        <!-- Informações da importação -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="text-base font-semibold text-gray-900">Informações</h3>
            </div>
            <div class="card-body">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Arquivo</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ import_.nome_arquivo }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Importado por</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ import_.user?.name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Data de criação</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ formatDate(import_.created_at) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Última atualização</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ formatDate(import_.updated_at) }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Registros importados -->
        <DataTable :columns="columns" :rows="import_.records ?? []">
            <template #header>
                <h3 class="text-base font-semibold text-gray-900">Registros Importados</h3>
            </template>

            <template #cell-unidade="{ row }">
                <span class="text-sm text-gray-900">{{ row.unidade?.nome ?? '—' }}</span>
            </template>

            <template #cell-setor="{ row }">
                <span class="text-sm text-gray-900">{{ row.setor?.nome ?? '—' }}</span>
            </template>

            <template #cell-linha_csv="{ row }">
                <span class="text-xs text-gray-500">Linha {{ row.linha_csv }}</span>
            </template>
        </DataTable>

        <div class="mt-6 flex gap-3">
            <Link :href="route('tenant.importacao.index')">
                <Button variant="secondary">← Voltar</Button>
            </Link>
        </div>
    </AppLayout>
</template>

<script setup>
import { usePage, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import Alert from '@/Components/Alert.vue';

const props = defineProps({
    import: Object,
});

// Renomeado para evitar conflito com keyword
const import_ = props.import;
const page = usePage();

const columns = [
    { key: 'unidade', label: 'Unidade' },
    { key: 'setor', label: 'Setor' },
    { key: 'email', label: 'Email' },
    { key: 'linha_csv', label: 'Linha CSV' },
];

const statusVariant = (status) => {
    const map = { concluido: 'success', processando: 'info', erro: 'danger', pendente: 'warning' };
    return map[status] ?? 'info';
};

const statusLabel = (status) => {
    const map = { concluido: 'Concluído', processando: 'Processando', erro: 'Erro', pendente: 'Pendente' };
    return map[status] ?? status;
};

const formatDate = (dateStr) => {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleString('pt-BR');
};
</script>
