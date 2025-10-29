<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Панель управления',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Панель управления" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4 bg-base-200"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-box border border-base-200 bg-base-100 shadow-sm" />
                <div class="relative aspect-video overflow-hidden rounded-box border border-base-200 bg-base-100 shadow-sm" />
                <div class="relative aspect-video overflow-hidden rounded-box border border-base-200 bg-base-100 shadow-sm" />
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-box bg-base-100 shadow-sm border border-base-200 md:min-h-min"
            >
                <div class="h-full w-full" />
            </div>
        </div>
    </AppLayout>
</template>
