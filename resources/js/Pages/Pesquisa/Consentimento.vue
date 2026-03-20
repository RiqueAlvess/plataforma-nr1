<template>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-blue-50 flex items-center justify-center p-4">
        <div class="max-w-2xl w-full bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-700 to-indigo-600 px-8 py-6">
                <h1 class="text-white text-xl font-bold">Plataforma NR1 – Vivamente360</h1>
                <p class="text-indigo-200 text-sm mt-1">{{ campanha }}</p>
            </div>

            <!-- Content -->
            <div class="px-8 py-6">
                <h2 class="text-gray-900 text-lg font-semibold mb-4">
                    Termo de Consentimento para Tratamento de Dados – Pesquisa de Clima Organizacional
                </h2>

                <div class="prose prose-sm text-gray-600 space-y-4 max-h-80 overflow-y-auto border border-gray-200 rounded-lg p-4 mb-6 text-sm leading-relaxed">
                    <p>
                        Este documento visa registrar a manifestação livre, informada e inequívoca pela qual o Titular
                        concorda com o tratamento de seus dados pessoais para uma finalidade específica, em estrita
                        conformidade com a Lei Geral de Proteção de Dados Pessoais (Lei nº 13.709/2018 - LGPD).
                    </p>

                    <p><strong>1. Objetivo da Coleta</strong><br>
                        A presente pesquisa, realizada por meio da plataforma Vivamente360, tem como objetivo avaliar
                        o clima organizacional e mapear fatores psicossociais no ambiente de trabalho. As informações
                        coletadas servirão exclusivamente para embasar diagnósticos e desenvolver ações de melhoria
                        contínua voltadas ao bem-estar, saúde e engajamento dos colaboradores.
                    </p>

                    <p><strong>2. Dados Coletados</strong><br>
                        Para a realização da pesquisa poderão ser tratados dados demográficos e percepções relacionadas
                        ao ambiente de trabalho.
                    </p>

                    <p><strong>3. Tratamento e Anonimização</strong><br>
                        Os relatórios serão apresentados apenas de forma agregada e anonimizada.
                    </p>

                    <p><strong>4. Armazenamento e Segurança</strong><br>
                        Os dados serão armazenados em ambientes seguros com controle de acesso restrito.
                    </p>

                    <p><strong>5. Direitos do Titular</strong><br>
                        O titular poderá solicitar acesso, correção ou eliminação dos dados conforme a LGPD.
                    </p>
                </div>

                <p class="text-sm font-semibold text-gray-700 mb-4">Declaração de Consentimento:</p>

                <div class="space-y-3">
                    <Link :href="route('pesquisa.questionario', token)">
                        <button class="w-full flex items-center gap-3 p-4 rounded-xl border-2 border-indigo-200 bg-indigo-50 hover:border-indigo-400 hover:bg-indigo-100 transition-colors text-left">
                            <div class="w-5 h-5 rounded border-2 border-indigo-500 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-indigo-700 text-sm">Li e concordo</p>
                                <p class="text-xs text-indigo-500">Iniciar pesquisa</p>
                            </div>
                        </button>
                    </Link>

                    <button
                        @click="recusar"
                        class="w-full flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-colors text-left"
                    >
                        <div class="w-5 h-5 rounded border-2 border-gray-400 flex-shrink-0"></div>
                        <div>
                            <p class="font-semibold text-gray-600 text-sm">Não concordo</p>
                            <p class="text-xs text-gray-400">A pesquisa não será iniciada</p>
                        </div>
                    </button>
                </div>

                <div v-if="recusado" class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700">
                    Tudo bem! Sua participação é voluntária. Você pode fechar esta janela.
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    token: String,
    campanha: String,
});

const recusado = ref(false);
const recusar = () => { recusado.value = true; };
</script>
