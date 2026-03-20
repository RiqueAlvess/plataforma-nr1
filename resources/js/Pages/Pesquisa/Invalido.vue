<template>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-blue-50 flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-8 text-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-3">Link inválido</h1>
            <p class="text-gray-600 leading-relaxed mb-4">{{ motivo }}</p>
            <p class="text-sm text-gray-400">
                Se você acredita que isso é um erro, entre em contato com o RH da sua empresa.
            </p>
        </div>
    </div>
</template>

<script setup>
defineOptions({ layout: null });
defineProps({ motivo: String });
</script>
