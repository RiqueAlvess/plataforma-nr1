<template>
    <AppLayout title="Nova Importação CSV" subtitle="Enviar arquivo CSV com registros organizacionais">
        <div class="max-w-2xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-gray-900">Upload de Arquivo CSV</h3>
                </div>
                <div class="card-body space-y-6">
                    <!-- Instruções -->
                    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
                        <h4 class="text-sm font-semibold text-blue-800 mb-2">Formato esperado do CSV</h4>
                        <p class="text-sm text-blue-700 mb-3">O arquivo CSV deve conter as seguintes colunas (na primeira linha):</p>
                        <div class="flex gap-2 flex-wrap mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-mono font-medium bg-blue-100 text-blue-800">UNIDADE</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-mono font-medium bg-blue-100 text-blue-800">SETOR</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-mono font-medium bg-blue-100 text-blue-800">EMAIL</span>
                        </div>
                        <div class="text-xs text-blue-600 font-mono bg-blue-100 rounded p-2 overflow-x-auto">
                            <div>UNIDADE,SETOR,EMAIL</div>
                            <div>Unidade Norte,Recursos Humanos,joao@empresa.com</div>
                            <div>Unidade Sul,TI,maria@empresa.com</div>
                        </div>
                        <ul class="mt-3 text-xs text-blue-700 space-y-1 list-disc list-inside">
                            <li>Colunas são case-insensitive (UNIDADE = Unidade = unidade)</li>
                            <li>Unidades e setores serão criados automaticamente se não existirem</li>
                            <li>Tamanho máximo do arquivo: 10MB</li>
                        </ul>
                    </div>

                    <!-- Formulário -->
                    <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Arquivo CSV <span class="text-red-500">*</span>
                            </label>
                            <div
                                class="border-2 border-dashed rounded-lg p-6 text-center transition-colors"
                                :class="dragover ? 'border-indigo-400 bg-indigo-50' : 'border-gray-300 hover:border-gray-400'"
                                @dragover.prevent="dragover = true"
                                @dragleave.prevent="dragover = false"
                                @drop.prevent="onDrop"
                            >
                                <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <p class="text-sm text-gray-600 mb-1">
                                    <span v-if="!selectedFile">Arraste um arquivo CSV ou </span>
                                    <span v-else class="font-medium text-indigo-600">{{ selectedFile.name }}</span>
                                </p>
                                <label v-if="!selectedFile" class="cursor-pointer text-sm font-medium text-indigo-600 hover:text-indigo-700">
                                    clique para selecionar
                                    <input type="file" accept=".csv,.txt" class="sr-only" @change="onFileChange" />
                                </label>
                                <button v-else type="button" class="text-xs text-gray-500 hover:text-red-500 mt-1" @click="clearFile">
                                    Remover arquivo
                                </button>
                            </div>
                            <p v-if="form.errors.arquivo" class="mt-1 text-xs text-red-600">{{ form.errors.arquivo }}</p>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <Link :href="route('tenant.importacao.index')">
                                <Button variant="secondary">Cancelar</Button>
                            </Link>
                            <Button type="submit" variant="primary" :loading="form.processing" :disabled="!selectedFile">
                                Importar CSV
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/Button.vue';

const form = useForm({ arquivo: null });
const selectedFile = ref(null);
const dragover = ref(false);

const onFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        selectedFile.value = file;
        form.arquivo = file;
    }
};

const onDrop = (e) => {
    dragover.value = false;
    const file = e.dataTransfer.files[0];
    if (file && (file.name.endsWith('.csv') || file.name.endsWith('.txt'))) {
        selectedFile.value = file;
        form.arquivo = file;
    }
};

const clearFile = () => {
    selectedFile.value = null;
    form.arquivo = null;
};

const submit = () => {
    form.post(route('tenant.importacao.store'));
};
</script>
