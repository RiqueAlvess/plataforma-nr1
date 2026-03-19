<template>
    <AppLayout title="Dashboard" :subtitle="stats.welcome_message">
        <!-- Stats grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <StatCard label="Status" value="Ativo" icon-bg="bg-green-100">
                <template #icon>
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>
            </StatCard>

            <StatCard label="Seu Perfil" :value="roleLabel" icon-bg="bg-indigo-100">
                <template #icon>
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </template>
            </StatCard>

            <StatCard label="Sistema" value="NR1" icon-bg="bg-purple-100">
                <template #icon>
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                </template>
            </StatCard>

            <StatCard label="Versão" value="1.0.0" icon-bg="bg-blue-100">
                <template #icon>
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </template>
            </StatCard>
        </div>

        <!-- Welcome card -->
        <div class="card p-8 bg-gradient-to-br from-indigo-600 to-purple-700 border-0 text-white">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold mb-1">{{ stats.welcome_message }}</h2>
                    <p class="text-indigo-200 text-sm">
                        Você está autenticado como
                        <strong class="text-white">{{ roleLabel }}</strong>.
                        Use o menu lateral para navegar pelo sistema.
                    </p>
                </div>
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Quick actions for admin -->
        <div v-if="isGlobalAdmin" class="mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ações Rápidas</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <Link :href="route('admin.tenants.create')" class="card p-5 hover:shadow-md transition-shadow group cursor-pointer block">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Novo Tenant</p>
                            <p class="text-gray-500 text-xs">Criar nova empresa</p>
                        </div>
                    </div>
                </Link>

                <Link :href="route('admin.users.create')" class="card p-5 hover:shadow-md transition-shadow group cursor-pointer block">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Novo Usuário</p>
                            <p class="text-gray-500 text-xs">Adicionar usuário ao sistema</p>
                        </div>
                    </div>
                </Link>

                <Link :href="route('admin.dashboard')" class="card p-5 hover:shadow-md transition-shadow group cursor-pointer block">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Painel Admin</p>
                            <p class="text-gray-500 text-xs">Gerenciar o sistema</p>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';

const props = defineProps({
    stats: Object,
});

const page = usePage();
const isGlobalAdmin = computed(() => page.props.auth.user?.role === 'GLOBAL_ADMIN');

const roleLabel = computed(() => {
    const roles = {
        GLOBAL_ADMIN: 'Administrador Global',
        RH: 'Recursos Humanos',
        LEADER: 'Líder',
    };
    return roles[page.props.auth.user?.role] || '';
});
</script>
