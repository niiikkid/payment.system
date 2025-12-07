<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { vueLang } from '@erag/lang-sync-inertia';
import MerchantModal, { type MerchantForm } from '@/components/modals/merchants/MerchantModal.vue';
import Pagination from '@/components/ui/Pagination.vue';
import DateTimeFormat from '@/components/ui/DateTimeFormat.vue';

interface Merchant {
    id: number;
    name: string;
    description: string | null;
    initials: string;
    logo_path: string | null;
    logo_url: string | null;
    created_at: string | null;
}

interface PaginationLinks {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedMerchants {
    data: Merchant[];
    links: PaginationLinks[];
}

interface Props {
    merchants: PaginatedMerchants;
}

const props = defineProps<Props>();
const { __ } = vueLang();

const showModal = ref(false);
const editing = ref<Merchant | null>(null);
const currentLogoUrl = ref<string | null>(null);

const merchantForm = useForm<MerchantForm>({
    name: '',
    description: '',
    initials: '',
    logo: null,
});

function resetForm() {
    merchantForm.reset();
    merchantForm.clearErrors();
    merchantForm.logo = null;
    currentLogoUrl.value = null;
}

function openCreate() {
    editing.value = null;
    resetForm();
    showModal.value = true;
}

function openEdit(item: Merchant) {
    editing.value = item;
    merchantForm.name = item.name ?? '';
    merchantForm.description = item.description ?? '';
    merchantForm.initials = item.initials ?? '';
    merchantForm.logo = null;
    merchantForm.clearErrors();
    currentLogoUrl.value = item.logo_url;
    showModal.value = true;
}

function updateFormPayload(payload: MerchantForm) {
    merchantForm.name = payload.name;
    merchantForm.description = payload.description;
    merchantForm.initials = payload.initials;
    merchantForm.logo = payload.logo;
}

function submit() {
    const isEdit = !!editing.value;
    const url = isEdit ? `/merchants/${editing.value?.id}` : '/merchants';
    const method = isEdit ? 'patch' : 'post';

    const payload = {
        name: (merchantForm.name ?? editing.value?.name ?? '').toString().trim(),
        description: (merchantForm.description ?? editing.value?.description ?? '').toString().trim(),
        initials: (merchantForm.initials ?? editing.value?.initials ?? '').toString().trim(),
        logo: merchantForm.logo ?? null,
    };

    merchantForm.transform(() => payload)[method](url, {
        preserveScroll: true,
        forceFormData: !!payload.logo,
        onSuccess: () => {
            showModal.value = false;
            resetForm();
        },
        onError: () => {
            showModal.value = true;
        },
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: __('frontend.nav.dashboard'), href: '/' }, { title: __('frontend.nav.merchants'), href: '/merchants' }]">
        <template #header-actions>
            <div class="flex items-center gap-2">
                <button class="btn btn-primary btn-sm" @click="openCreate">{{ __('frontend.merchants.actions.create') }}</button>
            </div>
        </template>

        <div class="grid gap-6">
            <div class="lg:card lg:bg-base-100 lg:shadow">
                <div class="lg:card-body">
                    <h2 class="hidden lg:block card-title">{{ __('frontend.merchants.list.title') }}</h2>
                    <h2 class="lg:hidden card-title mb-3">{{ __('frontend.merchants.list.title') }}</h2>

                    <div class="hidden lg:block overflow-x-auto">
                        <table class="table table-sm w-full">
                            <thead>
                                <tr>
                                    <th>{{ __('frontend.common.id') }}</th>
                                    <th>{{ __('frontend.merchants.fields.name') }}</th>
                                    <th>{{ __('frontend.merchants.fields.initials') }}</th>
                                    <th>{{ __('frontend.merchants.fields.description') }}</th>
                                    <th>{{ __('frontend.common.created_at') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="merchant in props.merchants.data" :key="merchant.id">
                                    <td class="whitespace-nowrap">#{{ merchant.id }}</td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="avatar placeholder">
                                                <div class="bg-primary text-primary-content rounded-full w-10 h-10">
                                                <img v-if="merchant.logo_url" :src="merchant.logo_url" alt="logo" class="w-full h-full object-cover rounded-full" />
                                                <span v-else class="flex items-center justify-center w-full h-full">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                                    </svg>
                                                </span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-semibold">{{ merchant.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ merchant.initials }}</td>
                                    <td class="max-w-xs truncate">{{ merchant.description }}</td>
                                    <td class="text-xs text-base-content/70 whitespace-nowrap">
                                        <DateTimeFormat v-if="merchant.created_at" :value="merchant.created_at" />
                                        <span v-else>—</span>
                                    </td>
                                    <td class="text-right">
                                        <button class="btn btn-ghost btn-xs" @click="openEdit(merchant)">{{ __('frontend.common.edit') }}</button>
                                    </td>
                                </tr>
                                <tr v-if="props.merchants.data.length === 0">
                                    <td colspan="6" class="text-center text-sm opacity-70 py-6">{{ __('frontend.merchants.empty') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="space-y-3 lg:hidden">
                        <div v-for="merchant in props.merchants.data" :key="merchant.id" class="card bg-base-100 shadow-sm">
                            <div class="card-body p-4 space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-primary text-primary-content rounded-full w-12 h-12">
                                            <img v-if="merchant.logo_url" :src="merchant.logo_url" alt="logo" class="w-full h-full object-cover rounded-full" />
                                            <span v-else class="flex items-center justify-center w-full h-full">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="space-y-0.5 min-w-0">
                                        <div class="font-semibold">{{ merchant.name }}</div>
                                        <div class="text-xs text-base-content/70 truncate max-w-xs">{{ merchant.description || '—' }}</div>
                                    </div>
                                    <div class="ml-auto">
                                        <button class="btn btn-ghost btn-xs" @click="openEdit(merchant)">{{ __('frontend.common.edit') }}</button>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-base-content/70">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>
                                    <DateTimeFormat v-if="merchant.created_at" :value="merchant.created_at" short-year />
                                    <span v-else>—</span>
                                </div>
                            </div>
                        </div>
                        <div v-if="!props.merchants.data.length" class="text-center text-sm opacity-70 py-6">
                            {{ __('frontend.merchants.empty') }}
                        </div>
                    </div>

                    <Pagination :links="props.merchants.links" />
                </div>
            </div>
        </div>

        <MerchantModal
            v-model="showModal"
            :form="merchantForm as unknown as MerchantForm"
            :errors="merchantForm.errors"
            :loading="merchantForm.processing"
            :mode="editing ? 'edit' : 'create'"
            :current-logo-url="currentLogoUrl"
            @update:form="updateFormPayload"
            @submit="submit"
            @close="resetForm"
        />
    </AppLayout>
</template>

