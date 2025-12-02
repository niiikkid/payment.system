<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { BreadcrumbItem } from '@/types';
import FormControl from '@/components/form/FormControl.vue';
import Label from '@/components/form/Label.vue';
import Input from '@/components/form/Input.vue';

interface SettingItem {
    id?: number;
    currency: string;
    min_invoice_amount: number;
    max_invoice_amount: number;
}

interface PageProps {
    settings: SettingItem[];
    currencies: string[];
}

const props = defineProps<PageProps>();

const form = useForm({
    settings: props.currencies.map((c) => {
        const found = props.settings.find((s) => s.currency === c);
        return {
            currency: c,
            min_invoice_amount: found?.min_invoice_amount ?? 0,
            max_invoice_amount: found?.max_invoice_amount ?? 0,
        };
    }),
});

const submit = () => {
    form.put('/app-settings');
};

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Главная', href: '/dashboard' },
    { title: 'App Settings' },
]);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <form class="space-y-6" @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 xl:grid-cols-3">
                <div v-for="(item, idx) in form.settings" :key="item.currency" class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <h2 class="card-title">{{ item.currency }}</h2>
                        </div>

                        <FormControl>
                            <Label :for="`min-${item.currency}`">Минимальная сумма ({{ item.currency }})</Label>
                            <Input
                                :id="`min-${item.currency}`"
                                v-model="form.settings[idx].min_invoice_amount"
                                type="number"
                                step="0.000001"
                                min="0"
                            />
                        </FormControl>

                        <FormControl class="mt-4">
                            <Label :for="`max-${item.currency}`">Максимальная сумма ({{ item.currency }})</Label>
                            <Input
                                :id="`max-${item.currency}`"
                                v-model="form.settings[idx].max_invoice_amount"
                                type="number"
                                step="0.000001"
                                min="0"
                            />
                        </FormControl>
                    </div>
                </div>
            </div>

            <div class="flex justify-start">
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    Сохранить
                </button>
            </div>
        </form>
    </AppLayout>

</template>


