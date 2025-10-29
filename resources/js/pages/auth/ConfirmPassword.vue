<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/password/confirm';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

function submit() {
    form.post(store.url(), {
        onFinish: () => form.reset('password'),
        preserveScroll: true,
    });
}
</script>

<template>
    <AuthLayout
        title="Подтвердите пароль"
        description="Это защищённая зона приложения. Подтвердите пароль, чтобы продолжить."
    >
        <Head title="Подтверждение пароля" />

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid gap-2">
                <label for="password" class="label">
                    <span class="label-text">Пароль</span>
                </label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="input input-bordered w-full"
                    required
                    autocomplete="current-password"
                    autofocus
                    v-model="form.password"
                />
                <p v-if="form.errors.password" class="text-error text-sm">{{ form.errors.password }}</p>
            </div>

            <div class="flex items-center">
                <button class="btn btn-primary w-full" :disabled="form.processing" data-test="confirm-password-button">
                    <span v-if="form.processing" class="loading loading-spinner loading-sm mr-2" />
                    Подтвердить пароль
                </button>
            </div>
        </form>
    </AuthLayout>
</template>


