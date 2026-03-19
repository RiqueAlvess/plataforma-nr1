<template>
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ label }}</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ value }}</p>
                <div v-if="change !== undefined" class="flex items-center gap-1 mt-2">
                    <span :class="change >= 0 ? 'text-green-600' : 'text-red-600'" class="text-xs font-medium">
                        {{ change >= 0 ? '+' : '' }}{{ change }}%
                    </span>
                    <span class="text-xs text-gray-400">vs mês anterior</span>
                </div>
            </div>
            <div :class="['w-12 h-12 rounded-xl flex items-center justify-center', iconBg]">
                <slot name="icon" />
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    label: String,
    value: [String, Number],
    change: Number,
    iconBg: { type: String, default: 'bg-indigo-100' },
});
</script>
