<template>
    <AppLayout :title="`Editar: ${user.name}`" subtitle="Atualizar dados do usuário">
        <div class="max-w-2xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-gray-900">Dados do Usuário</h3>
                </div>
                <div class="card-body">
                    <form @submit.prevent="submit" class="space-y-5">
                        <InputField
                            id="name"
                            label="Nome Completo"
                            v-model="form.name"
                            :error="form.errors.name"
                            required
                        />

                        <InputField
                            id="email"
                            label="Email"
                            type="email"
                            v-model="form.email"
                            :error="form.errors.email"
                            required
                        />

                        <InputField
                            id="password"
                            label="Nova Senha"
                            type="password"
                            v-model="form.password"
                            :error="form.errors.password"
                            hint="Deixe em branco para manter a senha atual"
                        />

                        <SelectField
                            id="role"
                            label="Perfil"
                            v-model="form.role"
                            :error="form.errors.role"
                            :options="roleOptions"
                            required
                        />

                        <SelectField
                            v-if="requiresTenant"
                            id="tenant_id"
                            label="Tenant (Empresa)"
                            v-model="form.tenant_id"
                            :error="form.errors.tenant_id"
                            :options="tenantOptions"
                            placeholder="Selecione o tenant"
                            required
                        />

                        <div class="flex items-center gap-3">
                            <input
                                id="is_active"
                                type="checkbox"
                                v-model="form.is_active"
                                class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <label for="is_active" class="text-sm font-medium text-gray-700">Usuário ativo</label>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <Link :href="route('admin.users.index')">
                                <Button variant="secondary">Cancelar</Button>
                            </Link>
                            <Button type="submit" variant="primary" :loading="form.processing">
                                Salvar Alterações
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputField from '@/Components/InputField.vue';
import SelectField from '@/Components/SelectField.vue';
import Button from '@/Components/Button.vue';

const props = defineProps({
    user: Object,
    tenants: Array,
});

const roleOptions = [
    { value: 'GLOBAL_ADMIN', label: 'Administrador Global' },
    { value: 'RH', label: 'Recursos Humanos' },
    { value: 'LEADER', label: 'Líder' },
];

const tenantOptions = computed(() =>
    (props.tenants || []).map(t => ({ value: t.id, label: t.company_name }))
);

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    role: props.user.role,
    tenant_id: props.user.tenant_id ?? '',
    is_active: props.user.is_active,
});

const requiresTenant = computed(() => ['RH', 'LEADER'].includes(form.role));

const submit = () => form.put(route('admin.users.update', props.user.id));
</script>
