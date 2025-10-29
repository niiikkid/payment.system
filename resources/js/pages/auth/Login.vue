<script setup lang="ts">
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store as loginStore } from '@/routes/login';
import { request as forgotRequest } from '@/routes/password';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post(loginStore.url(), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <AuthBase
        title="Вход в аккаунт"
        description="Введите e‑mail и пароль, чтобы войти"
    >
        <Head title="Вход" />

        <div v-if="props.status" class="mb-4 text-center text-sm font-medium text-success">
            {{ props.status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <label for="email" class="label">
                        <span class="label-text">E‑mail</span>
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        tabindex="1"
                        autocomplete="email"
                        placeholder="email@example.com"
                        v-model="form.email"
                        class="input input-bordered w-full"
                    />
                    <p v-if="form.errors.email" class="text-error text-sm">{{ form.errors.email }}</p>
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <label for="password" class="label">
                            <span class="label-text">Пароль</span>
                        </label>
                        <Link
                            v-if="props.canResetPassword"
                            :href="forgotRequest().url"
                            class="text-sm link link-hover"
                            tabindex="5"
                        >
                            Забыли пароль?
                        </Link>
                    </div>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        tabindex="2"
                        autocomplete="current-password"
                        placeholder="Пароль"
                        v-model="form.password"
                        class="input input-bordered w-full"
                    />
                    <p v-if="form.errors.password" class="text-error text-sm">{{ form.errors.password }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember" class="label cursor-pointer gap-3">
                        <input id="remember" name="remember" type="checkbox" tabindex="3" class="checkbox" v-model="form.remember" />
                        <span class="label-text">Запомнить меня</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary mt-2 w-full"
                    tabindex="4"
                    :disabled="form.processing"
                    data-test="login-button"
                >
                    <span v-if="form.processing" class="loading loading-spinner loading-sm mr-2" />
                    Войти
                </button>
            </div>

            <div class="text-center text-sm text-base-content/60" v-if="props.canRegister">
                Нет аккаунта?
                <Link :href="register().url" tabindex="5" class="link link-hover">Зарегистрироваться</Link>
            </div>
        </form>
    </AuthBase>
    
    
</template>


