<template>
    <span :class="['badge', variantClasses[variant]]">
        <slot />
    </span>
</template>

<script setup>
defineProps({
    variant: { type: String, default: 'info' },
});

const variantClasses = {
    success: 'badge-success',
    danger: 'badge-danger',
    warning: 'badge-warning',
    info: 'badge-info',
    primary: 'badge-primary',
};
</script>
