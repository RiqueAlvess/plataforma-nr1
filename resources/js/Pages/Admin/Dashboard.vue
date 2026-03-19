<template>
    <AppLayout title="Painel Administrativo" subtitle="Visão geral do sistema">
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <StatCard label="Total de Tenants" :value="stats.total_tenants" icon-bg="bg-indigo-100">
                <template #icon>
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </template>
            </StatCard>

            <StatCard label="Tenants Ativos" :value="stats.active_tenants" icon-bg="bg-green-100">
                <template #icon>
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>
            </StatCard>

            <StatCard label="Total de Usuários" :value="stats.total_users" icon-bg="bg-blue-100">
                <template #icon>
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13 5.197v-1a6 6 0 00-5-5.917"/>
                    </svg>
                </template>
            </StatCard>

            <StatCard label="Contas Bloqueadas" :value="stats.locked_users" icon-bg="bg-red-100">
                <template #icon>
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </template>
            </StatCard>
        </div>

        <!-- Recent tenants -->
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Tenants Recentes</h3>
                <Link :href="route('admin.tenants.index')" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                    Ver todos →
                </Link>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Empresa</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">CNPJ</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Domínios</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <tr v-for="tenant in recent_tenants" :key="tenant.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <Link :href="route('admin.tenants.show', tenant.id)" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                                    {{ tenant.company_name }}
                                </Link>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ tenant.cnpj }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span v-for="domain in tenant.domains" :key="domain.id" class="inline-block mr-1">
                                    <Badge variant="info">{{ domain.domain }}</Badge>
                                </span>
                                <span v-if="!tenant.domains?.length" class="text-gray-400">—</span>
                            </td>
                            <td class="px-6 py-4">
                                <Badge :variant="tenant.is_active ? 'success' : 'danger'">
                                    {{ tenant.is_active ? 'Ativo' : 'Inativo' }}
                                </Badge>
                            </td>
                        </tr>
                        <tr v-if="!recent_tenants?.length">
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm">Nenhum tenant cadastrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import Badge from '@/Components/Badge.vue';

defineProps({
    stats: Object,
    recent_tenants: Array,
});
</script>
