<template>
    <AppLayout title="Campanhas" subtitle="Gerenciar campanhas de pesquisa HSE-IT">
        <Alert v-if="page.props.flash.success" type="success" :message="page.props.flash.success" class="mb-6" />
        <Alert v-if="page.props.flash.error" type="error" :message="page.props.flash.error" class="mb-6" />

        <DataTable :columns="columns" :rows="campaigns.data">
            <template #header>
                <h3 class="text-base font-semibold text-gray-900">Campanhas de Pesquisa</h3>
                <Link :href="route('tenant.campanhas.create')">
                    <Button variant="primary" size="sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nova Campanha
                    </Button>
                </Link>
            </template>

            <template #cell-nome="{ row }">
                <Link :href="route('tenant.campanhas.show', row.id)" class="font-medium text-indigo-600 hover:text-indigo-700">
                    {{ row.nome }}
                </Link>
            </template>

            <template #cell-status="{ row }">
                <Badge :variant="statusVariant(row.status)">{{ statusLabel(row.status) }}</Badge>
            </template>

            <template #cell-invites_count="{ row }">
                <span class="text-sm text-gray-600">{{ row.invites_count ?? 0 }}</span>
            </template>

            <template #cell-responses_count="{ row }">
                <span class="text-sm text-gray-600">{{ row.responses_count ?? 0 }}</span>
            </template>

            <template #cell-created_at="{ row }">
                <span class="text-sm text-gray-600">{{ formatDate(row.created_at) }}</span>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-2">
                    <Link :href="route('tenant.campanhas.show', row.id)">
                        <Button size="sm" variant="secondary">Gerenciar</Button>
                    </Link>
                    <Link v-if="row.responses_count > 0" :href="route('tenant.campanhas.analytics', row.id)">
                        <Button size="sm" variant="secondary">Analytics</Button>
                    </Link>
                    <Button size="sm" variant="danger" @click="confirmDelete(row)">Excluir</Button>
                </div>
            </template>

            <template #footer>
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>{{ campaigns.total }} campanha(s) encontrada(s)</span>
                    <div class="flex gap-2">
                        <Link v-if="campaigns.prev_page_url" :href="campaigns.prev_page_url">
                            <Button size="sm" variant="secondary">← Anterior</Button>
                        </Link>
                        <Link v-if="campaigns.next_page_url" :href="campaigns.next_page_url">
                            <Button size="sm" variant="secondary">Próxima →</Button>
                        </Link>
                    </div>
                </div>
            </template>
        </DataTable>

        <Modal :show="!!deletingCampaign" title="Confirmar exclusão" @close="deletingCampaign = null">
            <p class="text-gray-600 text-sm">
                Tem certeza que deseja excluir a campanha
                <strong class="text-gray-900">{{ deletingCampaign?.nome }}</strong>?
                Todos os convites e respostas vinculados serão removidos.
            </p>
            <template #footer>
                <Button variant="secondary" @click="deletingCampaign = null">Cancelar</Button>
                <Button variant="danger" :loading="deleting" @click="handleDelete">Excluir</Button>
            </template>
        </Modal>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import Alert from '@/Components/Alert.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({ campaigns: Object });
const page = usePage();
const deletingCampaign = ref(null);
const deleting = ref(false);

const columns = [
    { key: 'nome', label: 'Nome' },
    { key: 'status', label: 'Status' },
    { key: 'invites_count', label: 'Convidados' },
    { key: 'responses_count', label: 'Respostas' },
    { key: 'created_at', label: 'Criada em' },
];

const statusVariant = (s) => ({ rascunho: 'warning', ativa: 'success', encerrada: 'info' }[s] ?? 'info');
const statusLabel = (s) => ({ rascunho: 'Rascunho', ativa: 'Ativa', encerrada: 'Encerrada' }[s] ?? s);
const formatDate = (d) => d ? new Date(d).toLocaleDateString('pt-BR') : '—';

const confirmDelete = (c) => { deletingCampaign.value = c; };
const handleDelete = () => {
    deleting.value = true;
    router.delete(route('tenant.campanhas.destroy', deletingCampaign.value.id), {
        onFinish: () => { deleting.value = false; deletingCampaign.value = null; },
    });
};
</script>
