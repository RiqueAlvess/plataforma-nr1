<template>
    <div class="min-h-screen flex bg-gray-50">
        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-indigo-950 to-indigo-900 transform transition-transform duration-300 ease-in-out',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
        >
            <!-- Logo -->
            <div class="flex items-center gap-3 px-6 py-6 border-b border-white/10">
                <div class="w-9 h-9 bg-indigo-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-white font-semibold text-sm leading-tight">Plataforma NR1</h2>
                    <p class="text-indigo-400 text-xs">{{ tenantName }}</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="px-3 py-4 flex-1 overflow-y-auto">
                <div class="space-y-1">
                    <NavItem :href="dashboardRoute" :active="isRoute('dashboard') || isRoute('tenant.dashboard')">
                        <template #icon>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </template>
                        Dashboard
                    </NavItem>

                    <!-- RH Only -->
                    <template v-if="isRh">
                        <div class="pt-4 pb-2">
                            <p class="text-xs font-semibold text-indigo-400 uppercase tracking-wider px-3">Gestão</p>
                        </div>
                        <NavItem :href="route('tenant.campanhas.index')" :active="isRoute('tenant.campanhas.*')">
                            <template #icon>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </template>
                            Campanhas
                        </NavItem>
                        <NavItem :href="route('tenant.importacao.index')" :active="isRoute('tenant.importacao.*')">
                            <template #icon>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            </template>
                            Importação CSV
                        </NavItem>
                        <NavItem :href="route('tenant.usuarios.index')" :active="isRoute('tenant.usuarios.*')">
                            <template #icon>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13 5.197v-1a6 6 0 00-5-5.917"/>
                                </svg>
                            </template>
                            Usuários
                        </NavItem>
                    </template>

                    <!-- Leader Only -->
                    <template v-if="isLeader">
                        <div class="pt-4 pb-2">
                            <p class="text-xs font-semibold text-indigo-400 uppercase tracking-wider px-3">Meu Painel</p>
                        </div>
                        <NavItem :href="dashboardRoute" :active="isRoute('tenant.dashboard')">
                            <template #icon>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </template>
                            Análises
                        </NavItem>
                    </template>

                    <!-- Admin Only -->
                    <template v-if="isGlobalAdmin">
                        <div class="pt-4 pb-2">
                            <p class="text-xs font-semibold text-indigo-400 uppercase tracking-wider px-3">Administração</p>
                        </div>
                        <NavItem :href="route('admin.dashboard')" :active="isRoute('admin.*')">
                            <template #icon>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </template>
                            Painel Admin
                        </NavItem>
                        <NavItem :href="route('admin.tenants.index')" :active="isRoute('admin.tenants.*')">
                            <template #icon>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </template>
                            Tenants
                        </NavItem>
                        <NavItem :href="route('admin.users.index')" :active="isRoute('admin.users.*')">
                            <template #icon>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13 5.197v-1a6 6 0 00-5-5.917"/>
                                </svg>
                            </template>
                            Usuários
                        </NavItem>
                    </template>
                </div>
            </nav>

            <!-- User info at bottom -->
            <div class="p-4 border-t border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-indigo-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-sm font-semibold">{{ userInitials }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-medium truncate">{{ $page.props.auth.user?.name }}</p>
                        <p class="text-indigo-400 text-xs truncate">{{ roleLabel }}</p>
                    </div>
                    <button @click="logout" class="text-indigo-400 hover:text-white transition-colors" title="Sair">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Sidebar overlay (mobile) -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        <!-- Main content -->
        <div class="flex-1 lg:pl-64">
            <!-- Top bar -->
            <header class="sticky top-0 z-30 bg-white border-b border-gray-200 px-4 sm:px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900">{{ title }}</h1>
                            <p v-if="subtitle" class="text-sm text-gray-500">{{ subtitle }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="hidden sm:flex items-center gap-2 text-sm text-gray-600">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <span class="text-indigo-700 text-xs font-semibold">{{ userInitials }}</span>
                            </div>
                            <span class="font-medium">{{ $page.props.auth.user?.name }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import NavItem from '@/Components/NavItem.vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Dashboard',
    },
    subtitle: {
        type: String,
        default: null,
    },
});

const page = usePage();
const sidebarOpen = ref(false);

const isGlobalAdmin = computed(() => page.props.auth.user?.role === 'GLOBAL_ADMIN');
const isRh = computed(() => page.props.auth.user?.role === 'RH');
const isLeader = computed(() => page.props.auth.user?.role === 'LEADER');

const dashboardRoute = computed(() => {
    if (isGlobalAdmin.value) {
        return route('admin.dashboard');
    }
    try {
        return route('tenant.dashboard');
    } catch {
        return route('dashboard');
    }
});
const logoutRouteName = computed(() => {
    try {
        route('tenant.logout');
        return 'tenant.logout';
    } catch {
        return 'logout';
    }
});

const tenantName = computed(() => {
    if (isGlobalAdmin.value) return 'Admin Global';
    return page.props.auth.user?.tenant_name || 'Empresa';
});

const userInitials = computed(() => {
    const name = page.props.auth.user?.name || '';
    return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
});

const roleLabel = computed(() => {
    const roles = {
        GLOBAL_ADMIN: 'Administrador Global',
        RH: 'Recursos Humanos',
        LEADER: 'Líder',
    };
    return roles[page.props.auth.user?.role] || '';
});

const isRoute = (pattern) => {
    try {
        return route().current(pattern);
    } catch {
        return false;
    }
};

const logout = () => {
    router.post(route(logoutRouteName.value));
};
</script>
