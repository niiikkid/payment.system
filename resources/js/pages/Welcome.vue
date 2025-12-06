<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { vueLang } from '@erag/lang-sync-inertia';

withDefaults(defineProps<{ canRegister: boolean }>(), { canRegister: true });

const { __ } = vueLang();
</script>

<template>
    <Head :title="__('frontend.welcome.title')" />
    <div class="hero min-h-screen bg-base-200">
        <div class="hero-content text-center">
            <div class="max-w-md">
                <h1 class="text-4xl font-bold">{{ __('frontend.welcome.title') }}</h1>
                <p class="py-6 text-base-content/70">
                    {{ __('frontend.welcome.description') }}
                </p>
                <div class="flex justify-center gap-3">
                    <Link v-if="$page.props.auth.user" :href="dashboard()" class="btn btn-primary">
                        {{ __('frontend.welcome.go_dashboard') }}
                    </Link>
                    <template v-else>
                        <Link :href="login()" class="btn">{{ __('frontend.welcome.login') }}</Link>
                        <Link v-if="canRegister" :href="register()" class="btn btn-outline">{{ __('frontend.welcome.register') }}</Link>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>


