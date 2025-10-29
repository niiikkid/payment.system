<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { update } from '@/routes/password';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    token: string;
    email: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(update.url(), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <AuthLayout
        title="Сброс пароля"
        description="Введите новый пароль ниже"
    >
        <Head title="Сброс пароля" />

        <form @submit.prevent="submit">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <label for="email" class="label">
                        <span class="label-text">E‑mail</span>
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="form.email"
                        class="input input-bordered w-full"
                        readonly
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
                        name="password"
                        autocomplete="new-password"
                        class="input input-bordered w-full"
                        autofocus
                        placeholder="Пароль"
                        v-model="form.password"
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
                        name="password_confirmation"
                        autocomplete="new-password"
                        class="input input-bordered w-full"
                        placeholder="Подтвердите пароль"
                        v-model="form.password_confirmation"
                    />
                    <p v-if="form.errors.password_confirmation" class="text-error text-sm">{{ form.errors.password_confirmation }}</p>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary mt-2 w-full"
                    :disabled="form.processing"
                    data-test="reset-password-button"
                >
                    <span v-if="form.processing" class="loading loading-spinner loading-sm mr-2" />
                    Сбросить пароль
                </button>
            </div>
        </form>
    </AuthLayout>
</template>


