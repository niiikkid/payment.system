<script setup lang="ts">
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store as registerStore } from '@/routes/register';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(registerStore.url(), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <AuthBase
        title="Создать аккаунт"
        description="Заполните данные, чтобы создать аккаунт"
    >
        <Head title="Регистрация" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <label for="name" class="label">
                        <span class="label-text">Имя</span>
                    </label>
                    <input
                        id="name"
                        type="text"
                        required
                        autofocus
                        tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Полное имя"
                        v-model="form.name"
                        class="input input-bordered w-full"
                    />
                    <p v-if="form.errors.name" class="text-error text-sm">{{ form.errors.name }}</p>
                </div>

                <div class="grid gap-2">
                    <label for="email" class="label">
                        <span class="label-text">E‑mail</span>
                    </label>
                    <input
                        id="email"
                        type="email"
                        required
                        tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="email@example.com"
                        v-model="form.email"
                        class="input input-bordered w-full"
                    />
                    <p v-if="form.errors.email" class="text-error text-sm">{{ form.errors.email }}</p>
                </div>

                <div class="grid gap-2">
                    <label for="password" class="label">
                        <span class="label-text">Пароль</span>
                    </label>
                    <input
                        id="password"
                        type="password"
                        required
                        tabindex="3"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Пароль"
                        v-model="form.password"
                        class="input input-bordered w-full"
                    />
                    <p v-if="form.errors.password" class="text-error text-sm">{{ form.errors.password }}</p>
                </div>

                <div class="grid gap-2">
                    <label for="password_confirmation" class="label">
                        <span class="label-text">Подтвердите пароль</span>
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        required
                        tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Подтвердите пароль"
                        v-model="form.password_confirmation"
                        class="input input-bordered w-full"
                    />
                    <p v-if="form.errors.password_confirmation" class="text-error text-sm">{{ form.errors.password_confirmation }}</p>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary mt-2 w-full"
                    tabindex="5"
                    :disabled="form.processing"
                    data-test="register-user-button"
                >
                    <span v-if="form.processing" class="loading loading-spinner loading-sm mr-2" />
                    Создать аккаунт
                </button>
            </div>

            <div class="text-center text-sm text-base-content/60">
                Уже есть аккаунт?
                <Link :href="login().url" class="link link-hover" tabindex="6">Войти</Link>
            </div>
        </form>
    </AuthBase>
</template>


