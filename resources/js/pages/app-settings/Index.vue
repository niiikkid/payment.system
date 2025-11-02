<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';

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

const breadcrumbs = computed(() => [
    { title: 'Панель управления', href: '/dashboard' },
    { title: 'App Settings' },
]);
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold">Глобальные настройки</h1>
            <p class="text-base-content/70">Минимальная и максимальная сумма для создания инвойса</p>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 xl:grid-cols-3">
                <div v-for="(item, idx) in form.settings" :key="item.currency" class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <h2 class="card-title">{{ item.currency }}</h2>
                        </div>

                        <div class="form-control">
                            <label class="label" :for="`min-${item.currency}`">
                                <span class="label-text">Минимальная сумма ({{ item.currency }})</span>
                            </label>
                            <input :id="`min-${item.currency}`" type="number" step="0.000001" min="0" class="input input-bordered" v-model.number="form.settings[idx].min_invoice_amount" />
                        </div>

                        <div class="form-control mt-4">
                            <label class="label" :for="`max-${item.currency}`">
                                <span class="label-text">Максимальная сумма ({{ item.currency }})</span>
                            </label>
                            <input :id="`max-${item.currency}`" type="number" step="0.000001" min="0" class="input input-bordered" v-model.number="form.settings[idx].max_invoice_amount" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-start">
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    Сохранить
                </button>
            </div>
        </form>
    </AppSidebarLayout>

</template>


