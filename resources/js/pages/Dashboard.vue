<script setup lang="ts">
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';

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
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
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
    </AppSidebarLayout>
</template>
