<template>
    <AppLayout title="Usuários" subtitle="Gerenciar usuários do sistema">
        <Alert v-if="page.props.flash.success" type="success" :message="page.props.flash.success" class="mb-6" />

        <!-- Tenant filter -->
        <div class="mb-4 flex items-center gap-3">
            <select
                v-model="selectedTenant"
                @change="filterByTenant"
                class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                <option value="">Todos os tenants</option>
                <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                    {{ tenant.company_name }}
                </option>
            </select>
        </div>

        <DataTable :columns="columns" :rows="users.data">
            <template #header>
                <h3 class="text-base font-semibold text-gray-900">Usuários do Sistema</h3>
                <Link :href="route('admin.users.create')">
                    <Button variant="primary" size="sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Novo Usuário
                    </Button>
                </Link>
            </template>

            <template #cell-name="{ row }">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-indigo-700 text-xs font-semibold">{{ initials(row.name) }}</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ row.name }}</p>
                        <p class="text-xs text-gray-500">{{ row.email }}</p>
                    </div>
                </div>
            </template>

            <template #cell-tenant="{ row }">
                <span v-if="row.tenant" class="text-sm text-gray-700">{{ row.tenant.company_name }}</span>
                <span v-else class="text-xs text-gray-400 italic">Global</span>
            </template>

            <template #cell-role="{ row }">
                <Badge :variant="roleBadge(row.role)">{{ roleLabel(row.role) }}</Badge>
            </template>

            <template #cell-is_active="{ row }">
                <Badge :variant="row.is_active ? 'success' : 'danger'">
                    {{ row.is_active ? 'Ativo' : 'Inativo' }}
                </Badge>
            </template>

            <template #cell-locked_at="{ row }">
                <Badge v-if="row.locked_at" variant="danger">Bloqueada</Badge>
                <Badge v-else variant="success">Normal</Badge>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-2">
                    <form @submit.prevent="toggleLock(row)">
                        <Button size="sm" :variant="row.locked_at ? 'secondary' : 'warning'" type="submit">
                            {{ row.locked_at ? 'Desbloquear' : 'Bloquear' }}
                        </Button>
                    </form>
                    <Link :href="route('admin.users.edit', row.id)">
                        <Button size="sm" variant="secondary">Editar</Button>
                    </Link>
                    <Button size="sm" variant="danger" @click="confirmDelete(row)">Excluir</Button>
                </div>
            </template>

            <template #footer>
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>{{ users.total }} usuário(s)</span>
                    <div class="flex gap-2">
                        <Link v-if="users.prev_page_url" :href="users.prev_page_url">
                            <Button size="sm" variant="secondary">← Anterior</Button>
                        </Link>
                        <Link v-if="users.next_page_url" :href="users.next_page_url">
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
            </p>
            <template #footer>
                <Button variant="secondary" @click="deletingUser = null">Cancelar</Button>
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

const props = defineProps({ users: Object, tenants: Array });
const page = usePage();
const deletingUser = ref(null);
const deleting = ref(false);
const selectedTenant = ref('');

const columns = [
    { key: 'name', label: 'Usuário' },
    { key: 'tenant', label: 'Tenant' },
    { key: 'role', label: 'Perfil' },
    { key: 'is_active', label: 'Status' },
    { key: 'locked_at', label: 'Conta' },
];

const initials = (name) => name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

const roleLabel = (role) => ({ GLOBAL_ADMIN: 'Admin Global', RH: 'RH', LEADER: 'Líder' }[role] || role);
const roleBadge = (role) => ({ GLOBAL_ADMIN: 'primary', RH: 'info', LEADER: 'warning' }[role] || 'info');

const filterByTenant = () => {
    router.get(route('admin.users.index'), { tenant_id: selectedTenant.value || undefined }, { preserveState: true });
};

const toggleLock = (user) => {
    router.post(route('admin.users.toggle-lock', user.id));
};

const confirmDelete = (user) => { deletingUser.value = user; };
const handleDelete = () => {
    deleting.value = true;
    router.delete(route('admin.users.destroy', deletingUser.value.id), {
        onFinish: () => { deleting.value = false; deletingUser.value = null; },
    });
};
</script>
