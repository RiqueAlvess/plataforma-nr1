<template>
    <AppLayout title="Usuários" subtitle="Gerenciar usuários da organização">
        <Alert v-if="page.props.flash.success" type="success" :message="page.props.flash.success" class="mb-6" />
        <Alert v-if="page.props.flash.error" type="error" :message="page.props.flash.error" class="mb-6" />

        <DataTable :columns="columns" :rows="usuarios.data">
            <template #header>
                <h3 class="text-base font-semibold text-gray-900">Usuários Cadastrados</h3>
                <Link :href="route('tenant.usuarios.create')">
                    <Button variant="primary" size="sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Novo Usuário
                    </Button>
                </Link>
            </template>

            <template #cell-role="{ row }">
                <Badge :variant="row.role === 'RH' ? 'info' : 'warning'">{{ row.role }}</Badge>
            </template>

            <template #cell-is_active="{ row }">
                <Badge :variant="row.is_active ? 'success' : 'danger'">
                    {{ row.is_active ? 'Ativo' : 'Inativo' }}
                </Badge>
            </template>

            <template #cell-status="{ row }">
                <Badge v-if="row.locked_at" variant="danger">Bloqueado</Badge>
                <span v-else class="text-xs text-gray-400">—</span>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-2">
                    <Link :href="route('tenant.usuarios.edit', row.id)">
                        <Button size="sm" variant="secondary">Editar</Button>
                    </Link>
                    <Button
                        size="sm"
                        :variant="row.locked_at ? 'secondary' : 'danger'"
                        @click="toggleLock(row)"
                        :disabled="row.id === currentUser.id"
                    >
                        {{ row.locked_at ? 'Desbloquear' : 'Bloquear' }}
                    </Button>
                    <Button
                        size="sm"
                        variant="danger"
                        @click="confirmDelete(row)"
                        :disabled="row.id === currentUser.id"
                    >
                        Excluir
                    </Button>
                </div>
            </template>

            <template #footer>
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>{{ usuarios.total }} usuário(s) encontrado(s)</span>
                    <div class="flex gap-2">
                        <Link v-if="usuarios.prev_page_url" :href="usuarios.prev_page_url">
                            <Button size="sm" variant="secondary">← Anterior</Button>
                        </Link>
                        <Link v-if="usuarios.next_page_url" :href="usuarios.next_page_url">
                            <Button size="sm" variant="secondary">Próxima →</Button>
                        </Link>
                    </div>
                </div>
            </template>
        </DataTable>

        <Modal :show="!!deletingUser" title="Confirmar exclusão" @close="deletingUser = null">
            <p class="text-gray-600 text-sm">
                Tem certeza que deseja excluir o usuário
                <strong class="text-gray-900">{{ deletingUser?.name }}</strong>?
                Esta ação não pode ser desfeita.
            </p>
            <template #footer>
                <Button variant="secondary" @click="deletingUser = null">Cancelar</Button>
                <Button variant="danger" :loading="deleting" @click="handleDelete">Excluir</Button>
            </template>
        </Modal>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import Alert from '@/Components/Alert.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    usuarios: Object,
});

const page = usePage();
const currentUser = computed(() => page.props.auth.user);
const deletingUser = ref(null);
const deleting = ref(false);

const columns = [
    { key: 'name', label: 'Nome' },
    { key: 'email', label: 'Email' },
    { key: 'role', label: 'Perfil' },
    { key: 'is_active', label: 'Situação' },
    { key: 'status', label: 'Bloqueio' },
];

const confirmDelete = (user) => {
    deletingUser.value = user;
};

const handleDelete = () => {
    deleting.value = true;
    router.delete(route('tenant.usuarios.destroy', deletingUser.value.id), {
        onFinish: () => {
            deleting.value = false;
            deletingUser.value = null;
        },
    });
};

const toggleLock = (user) => {
    router.post(route('tenant.usuarios.toggle-lock', user.id));
};
</script>
