<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';

withDefaults(defineProps<{ canRegister: boolean }>(), { canRegister: true });
</script>

<template>
    <Head title="Добро пожаловать" />
    <div class="hero min-h-screen bg-base-200">
        <div class="hero-content text-center">
            <div class="max-w-md">
                <h1 class="text-4xl font-bold">Добро пожаловать</h1>
                <p class="py-6 text-base-content/70">
                    Это платежная система для обработки криптовалютных платежей.
                    Войдите или зарегистрируйтесь, чтобы продолжить.
                </p>
                <div class="flex justify-center gap-3">
                    <Link v-if="$page.props.auth.user" :href="dashboard()" class="btn btn-primary">
                        Перейти в панель
                    </Link>
                    <template v-else>
                        <Link :href="login()" class="btn">Войти</Link>
                        <Link v-if="canRegister" :href="register()" class="btn btn-outline">Регистрация</Link>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>


