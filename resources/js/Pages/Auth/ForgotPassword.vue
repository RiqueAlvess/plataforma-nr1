<template>
    <AuthLayout>
        <div>
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Recuperar senha</h2>
                <p class="text-gray-500 text-sm mt-1">Digite seu email para receber o link de recuperação</p>
            </div>

            <Alert v-if="page.props.flash.status" type="success" :message="page.props.flash.status" class="mb-4" />

            <form v-if="!page.props.flash.status" @submit.prevent="submit" class="space-y-4">
                <InputField
                    id="email"
                    label="Email"
                    type="email"
                    v-model="form.email"
                    :error="form.errors.email"
                    placeholder="seu@email.com"
                    required
                >
                    <template #prefix>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </template>
                </InputField>

                <Button type="submit" variant="primary" :loading="form.processing" class="w-full justify-center">
                    Enviar link de recuperação
                </Button>
            </form>

            <div class="mt-4 text-center">
                <Link :href="loginPath" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                    ← Voltar ao login
                </Link>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import { useForm, usePage, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputField from '@/Components/InputField.vue';
import Button from '@/Components/Button.vue';
import Alert from '@/Components/Alert.vue';

defineOptions({ layout: null });

const props = defineProps({
    passwordEmailPath: String,
    loginPath: String,
});

const page = usePage();
const form = useForm({ email: '' });
const submit = () => form.post(props.passwordEmailPath);
</script>
