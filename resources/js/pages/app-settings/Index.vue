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
    { title: 'Главная', href: '/dashboard' },
    { title: 'App Settings' },
]);
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-xl font-semibold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 opacity-60">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                Глобальные настройки
            </h1>
        </div>

        <form class="mt-6 space-y-6" @submit.prevent="submit">
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


