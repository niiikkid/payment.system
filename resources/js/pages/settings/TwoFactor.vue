<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { disable, enable, show } from '@/routes/two-factor';
import { Head, useForm } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';

interface Props {
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
}

withDefaults(defineProps<Props>(), {
    requiresConfirmation: false,
    twoFactorEnabled: false,
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Двухфакторная аутентификация', href: show.url() },
];

const enableForm = useForm({});
const disableForm = useForm({ _method: 'DELETE' });

function submitEnable() {
    enableForm.post(enable.url(), { preserveScroll: true });
}

function submitDisable() {
    disableForm.post(disable.url(), { preserveScroll: true });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Двухфакторная аутентификация" />
        <SettingsLayout>
            <div class="space-y-6">
                <div class="space-y-1">
                    <h2 class="text-lg font-medium">Двухфакторная аутентификация</h2>
                    <p class="text-sm text-base-content/70">Управляйте настройками 2FA</p>
                </div>

                <div v-if="!twoFactorEnabled" class="flex flex-col items-start space-y-4">
                    <div class="badge badge-error">Отключена</div>
                    <p class="text-base-content/70">
                        При включении 2FA во время входа потребуется одноразовый код из приложения-аутентификатора.
                    </p>
                    <button class="btn btn-primary" :disabled="enableForm.processing" @click="submitEnable">
                        <span v-if="enableForm.processing" class="loading loading-spinner loading-sm mr-2" />
                        Включить 2FA
                    </button>
                </div>

                <div v-else class="flex flex-col items-start space-y-4">
                    <div class="badge badge-success">Включена</div>
                    <p class="text-base-content/70">
                        При включённой 2FA вход защищён одноразовым кодом из приложения-аутентификатора.
                    </p>
                    <button class="btn btn-error" :disabled="disableForm.processing" @click="submitDisable">
                        <span v-if="disableForm.processing" class="loading loading-spinner loading-sm mr-2" />
                        Отключить 2FA
                    </button>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>


