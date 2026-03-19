<template>
    <div>
        <label v-if="label" :for="id" class="form-label">
            {{ label }}
            <span v-if="required" class="text-red-500 ml-0.5">*</span>
        </label>
        <select
            :id="id"
            :value="modelValue"
            :class="['form-input', error ? 'border-red-400 focus:border-red-500' : '']"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
            <option v-for="option in options" :key="option.value" :value="option.value">
                {{ option.label }}
            </option>
        </select>
        <p v-if="error" class="form-error">{{ error }}</p>
    </div>
</template>

<script setup>
defineProps({
    id: String,
    label: String,
    modelValue: [String, Number],
    options: Array,
    placeholder: String,
    error: String,
    required: Boolean,
});

defineEmits(['update:modelValue']);
</script>
