<script setup lang="ts">
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import { vueLang } from '@erag/lang-sync-inertia';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

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

const { __ } = vueLang();
const page = usePage();
const isApproved = computed(() => (page.props as any)?.auth?.is_approved === true);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: __('frontend.nav.dashboard'),
        href: dashboard().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="!isApproved" class="alert alert-warning">
            <div class="flex flex-col gap-1">
                <div class="font-semibold">{{ __('frontend.dashboard.pending_approval.title') }}</div>
                <div class="text-sm opacity-90">{{ __('frontend.dashboard.pending_approval.description') }}</div>
            </div>
        </div>
        <div
            class="mt-6 flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-base-200 shadow"
        >
            <div class="stats stats-vertical md:stats-horizontal bg-base-100">
                <div class="stat">
                    <div class="stat-title">{{ __('frontend.dashboard.stats.total') }}</div>
                    <div class="stat-value">{{ props.stats.totalInvoices }}</div>
                    <div class="stat-desc">{{ __('frontend.dashboard.stats.active', { count: props.stats.activeInvoices }) }}</div>
                </div>

                <div class="stat">
                    <div class="stat-title">{{ __('frontend.dashboard.stats.paid') }}</div>
                    <div class="stat-value text-success">{{ props.stats.paidInvoices }}</div>
                    <div class="stat-desc">{{ __('frontend.dashboard.stats.success', { rate: props.stats.successRate }) }}</div>
                </div>

                <div class="stat">
                    <div class="stat-title">{{ __('frontend.dashboard.stats.expired') }}</div>
                    <div class="stat-value text-warning">{{ props.stats.expiredInvoices }}</div>
                    <div class="stat-desc">{{ __('frontend.dashboard.stats.addresses', { count: props.stats.addressesTotal }) }}</div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
