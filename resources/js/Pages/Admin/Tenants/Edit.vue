<template>
    <AppLayout :title="`Editar: ${tenant.company_name}`" subtitle="Atualizar dados da empresa">
        <div class="max-w-2xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-gray-900">Dados da Empresa</h3>
                </div>
                <div class="card-body">
                    <form @submit.prevent="submit" class="space-y-5">
                        <InputField
                            id="company_name"
                            label="Nome da Empresa"
                            v-model="form.company_name"
                            :error="form.errors.company_name"
                            required
                        />

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <InputField
                                id="cnpj"
                                label="CNPJ"
                                v-model="form.cnpj"
                                :error="form.errors.cnpj"
                                placeholder="XX.XXX.XXX/XXXX-XX"
                                required
                            />
                            <InputField
                                id="cnae"
                                label="CNAE"
                                v-model="form.cnae"
                                :error="form.errors.cnae"
                            />
                        </div>

                        <InputField
                            id="responsible_email"
                            label="Email do Responsável"
                            type="email"
                            v-model="form.responsible_email"
                            :error="form.errors.responsible_email"
                            required
                        />

                        <div class="flex items-center gap-3">
                            <input
                                id="is_active"
                                type="checkbox"
                                v-model="form.is_active"
                                class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <label for="is_active" class="text-sm font-medium text-gray-700">Tenant ativo</label>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <Link :href="route('admin.tenants.show', tenant.id)">
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
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputField from '@/Components/InputField.vue';
import Button from '@/Components/Button.vue';

const props = defineProps({
    tenant: Object,
});

const form = useForm({
    company_name: props.tenant.company_name,
    cnpj: props.tenant.cnpj,
    cnae: props.tenant.cnae || '',
    responsible_email: props.tenant.responsible_email,
    is_active: props.tenant.is_active,
});

const submit = () => form.put(route('admin.tenants.update', props.tenant.id));
</script>
