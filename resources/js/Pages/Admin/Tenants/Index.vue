<template>
    <AppLayout title="Tenants" subtitle="Gerenciar empresas do sistema">
        <Alert v-if="page.props.flash.success" type="success" :message="page.props.flash.success" class="mb-6" />

        <DataTable :columns="columns" :rows="tenants.data">
            <template #header>
                <h3 class="text-base font-semibold text-gray-900">Empresas Cadastradas</h3>
                <Link :href="route('admin.tenants.create')">
                    <Button variant="primary" size="sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Nova Empresa
                    </Button>
                </Link>
            </template>

            <template #cell-company_name="{ row }">
                <Link :href="route('admin.tenants.show', row.id)" class="font-medium text-indigo-600 hover:text-indigo-700">
                    {{ row.company_name }}
                </Link>
            </template>

            <template #cell-domains="{ row }">
                <div class="flex flex-wrap gap-1">
                    <Badge v-for="domain in row.domains" :key="domain.id" variant="info">{{ domain.domain }}</Badge>
                    <span v-if="!row.domains?.length" class="text-gray-400">—</span>
                </div>
            </template>

            <template #cell-is_active="{ row }">
                <Badge :variant="row.is_active ? 'success' : 'danger'">
                    {{ row.is_active ? 'Ativo' : 'Inativo' }}
                </Badge>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-2">
                    <Link :href="route('admin.tenants.edit', row.id)">
                        <Button size="sm" variant="secondary">Editar</Button>
                    </Link>
                    <Button size="sm" variant="danger" @click="confirmDelete(row)">Excluir</Button>
                </div>
            </template>

            <template #footer>
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>{{ tenants.total }} empresa(s) encontrada(s)</span>
                    <div class="flex gap-2">
                        <Link v-if="tenants.prev_page_url" :href="tenants.prev_page_url">
                            <Button size="sm" variant="secondary">← Anterior</Button>
                        </Link>
                        <Link v-if="tenants.next_page_url" :href="tenants.next_page_url">
                            <Button size="sm" variant="secondary">Próxima →</Button>
                        </Link>
                    </div>
                </div>
            </template>
        </DataTable>

        <!-- Delete modal -->
        <Modal :show="!!deletingTenant" title="Confirmar exclusão" @close="deletingTenant = null">
            <p class="text-gray-600 text-sm">
                Tem certeza que deseja excluir o tenant
                <strong class="text-gray-900">{{ deletingTenant?.company_name }}</strong>?
                Esta ação não pode ser desfeita e todos os dados serão removidos.
            </p>
            <template #footer>
                <Button variant="secondary" @click="deletingTenant = null">Cancelar</Button>
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
    tenants: Object,
});

const page = usePage();
const deletingTenant = ref(null);
const deleting = ref(false);

const columns = [
    { key: 'company_name', label: 'Empresa' },
    { key: 'cnpj', label: 'CNPJ' },
    { key: 'responsible_email', label: 'Email Responsável' },
    { key: 'domains', label: 'Domínios' },
    { key: 'is_active', label: 'Status' },
];

const confirmDelete = (tenant) => {
    deletingTenant.value = tenant;
};

const handleDelete = () => {
    deleting.value = true;
    router.delete(route('admin.tenants.destroy', deletingTenant.value.id), {
        onFinish: () => {
            deleting.value = false;
            deletingTenant.value = null;
        },
    });
};
</script>
