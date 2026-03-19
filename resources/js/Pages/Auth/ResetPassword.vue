<template>
    <AuthLayout>
        <div>
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Nova senha</h2>
                <p class="text-gray-500 text-sm mt-1">Escolha uma senha forte para sua conta</p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <InputField
                    id="email"
                    label="Email"
                    type="email"
                    v-model="form.email"
                    :error="form.errors.email"
                    readonly
                />

                <InputField
                    id="password"
                    label="Nova senha"
                    type="password"
                    v-model="form.password"
                    :error="form.errors.password"
                    placeholder="Mínimo 8 caracteres"
                    required
                />

                <InputField
                    id="password_confirmation"
                    label="Confirmar senha"
                    type="password"
                    v-model="form.password_confirmation"
                    :error="form.errors.password_confirmation"
                    placeholder="Repita a senha"
                    required
                />

                <Button type="submit" variant="primary" :loading="form.processing" class="w-full justify-center">
                    Redefinir senha
                </Button>
            </form>
        </div>
    </AuthLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputField from '@/Components/InputField.vue';
import Button from '@/Components/Button.vue';

defineOptions({ layout: null });

const props = defineProps({
    token: String,
    email: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => form.post(route('password.update'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
});
</script>
