<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import CurrencyNetworkBadge from '@/components/ui/CurrencyNetworkBadge.vue';
import AddressCopy from '@/components/ui/AddressCopy.vue';
import DateTimeFormat from '@/components/ui/DateTimeFormat.vue';
import Pagination from '@/components/ui/Pagination.vue';
import AddressCreateModal, { type AddressCreateForm } from '@/components/modals/addresses/AddressCreateModal.vue';

interface AddressItem {
    id: number;
    currency: string;
    currency_label: string;
    network: string;
    network_label: string;
    address: string;
    is_active: boolean;
    balance: string;
    last_checked_at: string | null;
    created_at: string | null;
}

interface PaginationLinks {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedAddresses {
    data: AddressItem[];
    links: PaginationLinks[];
}

interface Props {
    addresses: PaginatedAddresses;
    currencyOptions: { value: string; label: string }[];
    networkOptions: { value: string; label: string }[];
}

const props = defineProps<Props>();

const showCreate = ref(false);
const createPayload = ref<AddressCreateForm>({
    currency: '',
    network: '',
    address: '',
});
const createLoading = ref(false);
const createError = ref<string | null>(null);

function closeCreate() {
    showCreate.value = false;
}

function updateCreatePayload(payload: AddressCreateForm) {
    createPayload.value = payload;
}

function resetCreatePayload() {
    createPayload.value = { currency: '', network: '', address: '' };
}

async function submitCreate() {
    createError.value = null;
    createLoading.value = true;
    try {
        await axios.post('/addresses', {
            currency: createPayload.value.currency,
            network: createPayload.value.network,
            address: createPayload.value.address.trim(),
        });
        showCreate.value = false;
        router.reload({ only: ['addresses'] });
        resetCreatePayload();
    } catch (e: any) {
        createError.value = e?.response?.data?.message || e?.response?.data?.errors?.address?.[0] || e?.response?.data?.errors?.network?.[0] || e?.response?.data?.errors?.currency?.[0] || e?.message || 'Ошибка при добавлении адреса';
    } finally {
        createLoading.value = false;
    }
}

function toggleAddress(id: number, nextActive: boolean) {
    const form = useForm({ is_active: nextActive });
    form.patch(`/addresses/${id}`, { preserveScroll: true });
}

function toIso(input: string | null | undefined): string {
    if (!input) return '';
    if (typeof input !== 'string') return '';
    if (input.includes('T')) return input; // уже ISO
    // Преобразуем 'YYYY-MM-DD HH:mm:ss' -> 'YYYY-MM-DDTHH:mm:ssZ'
    return `${input.replace(' ', 'T')}Z`;
}
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Главная', href: '/' }, { title: 'Адреса', href: '/addresses' }]">
        <template #header-actions>
            <div class="flex items-center gap-2">
                <button class="btn btn-primary btn-sm" @click="showCreate = true">Создать адрес</button>
            </div>
        </template>

        <div class="grid gap-6">
            <div class="lg:card lg:bg-base-100 lg:shadow">
                <div class="lg:card-body">
                    <h2 class="hidden lg:block card-title">Список адресов</h2>
                    <h2 class="lg:hidden card-title mb-3">Список адресов</h2>

