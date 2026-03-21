<template>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-blue-50 py-8 px-4">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-700 to-indigo-600 rounded-t-2xl px-8 py-5">
                <h1 class="text-white font-bold text-lg">Pesquisa de Clima Organizacional</h1>
                <p class="text-indigo-200 text-sm mt-0.5">{{ campanha }}</p>
                <!-- Progress -->
                <div class="mt-3">
                    <div class="flex items-center justify-between text-xs text-indigo-300 mb-1">
                        <span>Progresso</span>
                        <span>{{ respostasPreenchidas }}/{{ totalPerguntas }}</span>
                    </div>
                    <div class="w-full bg-indigo-900/40 rounded-full h-1.5">
                        <div
                            class="bg-indigo-300 h-1.5 rounded-full transition-all"
                            :style="{ width: progresso + '%' }"
                        ></div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded-b-2xl shadow-lg">
                <!-- Demographic section -->
                <div class="px-8 py-6 border-b border-gray-100">
                    <h2 class="text-gray-800 font-semibold text-base mb-4">Dados Demográficos (opcional)</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gênero</label>
                            <select v-model="form.genero" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none">
                                <option value="">Prefiro não informar</option>
                                <option value="masculino">Masculino</option>
                                <option value="feminino">Feminino</option>
                                <option value="outro">Outro</option>
                                <option value="nao_informado">Prefiro não dizer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Faixa Etária</label>
                            <select v-model="form.faixa_etaria" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none">
                                <option value="">Prefiro não informar</option>
                                <option value="18-24">18 a 24 anos</option>
                                <option value="25-34">25 a 34 anos</option>
                                <option value="35-44">35 a 44 anos</option>
                                <option value="45-54">45 a 54 anos</option>
                                <option value="55+">55 anos ou mais</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Questions by dimension -->
                <div v-for="(label, chave) in dimensoes" :key="chave" class="px-8 py-6 border-b border-gray-100">
                    <h2 class="text-indigo-700 font-semibold text-sm uppercase tracking-wide mb-4">{{ label }}</h2>
                    <div class="space-y-6">
                        <div v-for="pergunta in getPerguntasDaDimensao(chave)" :key="pergunta.numero" class="space-y-2">
                            <p class="text-sm text-gray-700 leading-relaxed">
                                <span class="font-medium text-gray-400 mr-2">{{ pergunta.numero }}.</span>
                                {{ pergunta.texto }}
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <label
                                    v-for="(rotulo, valor) in opcoes"
                                    :key="valor"
                                    class="flex items-center gap-1.5 cursor-pointer"
                                >
                                    <input
                                        type="radio"
                                        :name="`q_${pergunta.numero}`"
                                        :value="Number(valor)"
                                        v-model="form.respostas[pergunta.numero]"
                                        class="text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                    />
                                    <span class="text-xs text-gray-600 whitespace-nowrap">{{ rotulo }}</span>
                                </label>
                            </div>
                            <p v-if="erros[`respostas.${pergunta.numero}`]" class="text-xs text-red-500">
                                Esta pergunta é obrigatória.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="px-8 py-6">
                    <p v-if="errosGlobais" class="text-sm text-red-600 mb-3">
                        Por favor, responda todas as perguntas antes de enviar.
                    </p>
                    <button
                        type="submit"
                        :disabled="enviando"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-semibold py-3 px-6 rounded-xl transition-colors"
                    >
                        <span v-if="enviando">Enviando…</span>
                        <span v-else>Enviar Respostas</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    token: String,
    campanha: String,
    perguntas: Array,
    dimensoes: Object,
    opcoes: Object,
});

const form = reactive({
    genero: '',
    faixa_etaria: '',
    respostas: {},
});

const enviando = ref(false);
const erros = ref({});
const errosGlobais = ref(false);

const totalPerguntas = computed(() => props.perguntas.length);
const respostasPreenchidas = computed(() => Object.keys(form.respostas).length);
const progresso = computed(() => Math.round((respostasPreenchidas.value / totalPerguntas.value) * 100));

const getPerguntasDaDimensao = (dimensao) => props.perguntas.filter(p => p.dimensao === dimensao);

const submit = () => {
    erros.value = {};
    errosGlobais.value = false;

    // Validate all answered
    const unanswered = props.perguntas.filter(p => form.respostas[p.numero] === undefined);
    if (unanswered.length > 0) {
        unanswered.forEach(p => { erros.value[`respostas.${p.numero}`] = true; });
        errosGlobais.value = true;
        // Scroll to first error
        const first = document.querySelector('[name="q_' + unanswered[0].numero + '"]');
        if (first) first.closest('.space-y-2').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    enviando.value = true;

    router.post(route('pesquisa.responder', { token: props.token }), {
        genero: form.genero || null,
        faixa_etaria: form.faixa_etaria || null,
        respostas: form.respostas,
    }, {
        onError: (e) => { erros.value = e; errosGlobais.value = true; },
        onFinish: () => { enviando.value = false; },
    });
};
</script>
