<script setup lang="ts">
import { ref } from 'vue';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import CurrencyNetworkBadge from '@/components/ui/CurrencyNetworkBadge.vue';
import AddressCopy from '@/components/ui/AddressCopy.vue';
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
</script>

<template>
    <AppSidebarLayout :breadcrumbs="[{ title: 'Главная', href: '/' }, { title: 'Адреса', href: '/addresses' }]">
        <template #header-actions>
            <div class="flex items-center gap-2">
                <button class="btn btn-primary btn-sm" @click="showCreate = true">Создать адрес</button>
            </div>
        </template>

        <div class="grid gap-6">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Список адресов</h2>
                    <div class="overflow-x-auto">
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
    </AppSidebarLayout>
</template>


