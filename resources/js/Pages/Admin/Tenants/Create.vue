<template>
    <AppLayout title="Novo Tenant" subtitle="Cadastrar nova empresa">
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
                            placeholder="Ex: Empresa Exemplo Ltda"
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
                                placeholder="Ex: 7490-1/04"
                            />
                        </div>

                        <InputField
                            id="responsible_email"
                            label="Email do Responsável"
                            type="email"
                            v-model="form.responsible_email"
                            :error="form.errors.responsible_email"
                            placeholder="responsavel@empresa.com"
                            required
                        />

                        <InputField
                            id="domain"
                            label="Domínio"
                            v-model="form.domain"
                            :error="form.errors.domain"
                            placeholder="empresa.sistema.com"
                            hint="O domínio pelo qual os usuários desta empresa acessarão o sistema."
                        />

                        <div class="flex items-center gap-3 pt-2">
                            <Link :href="route('admin.tenants.index')">
                                <Button variant="secondary">Cancelar</Button>
                            </Link>
                            <Button type="submit" variant="primary" :loading="form.processing">
                                Criar Tenant
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

const form = useForm({
    company_name: '',
    cnpj: '',
    cnae: '',
    responsible_email: '',
    domain: '',
    is_active: true,
});

const submit = () => form.post(route('admin.tenants.store'));
</script>
