<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/appearance';
import { Head } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Внешний вид', href: edit().url },
];

const themes = ['light', 'dark', 'cupcake', 'dim'];

function applyTheme(theme: string) {
    document.documentElement.setAttribute('data-theme', theme);
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Настройки внешнего вида" />

        <SettingsLayout>
            <div class="space-y-6">
                <div class="space-y-1">
                    <h2 class="text-lg font-medium">Внешний вид</h2>
                    <p class="text-sm text-base-content/70">Выберите тему интерфейса</p>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <button v-for="t in themes" :key="t" class="btn" @click="applyTheme(t)">
                        {{ t }}
                    </button>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
    
</template>


