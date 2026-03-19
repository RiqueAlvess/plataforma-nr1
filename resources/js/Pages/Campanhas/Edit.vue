<template>
    <AppLayout title="Editar Campanha" subtitle="Alterar dados da campanha">
        <div class="max-w-2xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-gray-900">Editar: {{ campaign.nome }}</h3>
                </div>
                <form @submit.prevent="submit" class="p-6 space-y-5">
                    <InputField
                        label="Nome da Campanha"
                        v-model="form.nome"
                        :error="form.errors.nome"
                        required
                    />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                        <textarea
                            v-model="form.descricao"
                            rows="4"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none"
                        ></textarea>
                        <p v-if="form.errors.descricao" class="mt-1 text-sm text-red-600">{{ form.errors.descricao }}</p>
                    </div>

                    <SelectField
                        label="Status"
                        v-model="form.status"
                        :error="form.errors.status"
                        :options="statusOptions"
                    />

                    <div class="flex justify-end gap-3 pt-2">
                        <Link :href="route('tenant.campanhas.show', campaign.id)">
                            <Button variant="secondary" type="button">Cancelar</Button>
                        </Link>
                        <Button variant="primary" type="submit" :loading="form.processing">Salvar Alterações</Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputField from '@/Components/InputField.vue';
import SelectField from '@/Components/SelectField.vue';
import Button from '@/Components/Button.vue';

const props = defineProps({ campaign: Object });

const form = useForm({
    nome: props.campaign.nome,
    descricao: props.campaign.descricao ?? '',
    status: props.campaign.status,
});

const statusOptions = [
    { value: 'rascunho', label: 'Rascunho' },
    { value: 'ativa', label: 'Ativa' },
    { value: 'encerrada', label: 'Encerrada' },
];

const submit = () => {
    form.put(route('tenant.campanhas.update', props.campaign.id));
};
</script>
