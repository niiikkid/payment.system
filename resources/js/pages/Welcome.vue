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
        <!-- Background -->
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-primary/20 blur-3xl"></div>
            <div class="absolute -right-24 top-16 h-80 w-80 rounded-full bg-secondary/20 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/2 h-96 w-96 -translate-x-1/2 rounded-full bg-accent/15 blur-3xl"></div>
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-base-300/70 to-transparent"></div>
        </div>

        <!-- Top: language -->
        <div class="absolute inset-x-0 top-0 z-20">
            <div class="mx-auto flex w-full max-w-6xl justify-end px-5 pt-4 md:px-12 lg:px-16">
                <div class="rounded-xl border border-base-300/70 bg-base-100/70 p-2 shadow-sm backdrop-blur">
                    <LanguageSwitcher direction="down" align="end" class="w-fit" />
                </div>
            </div>
        </div>

        <!-- Hero -->
        <div class="relative z-10 mx-auto flex min-h-screen max-w-6xl items-center px-5 py-16 sm:pt-16 pt-24 md:px-12 lg:px-16">
            <div class="grid w-full items-center gap-10 lg:grid-cols-2 lg:gap-14">
                <!-- Left -->
                <div class="space-y-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="badge badge-primary badge-outline gap-2 py-3">
                            <span class="h-2 w-2 animate-pulse rounded-full bg-primary"></span>
                            <span class="uppercase tracking-[0.2em]">{{ __('frontend.welcome.badge_crypto_billing_api') }}</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h1 class="text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                            {{ __('frontend.welcome.title') }}
                        </h1>
                        <p class="max-w-2xl text-base text-base-content/70 sm:text-lg">
                            {{ __('frontend.welcome.description') }}
                        </p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-base-300/70 bg-base-100/60 p-4 backdrop-blur">
                            <div class="text-sm font-semibold">{{ __('frontend.welcome.feature_dashboard_title') }}</div>
                            <div class="mt-1 text-sm text-base-content/60">
                                {{ __('frontend.welcome.feature_dashboard_description') }}
                            </div>
                        </div>
                        <div class="rounded-2xl border border-base-300/70 bg-base-100/60 p-4 backdrop-blur">
                            <div class="text-sm font-semibold">{{ __('frontend.welcome.feature_callbacks_title') }}</div>
                            <div class="mt-1 text-sm text-base-content/60">
                                {{ __('frontend.welcome.feature_callbacks_description') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: CTA card -->
                <div class="lg:justify-self-end grid md:grid-cols-2 lg:grid-cols-1">
                    <div class="card w-full border border-base-300/70 bg-base-100/70 shadow-xl backdrop-blur">
                        <div class="card-body gap-5">
                            <div class="space-y-1">
                                <div class="text-lg font-semibold">{{ __('frontend.welcome.cta_title') }}</div>
                                <div class="text-sm text-base-content/60">
                                    {{ __('frontend.welcome.cta_description') }}
                                </div>
                            </div>

                            <div class="space-y-3">
                                <Link
                                    v-if="$page.props.auth.user"
                                    :href="dashboard()"
                                    class="btn btn-primary btn-lg w-full shadow-lg shadow-primary/25"
                                >
                                    {{ __('frontend.welcome.go_dashboard') }}
                                </Link>
                                <template v-else>
                                    <Link :href="login()" class="btn btn-neutral btn-lg w-full">
                                        {{ __('frontend.welcome.login') }}
                                    </Link>
                                    <Link v-if="canRegister" :href="register()" class="btn btn-outline btn-lg w-full">
                                        {{ __('frontend.welcome.register') }}
                                    </Link>
                                </template>
                            </div>

                            <div class="divider my-0"></div>

                            <ul class="space-y-2 text-sm text-base-content/70">
                                <li class="flex items-start gap-2">
                                    <span class="mt-1 inline-block h-2 w-2 rounded-full bg-success"></span>
                                    <span>{{ __('frontend.welcome.bullet_api_tokens') }}</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="mt-1 inline-block h-2 w-2 rounded-full bg-success"></span>
                                    <span>{{ __('frontend.welcome.bullet_invoices_notifications') }}</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="mt-1 inline-block h-2 w-2 rounded-full bg-success"></span>
                                    <span>{{ __('frontend.welcome.bullet_highload_ready') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
