<template>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-blue-50 flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-8 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-3">Pesquisa concluída!</h1>
            <p class="text-gray-600 leading-relaxed">
                Obrigado pela sua participação. Suas respostas foram registradas de forma anônima e contribuirão para melhorar o ambiente de trabalho.
            </p>
            <p class="text-sm text-gray-400 mt-6">
                Você pode fechar esta janela.
            </p>
        </div>
    </div>
</template>

<script setup>
defineOptions({ layout: null });
</script>
