<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import CurrencyNetworkBadge from '@/components/ui/CurrencyNetworkBadge.vue';
import AddressCopy from '@/components/ui/AddressCopy.vue';

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

interface Props {
    addresses: AddressItem[];
    currencyOptions: { value: string; label: string }[];
    networkOptions: { value: string; label: string }[];
}

const props = defineProps<Props>();

const page = usePage();

const createForm = useForm({
    currency: '',
    network: '',
    address: '',
});

function submitCreate() {
    createForm.post('/addresses', {
        preserveScroll: true,
        onSuccess: () => {
            createForm.reset();
        },
    });
}

function toggleAddress(id: number, nextActive: boolean) {
    const form = useForm({ is_active: nextActive });
    form.patch(`/addresses/${id}`, { preserveScroll: true });
}
</script>

<template>
    <AppSidebarLayout :breadcrumbs="[{ title: 'Главная', href: '/' }, { title: 'Адреса', href: '/addresses' }]">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-xl font-semibold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 opacity-60">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
                Адреса
            </h1>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-[380px_1fr]">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Добавить новый адрес</h2>
                    <form @submit.prevent="submitCreate" class="grid gap-4">
                        <div v-if="page.props.flash?.error" class="alert alert-error">
                            <span>{{ page.props.flash.error }}</span>
                        </div>
                        <div v-if="Object.keys(createForm.errors).length" class="alert alert-error">
                            <span>
                                {{ createForm.errors.address || createForm.errors.network || createForm.errors.currency || 'Ошибка при добавлении адреса' }}
                            </span>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Валюта</span></label>
                            <select v-model="createForm.currency" class="select select-bordered" required>
                                <option value="" disabled>Выберите валюту</option>
                                <option v-for="opt in props.currencyOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                            </select>
                            <label v-if="createForm.errors.currency" class="label"><span class="label-text-alt text-error">{{ createForm.errors.currency }}</span></label>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Сеть</span></label>
                            <select v-model="createForm.network" class="select select-bordered" required>
                                <option value="" disabled>Выберите сеть</option>
                                <option v-for="opt in props.networkOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                            </select>
                            <label v-if="createForm.errors.network" class="label"><span class="label-text-alt text-error">{{ createForm.errors.network }}</span></label>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Адрес</span></label>
                            <input v-model.trim="createForm.address" type="text" class="input input-bordered" placeholder="Например: T..." required />
                            <label v-if="createForm.errors.address" class="label"><span class="label-text-alt text-error">{{ createForm.errors.address }}</span></label>
                        </div>
                        <div class="form-control mt-2">
                            <button class="btn btn-primary" :disabled="createForm.processing">Добавить</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Список адресов</h2>
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Валюта</th>
                                    <th>Адрес</th>
                                    <th>Баланс</th>
                                    <th>Активен</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="addr in props.addresses" :key="addr.id">
                                    <td>{{ addr.id }}</td>
                                    <td>
                                        <CurrencyNetworkBadge :currency-label="addr.currency_label" :network-label="addr.network_label" />
                                    </td>
                                    <td class="font-mono text-xs">
                                        <AddressCopy :address="addr.address" />
                                    </td>
                                    <td>{{ addr.balance }}</td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" class="toggle" :class="{'toggle-success': addr.is_active}" :checked="addr.is_active" @change="toggleAddress(addr.id, !addr.is_active)" />
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="props.addresses.length === 0">
                                    <td colspan="6" class="text-center text-sm opacity-70 py-6">Пока нет адресов</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template>


