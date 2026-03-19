<template>
    <Link
        :href="href"
        :class="[
            'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150',
            active
                ? 'bg-white/15 text-white'
                : 'text-indigo-200 hover:bg-white/8 hover:text-white'
        ]"
    >
        <span class="flex-shrink-0">
            <slot name="icon" />
        </span>
        <slot />
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: String,
    active: Boolean,
});
</script>