                    <!-- Desktop Table View (lg and above) -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="table table-sm w-full">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Адрес</th>
                                    <th>Баланс</th>
                                    <th>Валюта</th>
                                    <th>Активен</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="addr in props.addresses.data" :key="addr.id">
                                    <td>{{ addr.id }}</td>
                                    <td class="font-mono text-xs">
                                        <AddressCopy :address="addr.address" />
                                    </td>
                                    <td>{{ addr.balance }}</td>
                                    <td>
                                        <CurrencyNetworkBadge :currency-label="addr.currency_label" :network-label="addr.network_label" />
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" class="toggle" :class="{'toggle-success': addr.is_active}" :checked="addr.is_active" @change="toggleAddress(addr.id, !addr.is_active)" />
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="props.addresses.data.length === 0">
                                    <td colspan="6" class="text-center text-sm opacity-70 py-6">Пока нет адресов</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View (sm to lg) -->
                    <div class="hidden sm:block lg:hidden space-y-3">
                        <div v-for="addr in props.addresses.data" :key="addr.id" class="card bg-base-100 shadow-sm">
                            <div class="card-body p-4">
                                <!-- Header: ID and Date -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs opacity-70">ID:</span>
                                        <span class="text-sm font-semibold">{{ addr.id }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs opacity-70" v-if="addr.created_at">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                        </svg>
                                        <DateTimeFormat :value="toIso(addr.created_at)" />
                                    </div>
                                </div>

                                <!-- Main Content Row -->
                                <div class="grid grid-cols-[auto_1fr_1fr_auto] items-center gap-3">
                                    <div class="flex min-w-0">
                                        <div class="font-mono text-xs opacity-70 truncate">
                                            <AddressCopy :address="addr.address" />
                                        </div>
                                    </div>

                                    <div class="flex justify-center min-w-0">
                                        <CurrencyNetworkBadge :currency-label="addr.currency_label" :network-label="addr.network_label" />
                                    </div>

                                    <div class="flex items-center gap-2 whitespace-nowrap">
                                        <span class="text-xs opacity-70">Баланс:</span>
                                        <span class="text-sm font-semibold">{{ addr.balance }}</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" class="toggle toggle-sm" :class="{'toggle-success': addr.is_active}" :checked="addr.is_active" @change="toggleAddress(addr.id, !addr.is_active)" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="!props.addresses.data.length" class="text-center text-sm opacity-70 py-6">
                            Пока нет адресов
                        </div>
                    </div>

                    <!-- Small Mobile Card View (below sm) -->
                    <div class="sm:hidden space-y-3">
                        <div v-for="addr in props.addresses.data" :key="addr.id" class="card bg-base-100 shadow-sm">
                            <div class="card-body p-4">
                                <!-- Header: ID and Date -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs opacity-70">ID:</span>
                                        <span class="text-sm font-semibold">{{ addr.id }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs opacity-70" v-if="addr.created_at">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                        </svg>
                                        <DateTimeFormat :value="toIso(addr.created_at)" short-year hide-seconds />
                                    </div>
                                </div>

                                <!-- Main Content Row -->
                                <div class="flex justify-between items-center gap-3">
                                    <!-- Balance and Currency -->
                                    <div class="flex items-center gap-2">
                                        <div class="font-mono text-xs text-center">
                                            <AddressCopy :address="addr.address" />
                                        </div>
                                    </div>

                                    <!-- Right Side: Active Toggle -->
                                    <div>
                                        <input type="checkbox" class="toggle toggle-sm" :class="{'toggle-success': addr.is_active}" :checked="addr.is_active" @change="toggleAddress(addr.id, !addr.is_active)" />
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="flex items-center justify-between mt-3">
                                    <div>
                                        <CurrencyNetworkBadge :currency-label="addr.currency_label" :network-label="addr.network_label" />
                                    </div>
                                    <div class="inline-flex items-center gap-2">
                                        <span class="text-xs opacity-70">Баланс:</span>
                                        <div class="text-sm font-semibold">{{ addr.balance }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="!props.addresses.data.length" class="text-center text-sm opacity-70 py-6">
                            Пока нет адресов
                        </div>
                    </div>

                    <Pagination :links="props.addresses.links" />
                </div>
            </div>
        </div>

        <AddressCreateModal
            v-model="showCreate"
            :form="createPayload"
            :currency-options="props.currencyOptions"
            :network-options="props.networkOptions"
            :error="createError"
            :loading="createLoading"
            @update:form="updateCreatePayload"
            @submit="submitCreate"
            @close="closeCreate"
        />
    </AppLayout>
</template>


