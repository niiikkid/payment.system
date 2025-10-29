<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit, update as updatePassword } from '@/routes/user-password';
import { Head, useForm } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(updatePassword.url(), {
        preserveScroll: true,
        onBefore: () => form.transform((data) => ({ ...data, _method: 'PUT' })),
        onFinish: () => form.reset('password', 'password_confirmation', 'current_password'),
    });
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Настройки пароля', href: edit().url },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Настройки пароля" />

        <SettingsLayout>
            <div class="space-y-6">
                <div class="space-y-1">
                    <h2 class="text-lg font-medium">Обновление пароля</h2>
                    <p class="text-sm text-base-content/70">Используйте длинный случайный пароль для безопасности</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <label for="current_password" class="label"><span class="label-text">Текущий пароль</span></label>
                        <input id="current_password" name="current_password" type="password" class="input input-bordered w-full" autocomplete="current-password" placeholder="Текущий пароль" v-model="form.current_password" />
                        <p v-if="form.errors.current_password" class="text-error text-sm">{{ form.errors.current_password }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label for="password" class="label"><span class="label-text">Новый пароль</span></label>
                        <input id="password" name="password" type="password" class="input input-bordered w-full" autocomplete="new-password" placeholder="Новый пароль" v-model="form.password" />
                        <p v-if="form.errors.password" class="text-error text-sm">{{ form.errors.password }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label for="password_confirmation" class="label"><span class="label-text">Подтвердите пароль</span></label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="input input-bordered w-full" autocomplete="new-password" placeholder="Подтвердите пароль" v-model="form.password_confirmation" />
                        <p v-if="form.errors.password_confirmation" class="text-error text-sm">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing" data-test="update-password-button">
                            <span v-if="form.processing" class="loading loading-spinner loading-sm mr-2" />
                            Сохранить пароль
                        </button>
                        <p v-if="form.recentlySuccessful" class="text-sm text-base-content/70">Сохранено.</p>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>


