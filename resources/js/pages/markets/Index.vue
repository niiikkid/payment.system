<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import FormControl from '@/components/form/FormControl.vue';
import Label from '@/components/form/Label.vue';
import Input from '@/components/form/Input.vue';
import MarketFiatModal from '@/components/modals/markets/MarketFiatModal.vue';
import RelativeTime from '@/components/ui/RelativeTime.vue';
import { computed, reactive, ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { vueLang } from '@erag/lang-sync-inertia';

interface MarketPrice {
    buy_price: number | null;
    sell_price: number | null;
    fetched_at?: string | null;
}

interface MarketFiat {
    id: number;
    code: string;
    rows: number;
    pay_types: string[];
    polling_interval_seconds: number;
    is_enabled: boolean;
    last_polled_at?: string | null;
    price?: MarketPrice | null;
}

interface PageProps {
    fiats: MarketFiat[];
    market: string;
}

const props = defineProps<PageProps>();
const { __ } = vueLang();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: __('frontend.app_settings_page.breadcrumb.home'), href: '/dashboard' },
    { title: __('frontend.layout.page_titles.markets') },
]);

const editForms = reactive<Record<number, any>>({});
const openedFiatId = ref<number | null>(null);
const modalVisible = ref(false);

props.fiats.forEach((fiat) => {
    editForms[fiat.id] = useForm({
        rows: fiat.rows,
        pay_types: fiat.pay_types.join(', '),
        polling_interval_seconds: fiat.polling_interval_seconds,
        is_enabled: fiat.is_enabled,
    });
});

const normalizePayTypes = (value: string): string[] => {
    return value
        .split(',')
        .map((item) => item.trim())
        .filter((item) => Boolean(item));
};

const submitUpdate = (fiatId: number) => {
    const form = editForms[fiatId];
    form
        .transform((data: any) => ({
            ...data,
            pay_types: normalizePayTypes(data.pay_types),
        }))
        .patch(`/markets/${fiatId}`, {
            preserveScroll: true,
            onSuccess: () => {
                openedFiatId.value = null;
            },
        });
};

const refreshFiat = (fiatId: number) => {
    router.post(`/markets/${fiatId}/refresh`, {}, { preserveScroll: true });
};

const formatPrice = (price?: number | null) => {
    if (price === null || price === undefined) {
        return '—';
    }
    return price.toFixed(4);
};

const formatList = (items: string[]) => (items.length ? items.join(', ') : '—');

const openedFiat = computed(() => props.fiats.find((f) => f.id === openedFiatId.value) ?? null);
// Упрощаем типизацию, чтобы избежать глубокой инстанциации useForm
const openedForm = computed<any>(() => (openedFiatId.value ? editForms[openedFiatId.value] : null));

const openSettings = (fiatId: number) => {
    openedFiatId.value = fiatId;
    modalVisible.value = true;
};

const closeSettings = () => {
    openedFiatId.value = null;
    modalVisible.value = false;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <div class="lg:card lg:bg-base-100 lg:shadow">
                <div class="lg:card-body">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="card-title">{{ __('frontend.markets.title') }}</h2>
                            <p class="text-sm opacity-70">{{ __('frontend.markets.subtitle') }}</p>
                        </div>
                        <span class="badge badge-outline uppercase">{{ props.market }}</span>
                    </div>

                    <!-- Desktop table -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="table table-sm w-full">
                            <thead>
                                <tr>
                                    <th>{{ __('frontend.markets.fields.pair') }}</th>
                                    <th>{{ __('frontend.markets.fields.buy_price') }}</th>
                                    <th>{{ __('frontend.markets.fields.sell_price') }}</th>
                                    <th>{{ __('frontend.markets.fields.updated_at') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="fiat in props.fiats" :key="fiat.id">
                                    <td class="uppercase flex items-center gap-2">
                                        USDT/{{ fiat.code }}
                                        <span
                                            class="badge badge-xs"
                                            :class="fiat.is_enabled ? 'badge-success' : 'badge-error'"
                                        />
                                    </td>
                                    <td>
                                        <div class="font-semibold">{{ formatPrice(fiat.price?.buy_price ?? null) }}</div>
                                    </td>
                                    <td>
                                        <div class="font-semibold">{{ formatPrice(fiat.price?.sell_price ?? null) }}</div>
                                    </td>
                                    <td>
                                        <div class="text-sm">
                                            <RelativeTime v-if="fiat.price?.fetched_at" :value="fiat.price.fetched_at" />
                                            <span v-else>—</span>
                                        </div>
                                    </td>
                                    <td class="flex gap-2">
                                        <button
                                            class="btn btn-outline btn-sm btn-square"
                                            :aria-label="__('frontend.markets.actions.settings')"
                                            @click="openSettings(fiat.id)"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"
                                                />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </button>
                                        <button
                                            class="btn btn-ghost btn-sm btn-square"
                                            :aria-label="__('frontend.markets.actions.refresh')"
                                            @click="refreshFiat(fiat.id)"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
                                                />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!props.fiats.length">
                                    <td colspan="9" class="text-center text-sm opacity-60 py-6">
                                        {{ __('frontend.markets.table.empty') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile cards -->
                    <div class="grid gap-3 sm:grid-cols-2 lg:hidden">
                        <div v-for="fiat in props.fiats" :key="fiat.id" class="card bg-base-100 shadow-sm">
                            <div class="card-body p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold uppercase">USDT/{{ fiat.code }}</span>
                                        <span
                                            class="badge badge-xs"
                                            :class="fiat.is_enabled ? 'badge-success' : 'badge-error'"
                                        />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button
                                            class="btn btn-outline btn-sm btn-square"
                                            :aria-label="__('frontend.markets.actions.settings')"
                                            @click="openSettings(fiat.id)"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"
                                                />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </button>
                                        <button
                                            class="btn btn-ghost btn-sm btn-square"
                                            :aria-label="__('frontend.markets.actions.refresh')"
                                            @click="refreshFiat(fiat.id)"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="grid gap-3 text-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="opacity-70">{{ __('frontend.markets.fields.buy_price') }}</span>
                                        <span class="font-semibold">{{ formatPrice(fiat.price?.buy_price ?? null) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="opacity-70">{{ __('frontend.markets.fields.sell_price') }}</span>
                                        <span class="font-semibold">{{ formatPrice(fiat.price?.sell_price ?? null) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="opacity-70">{{ __('frontend.markets.fields.updated_at') }}</span>
                                        <span>
                                            <RelativeTime v-if="fiat.price?.fetched_at" :value="fiat.price.fetched_at" />
                                            <span v-else>—</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="!props.fiats.length" class="text-center text-sm opacity-70 py-6 sm:col-span-2">
                            {{ __('frontend.markets.table.empty') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <MarketFiatModal
            v-if="openedFiat && openedForm"
            v-model="modalVisible"
            :fiat-code="openedFiat.code"
            :last-polled-at="openedFiat.last_polled_at ?? null"
            v-model:form="openedForm"
            :loading="openedForm.processing"
            @close="closeSettings"
            @submit="openedFiatId !== null && submitUpdate(openedFiatId)"
        />
    </AppLayout>
</template>

