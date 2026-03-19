<template>
    <AppLayout title="Novo Usuário" subtitle="Cadastrar usuário no sistema">
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
                            placeholder="João Silva"
                            required
                        />

                        <InputField
                            id="email"
                            label="Email"
                            type="email"
                            v-model="form.email"
                            :error="form.errors.email"
                            placeholder="joao@empresa.com"
                            required
                        />

                        <InputField
                            id="password"
                            label="Senha"
                            type="password"
                            v-model="form.password"
                            :error="form.errors.password"
                            placeholder="Mínimo 8 caracteres"
                            required
                        />

                        <SelectField
                            id="role"
                            label="Perfil"
                            v-model="form.role"
                            :error="form.errors.role"
                            :options="roleOptions"
                            placeholder="Selecione o perfil"
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
                                Criar Usuário
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputField from '@/Components/InputField.vue';
import SelectField from '@/Components/SelectField.vue';
import Button from '@/Components/Button.vue';

const roleOptions = [
    { value: 'GLOBAL_ADMIN', label: 'Administrador Global' },
    { value: 'RH', label: 'Recursos Humanos' },
    { value: 'LEADER', label: 'Líder' },
];

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: '',
    is_active: true,
});

const submit = () => form.post(route('admin.users.store'));
</script>
