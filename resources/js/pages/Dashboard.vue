<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    stats: {
        totalInvoices: number;
        paidInvoices: number;
        activeInvoices: number;
        expiredInvoices: number;
        addressesTotal: number;
        successRate: number;
    }
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Главная',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Главная" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-xl font-semibold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 opacity-60">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                Главная
            </h1>
        </div>

        <div
            class="mt-6 flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-base-200"
        >
            <div class="stats stats-vertical md:stats-horizontal shadow bg-base-100">
                <div class="stat">
                    <div class="stat-title">Всего инвойсов</div>
                    <div class="stat-value">{{ props.stats.totalInvoices }}</div>
                    <div class="stat-desc">Активных: {{ props.stats.activeInvoices }}</div>
                </div>

                <div class="stat">
                    <div class="stat-title">Оплачено</div>
                    <div class="stat-value text-success">{{ props.stats.paidInvoices }}</div>
                    <div class="stat-desc">Успех: {{ props.stats.successRate }}%</div>
                </div>

                <div class="stat">
                    <div class="stat-title">Истёкших</div>
                    <div class="stat-value text-warning">{{ props.stats.expiredInvoices }}</div>
                    <div class="stat-desc">Адресов: {{ props.stats.addressesTotal }}</div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
