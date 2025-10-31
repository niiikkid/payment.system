<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

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
    <AppSidebarLayout :breadcrumbs="[{ title: 'Главная', href: '/' }, { title: 'Адреса' }]">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-xl font-semibold">Адреса</h1>
            <Link href="/dashboard" class="btn btn-ghost btn-sm">На дашборд</Link>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-[380px_1fr]">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Добавить новый адрес</h2>
                    <form @submit.prevent="submitCreate" class="grid gap-4">
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
                                    <th>Сеть</th>
                                    <th>Адрес</th>
                                    <th>Баланс</th>
                                    <th>Активен</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="addr in props.addresses" :key="addr.id">
                                    <td>{{ addr.id }}</td>
                                    <td><span class="badge">{{ addr.currency_label }}</span></td>
                                    <td><span class="badge badge-ghost">{{ addr.network_label }}</span></td>
                                    <td class="font-mono text-xs break-all">{{ addr.address }}</td>
                                    <td>{{ addr.balance }}</td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" class="toggle" :checked="addr.is_active" @change="toggleAddress(addr.id, !addr.is_active)" />
                                            <span :class="['badge','badge-xs', addr.is_active ? 'badge-success' : 'badge-error']">{{ addr.is_active ? 'Да' : 'Нет' }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="props.addresses.length === 0">
                                    <td colspan="7" class="text-center text-sm opacity-70 py-6">Пока нет адресов</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template>


