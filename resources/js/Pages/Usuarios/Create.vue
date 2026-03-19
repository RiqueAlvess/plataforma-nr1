<template>
    <AppLayout title="Novo Usuário" subtitle="Cadastrar novo usuário na organização">
        <div class="max-w-3xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-gray-900">Dados do Usuário</h3>
                </div>
                <div class="card-body">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <InputField
                                id="name"
                                label="Nome completo"
                                v-model="form.name"
                                :error="form.errors.name"
                                placeholder="Nome do usuário"
                                required
                            />
                            <InputField
                                id="email"
                                label="Email"
                                type="email"
                                v-model="form.email"
                                :error="form.errors.email"
                                placeholder="usuario@empresa.com"
                                required
                            />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
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
                                :options="roles"
                                required
                            />
                        </div>

                        <div class="flex items-center gap-3">
                            <input
                                id="is_active"
                                type="checkbox"
                                v-model="form.is_active"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <label for="is_active" class="text-sm font-medium text-gray-700">Usuário ativo</label>
                        </div>

                        <!-- Permissões de Líder -->
                        <div v-if="form.role === 'LEADER'" class="border border-gray-200 rounded-lg p-4 space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-gray-800">Permissões de Visualização</h4>
                                <Button type="button" size="sm" variant="secondary" @click="addPermissao">
                                    + Adicionar permissão
                                </Button>
                            </div>
                            <p class="text-xs text-gray-500">
                                Configure quais unidades e setores este líder poderá visualizar.
                                Deixe o setor em branco para dar acesso a toda a unidade.
                            </p>

                            <div v-if="form.permissoes.length === 0" class="text-sm text-gray-400 text-center py-4">
                                Nenhuma permissão configurada. O líder não verá nenhum dado.
                            </div>

                            <div
                                v-for="(perm, index) in form.permissoes"
                                :key="index"
                                class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg"
                            >
                                <div class="flex-1 grid grid-cols-2 gap-3">
                                    <SelectField
                                        :id="`perm-unidade-${index}`"
                                        label="Unidade"
                                        v-model="perm.unidade_id"
                                        :options="unidadeOptions"
                                        :error="form.errors[`permissoes.${index}.unidade_id`]"
                                        @update:modelValue="perm.setor_id = null"
                                    />
                                    <SelectField
                                        :id="`perm-setor-${index}`"
                                        label="Setor (opcional)"
                                        v-model="perm.setor_id"
                                        :options="setorOptions(perm.unidade_id)"
                                        :error="form.errors[`permissoes.${index}.setor_id`]"
                                    />
                                </div>
                                <button
                                    type="button"
                                    class="mt-6 text-gray-400 hover:text-red-500 transition-colors"
                                    @click="removePermissao(index)"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <Link :href="route('tenant.usuarios.index')">
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
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputField from '@/Components/InputField.vue';
import SelectField from '@/Components/SelectField.vue';
import Button from '@/Components/Button.vue';

const props = defineProps({
    unidades: Array,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'LEADER',
    is_active: true,
    permissoes: [],
});

const roles = [
    { value: 'RH', label: 'RH' },
    { value: 'LEADER', label: 'Líder' },
];

const unidadeOptions = computed(() => [
    { value: '', label: 'Selecione uma unidade' },
    ...(props.unidades ?? []).map(u => ({ value: u.id, label: u.nome })),
]);

const setorOptions = (unidadeId) => {
    if (!unidadeId) return [{ value: '', label: 'Selecione uma unidade primeiro' }];
    const unidade = (props.unidades ?? []).find(u => u.id == unidadeId);
    return [
        { value: '', label: 'Todos os setores desta unidade' },
        ...(unidade?.setores ?? []).map(s => ({ value: s.id, label: s.nome })),
    ];
};

const addPermissao = () => {
    form.permissoes.push({ unidade_id: '', setor_id: null });
};

const removePermissao = (index) => {
    form.permissoes.splice(index, 1);
};

const submit = () => form.post(route('tenant.usuarios.store'));
</script>
