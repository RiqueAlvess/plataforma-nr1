<template>
    <AuthLayout>
        <div>
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Bem-vindo de volta</h2>
                <p class="text-gray-500 text-sm mt-1">Entre na sua conta para continuar</p>
            </div>

            <Alert v-if="page.props.flash.status" type="info" :message="page.props.flash.status" class="mb-4" />

            <form @submit.prevent="submit" class="space-y-4">
                <InputField
                    id="email"
                    label="Email"
                    type="email"
                    v-model="form.email"
                    :error="form.errors.email"
                    placeholder="seu@email.com"
                    required
                    autocomplete="email"
                >
                    <template #prefix>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </template>
                </InputField>

                <div>
                    <InputField
                        id="password"
                        label="Senha"
                        :type="showPassword ? 'text' : 'password'"
                        v-model="form.password"
                        :error="form.errors.password"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    >
                        <template #prefix>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </template>
                        <template #suffix>
                            <button type="button" @click="showPassword = !showPassword" class="text-gray-400 hover:text-gray-600">
                                <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </template>
                    </InputField>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            v-model="form.remember"
                            class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <span class="text-sm text-gray-600">Lembrar de mim</span>
                    </label>
                    <Link :href="forgotPasswordPath" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                        Esqueceu a senha?
                    </Link>
                </div>

                <Button
                    type="submit"
                    variant="primary"
                    :loading="form.processing"
                    class="w-full justify-center mt-2"
                >
                    Entrar
                </Button>
            </form>
        </div>
    </AuthLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputField from '@/Components/InputField.vue';
import Button from '@/Components/Button.vue';
import Alert from '@/Components/Alert.vue';

defineOptions({ layout: null });

const props = defineProps({
    loginStorePath: String,
    forgotPasswordPath: String,
});

const page = usePage();
const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(props.loginStorePath, {
        onFinish: () => form.reset('password'),
    });
};
</script>
