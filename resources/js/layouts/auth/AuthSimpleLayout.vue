<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="hero min-h-svh bg-base-200 p-6 md:p-10">
        <div class="hero-content w-full max-w-sm">
            <div class="card w-full bg-base-100 shadow-xl">
                <div class="card-body gap-6">
                    <div class="flex flex-col items-center gap-4">
                        <Link :href="home()" class="btn btn-ghost px-2 normal-case">
                            <AppLogoIcon class="size-9" />
                            <span class="sr-only">{{ title }}</span>
                        </Link>
                        <div class="text-center">
                            <h1 class="card-title justify-center text-xl font-medium">{{ title }}</h1>
                            <p class="text-base-content/70 text-sm">{{ description }}</p>
                        </div>
                    </div>
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
