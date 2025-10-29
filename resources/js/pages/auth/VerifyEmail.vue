<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{ status?: string }>();

const form = useForm({});

function resend() {
    form.post(send.url());
}
</script>

<template>
    <AuthLayout
        title="Подтверждение e‑mail"
        description="Пожалуйста, подтвердите e‑mail, перейдя по ссылке, которую мы вам отправили."
    >
        <Head title="Подтверждение e‑mail" />

        <div
            v-if="props.status === 'verification-link-sent'"
            class="mb-4 text-center text-sm font-medium text-success"
        >
            Новая ссылка подтверждения отправлена на e‑mail,
            указанный при регистрации.
        </div>

        <div class="space-y-6 text-center">
            <button class="btn btn-secondary" :disabled="form.processing" @click="resend">
                <span v-if="form.processing" class="loading loading-spinner loading-sm mr-2" />
                Отправить письмо повторно
            </button>

            <Link :href="logout().url" as="button" class="link link-hover block text-sm mx-auto">
                Выйти
            </Link>
        </div>
    </AuthLayout>
</template>


