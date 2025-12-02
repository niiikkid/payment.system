<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import CurrencyNetworkBadge from '@/components/ui/CurrencyNetworkBadge.vue';
import AddressCopy from '@/components/ui/AddressCopy.vue';
import Alert from '@/components/ui/Alert.vue';
import FormControl from '@/components/form/FormControl.vue';
import Label from '@/components/form/Label.vue';
import Input from '@/components/form/Input.vue';
import Select from '@/components/form/Select.vue';

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
    createForm.address = createForm.address.trim();
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
        <div class="grid gap-6 lg:grid-cols-[380px_1fr]">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Добавить новый адрес</h2>
                    <form @submit.prevent="submitCreate" class="grid gap-4">
                        <Alert v-if="page.props.flash?.error" type="error" :message="page.props.flash.error" />
                        <Alert
                            v-if="Object.keys(createForm.errors).length"
                            type="error"
                            :message="createForm.errors.address || createForm.errors.network || createForm.errors.currency || 'Ошибка при добавлении адреса'"
                        />
                        <FormControl :error="createForm.errors.currency">
                            <Label for="currency" required>Валюта</Label>
                            <Select
                                id="currency"
                                v-model="createForm.currency"
                                :options="props.currencyOptions"
                                placeholder="Выберите валюту"
                                required
                            />
                        </FormControl>
                        <FormControl :error="createForm.errors.network">
                            <Label for="network" required>Сеть</Label>
                            <Select
                                id="network"
                                v-model="createForm.network"
                                :options="props.networkOptions"
                                placeholder="Выберите сеть"
                                required
                            />
                        </FormControl>
                        <FormControl :error="createForm.errors.address">
                            <Label for="address" required>Адрес</Label>
                            <Input
                                id="address"
                                v-model="createForm.address"
                                type="text"
                                placeholder="Например: T..."
                                required
                            />
                        </FormControl>
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


