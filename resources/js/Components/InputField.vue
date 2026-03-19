<template>
    <div>
        <label v-if="label" :for="id" class="form-label">
            {{ label }}
            <span v-if="required" class="text-red-500 ml-0.5">*</span>
        </label>
        <div class="relative">
            <div v-if="$slots.prefix" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <slot name="prefix" />
            </div>
            <input
                :id="id"
                v-bind="$attrs"
                :type="type"
                :value="modelValue"
                :class="[
                    'form-input',
                    $slots.prefix ? 'pl-10' : '',
                    $slots.suffix ? 'pr-10' : '',
                    error ? 'border-red-400 focus:border-red-500 focus:ring-red-500/20' : ''
                ]"
                @input="$emit('update:modelValue', $event.target.value)"
            />
            <div v-if="$slots.suffix" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <slot name="suffix" />
            </div>
        </div>
        <p v-if="error" class="form-error">{{ error }}</p>
        <p v-if="hint && !error" class="text-xs text-gray-500 mt-1">{{ hint }}</p>
    </div>
</template>

<script setup>
defineProps({
    id: String,
    label: String,
    type: { type: String, default: 'text' },
    modelValue: [String, Number],
    error: String,
    hint: String,
    required: Boolean,
});

defineEmits(['update:modelValue']);
defineOptions({ inheritAttrs: false });
</script>
