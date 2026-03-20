<template>
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">{{ label }}</p>
                <p :class="['text-3xl font-bold mt-1', colorClasses[color]]">{{ value }}</p>
            </div>
            <div v-if="$slots.icon || icon" class="w-12 h-12 rounded-xl flex items-center justify-center bg-gray-100">
                <slot name="icon" />
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    label: String,
    value: [String, Number],
    color: { type: String, default: 'indigo' },
    icon: String,
});

const colorClasses = {
    indigo: 'text-indigo-600',
    green: 'text-green-600',
    blue: 'text-blue-600',
    red: 'text-red-600',
    purple: 'text-purple-600',
    amber: 'text-amber-600',
};
</script>
