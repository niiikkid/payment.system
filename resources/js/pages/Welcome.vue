<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import LanguageSwitcher from '@/components/ui/LanguageSwitcher.vue';
import { vueLang } from '@erag/lang-sync-inertia';

withDefaults(defineProps<{ canRegister: boolean }>(), { canRegister: true });

const { __ } = vueLang();
</script>

<template>
    <Head :title="__('frontend.welcome.title')" />
    <div class="relative min-h-screen overflow-hidden bg-base-200 text-base-content">

        <div class="mt-3 mx-auto grid w-full max-w-6xl gap-10 lg:grid-cols-[1.1fr,0.9fr]">
            <div class="w-fit">
                <LanguageSwitcher direction="down" align="end" class="w-fit" />
            </div>
        </div>
        <div class="relative z-10 flex min-h-screen items-center px-5 py-12 pt-0 md:px-12 lg:px-16">
            <div class="mx-auto grid w-full max-w-6xl gap-10 lg:grid-cols-[1.1fr,0.9fr]">
                <!-- Left text + CTA -->
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 rounded-full border border-base-300/60 bg-base-100/70 px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] backdrop-blur">
                        <span class="h-2 w-2 rounded-full bg-primary animate-pulse" aria-hidden="true"></span>
                        <span>Crypto. Billing. API.</span>
                    </div>

                    <div class="space-y-4">
                        <h1 class="text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                            {{ __('frontend.welcome.title') }}
                        </h1>
                        <p class="text-base text-base-content/70 sm:text-lg max-w-2xl">
                            {{ __('frontend.welcome.description') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <Link v-if="$page.props.auth.user" :href="dashboard()" class="btn btn-primary btn-lg shadow-lg shadow-primary/25">
                            {{ __('frontend.welcome.go_dashboard') }}
                        </Link>
                        <template v-else>
                            <Link :href="login()" class="btn btn-lg btn-neutral bg-base-100/80 border-base-300/80 shadow-md">
                                {{ __('frontend.welcome.login') }}
                            </Link>
                            <Link v-if="canRegister" :href="register()" class="btn btn-outline btn-lg">
                                {{ __('frontend.welcome.register') }}
                            </Link>
                        </template>
                    </div>

                    <div class="flex flex-wrap gap-3 text-sm text-base-content/60">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-primary"></span> On-chain / Off-chain
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-secondary"></span> Webhooks & Callbacks
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-accent"></span> Fiat → Crypto Routing
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
