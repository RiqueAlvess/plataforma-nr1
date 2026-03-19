<template>
    <AppLayout :title="tenant.company_name" subtitle="Detalhes do tenant">
        <Alert v-if="page.props.flash.success" type="success" :message="page.props.flash.success" class="mb-6" />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="card">
                    <div class="card-header flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">Informações da Empresa</h3>
                        <Link :href="route('admin.tenants.edit', tenant.id)">
                            <Button size="sm" variant="secondary">Editar</Button>
                        </Link>
                    </div>
                    <div class="card-body">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ tenant.company_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">CNPJ</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ tenant.cnpj }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">CNAE</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ tenant.cnae || '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Email Responsável</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ tenant.responsible_email }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</dt>
                                <dd class="mt-1">
                                    <Badge :variant="tenant.is_active ? 'success' : 'danger'">
                                        {{ tenant.is_active ? 'Ativo' : 'Inativo' }}
                                    </Badge>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</dt>
                                <dd class="mt-1 text-sm text-gray-500 font-mono">{{ tenant.id }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Domains -->
            <div class="space-y-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-base font-semibold text-gray-900">Domínios</h3>
                    </div>
                    <div class="card-body">
                        <div v-if="tenant.domains?.length" class="space-y-2">
                            <div v-for="domain in tenant.domains" :key="domain.id"
                                class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                                <span class="text-sm text-gray-700 font-medium">{{ domain.domain }}</span>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-400">Nenhum domínio configurado.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <Link :href="route('admin.tenants.index')">
                <Button variant="secondary">← Voltar</Button>
            </Link>
        </div>
    </AppLayout>
</template>

<script setup>
import { usePage, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import Alert from '@/Components/Alert.vue';

defineProps({ tenant: Object });
const page = usePage();
</script>
