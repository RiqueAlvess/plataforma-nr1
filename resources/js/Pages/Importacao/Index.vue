<template>
    <AppLayout title="Importação CSV" subtitle="Gerenciar importações de registros organizacionais">
        <Alert v-if="page.props.flash.success" type="success" :message="page.props.flash.success" class="mb-6" />
        <Alert v-if="page.props.flash.error" type="error" :message="page.props.flash.error" class="mb-6" />

        <DataTable :columns="columns" :rows="imports.data">
            <template #header>
                <h3 class="text-base font-semibold text-gray-900">Histórico de Importações</h3>
                <Link :href="route('tenant.importacao.create')">
                    <Button variant="primary" size="sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Nova Importação
                    </Button>
                </Link>
            </template>

            <template #cell-nome_arquivo="{ row }">
                <Link :href="route('tenant.importacao.show', row.id)" class="font-medium text-indigo-600 hover:text-indigo-700">
                    {{ row.nome_arquivo }}
                </Link>
            </template>

            <template #cell-status="{ row }">
                <Badge :variant="statusVariant(row.status)">{{ statusLabel(row.status) }}</Badge>
            </template>

            <template #cell-registros="{ row }">
                <span class="text-sm text-gray-600">
                    {{ row.registros_importados }} / {{ row.total_registros }}
                    <span v-if="row.registros_com_erro > 0" class="text-red-500">({{ row.registros_com_erro }} erros)</span>
                </span>
            </template>

            <template #cell-user="{ row }">
                <span class="text-sm text-gray-600">{{ row.user?.name ?? '—' }}</span>
            </template>

            <template #cell-created_at="{ row }">
                <span class="text-sm text-gray-600">{{ formatDate(row.created_at) }}</span>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-2">
                    <Link :href="route('tenant.importacao.show', row.id)">
                        <Button size="sm" variant="secondary">Ver detalhes</Button>
                    </Link>
                    <Button size="sm" variant="danger" @click="confirmDelete(row)">Excluir</Button>
                </div>
            </template>

            <template #footer>
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>{{ imports.total }} importação(ões) encontrada(s)</span>
                    <div class="flex gap-2">
                        <Link v-if="imports.prev_page_url" :href="imports.prev_page_url">
                            <Button size="sm" variant="secondary">← Anterior</Button>
                        </Link>
                        <Link v-if="imports.next_page_url" :href="imports.next_page_url">
                            <Button size="sm" variant="secondary">Próxima →</Button>
                        </Link>
                    </div>
                </div>
            </template>
        </DataTable>

        <Modal :show="!!deletingImport" title="Confirmar exclusão" @close="deletingImport = null">
            <p class="text-gray-600 text-sm">
                Tem certeza que deseja excluir a importação
                <strong class="text-gray-900">{{ deletingImport?.nome_arquivo }}</strong>?
                Todos os registros vinculados a esta importação serão removidos.
            </p>
            <template #footer>
                <Button variant="secondary" @click="deletingImport = null">Cancelar</Button>
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

const props = defineProps({
    imports: Object,
});

const page = usePage();
const deletingImport = ref(null);
const deleting = ref(false);

const columns = [
    { key: 'nome_arquivo', label: 'Arquivo' },
    { key: 'status', label: 'Status' },
    { key: 'registros', label: 'Registros (import./total)' },
    { key: 'user', label: 'Importado por' },
    { key: 'created_at', label: 'Data' },
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

const confirmDelete = (imp) => {
    deletingImport.value = imp;
};

const handleDelete = () => {
    deleting.value = true;
    router.delete(route('tenant.importacao.destroy', deletingImport.value.id), {
        onFinish: () => {
            deleting.value = false;
            deletingImport.value = null;
        },
    });
};
</script>
