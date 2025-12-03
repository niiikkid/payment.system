<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/appearance';
import { Head } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { ref, onMounted } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Внешний вид', href: edit().url },
];

// Полный список тем DaisyUI + дополнительные
const themes = [
    'light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk',
    'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black',
    'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter',
    'dim', 'nord', 'sunset', 'caramellatte', 'abyss', 'silk'
] as const;

type ThemeName = typeof themes[number];

const storedTheme = typeof window !== 'undefined' ? window.localStorage.getItem('theme') : null;
const initialTheme = (storedTheme && themes.includes(storedTheme as ThemeName))
    ? (storedTheme as ThemeName)
    : (document.querySelector('html')?.getAttribute('data-theme') as ThemeName | null);

const currentTheme = ref<ThemeName | null>(initialTheme ?? null);

function applyTheme(theme: ThemeName) {
    if (!themes.includes(theme)) return;
    const html = document.documentElement;
    html.setAttribute('data-theme', theme);
    currentTheme.value = theme;
    try {
        window.localStorage.setItem('theme', theme);
    } catch (e) {
        // ignore storage errors
    }
}

onMounted(() => {
    const saved = typeof window !== 'undefined' ? window.localStorage.getItem('theme') : null;
    const themeToApply = (saved && themes.includes(saved as ThemeName))
        ? (saved as ThemeName)
        : (currentTheme.value ?? themes[0]);
    if (themeToApply) {
        document.documentElement.setAttribute('data-theme', themeToApply);
        currentTheme.value = themeToApply;
    }
});
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

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    <div
                        class="cursor-pointer"
                        v-for="t in themes"
                        :key="t"
                        @click="applyTheme(t)"
                    >
                        <button
                            type="button"
                            class="w-full p-3 rounded-lg border bg-base-200 hover:border-primary transition"
                            :class="currentTheme === t ? 'border-primary ring-2 ring-primary' : 'border-base-300'"
                            :title="t"
                            :aria-label="'Тема ' + t"
                            :aria-pressed="currentTheme === t"
                            :data-theme="t"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-base-content">{{ t }}</span>
                                <div class="grid grid-cols-4 gap-1">
                                    <span class="w-3 h-3 rounded-full bg-primary"></span>
                                    <span class="w-3 h-3 rounded-full bg-secondary"></span>
                                    <span class="w-3 h-3 rounded-full bg-accent"></span>
                                    <span class="w-3 h-3 rounded-full bg-neutral"></span>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>

</template>


