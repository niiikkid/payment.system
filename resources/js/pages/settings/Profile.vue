<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit, update as updateProfile } from '@/routes/profile';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const page = usePage();
const user = page.props.auth.user as { name: string; email: string; email_verified_at?: string | null };

const form = useForm({
    name: user?.name ?? '',
    email: user?.email ?? '',
});

function submit() {
    form.post(updateProfile.url(), {
        preserveScroll: true,
        onBefore: () => form.transform((data) => ({ ...data, _method: 'PATCH' })),
    });
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Настройки профиля', href: edit().url },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Настройки профиля" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <div class="space-y-1">
                    <h2 class="text-lg font-medium">Информация профиля</h2>
                    <p class="text-sm text-base-content/70">Обновите имя и e‑mail</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <label for="name" class="label"><span class="label-text">Имя</span></label>
                        <input id="name" name="name" type="text" class="input input-bordered w-full" v-model="form.name" placeholder="Ваше имя" />
                        <p v-if="form.errors.name" class="text-error text-sm">{{ form.errors.name }}</p>
                    </div>

                    <div class="grid gap-2">
                        <label for="email" class="label"><span class="label-text">E‑mail</span></label>
                        <input id="email" name="email" type="email" class="input input-bordered w-full" v-model="form.email" placeholder="email@example.com" />
                        <p v-if="form.errors.email" class="text-error text-sm">{{ form.errors.email }}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing" data-test="update-profile-button">
                            <span v-if="form.processing" class="loading loading-spinner loading-sm mr-2" />
                            Сохранить
                        </button>
                        <p v-if="form.recentlySuccessful" class="text-sm text-base-content/70">Сохранено.</p>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>


