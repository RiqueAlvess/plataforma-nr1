<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        :class="[
            'btn',
            variantClasses[variant],
            sizeClasses[size],
        ]"
    >
        <svg v-if="loading" class="animate-spin -ml-1 w-4 h-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <slot />
    </button>
</template>

<script setup>
defineProps({
    type: { type: String, default: 'button' },
    variant: { type: String, default: 'primary' },
    size: { type: String, default: 'md' },
    disabled: Boolean,
    loading: Boolean,
});

const variantClasses = {
    primary: 'btn-primary',
    secondary: 'btn-secondary',
    danger: 'btn-danger',
    ghost: 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-lg px-4 py-2 text-sm font-medium transition-colors',
};

const sizeClasses = {
    sm: 'btn-sm',
    md: '',
    lg: 'px-6 py-3 text-base',
};
</script>
