<script setup lang="ts">
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import FlagIcon from './FlagIcon.vue';

type LocaleOption = {
    code: string;
    label: string;
    flag: string;
};

const props = defineProps<{
    direction?: 'up' | 'down' | 'left' | 'right';
    align?: 'start' | 'center' | 'end';
}>();

const locales: LocaleOption[] = [
    { code: 'ru', label: 'Русский', flag: 'RU' },
    { code: 'az', label: 'Azərbaycan', flag: 'AZ' },
    { code: 'kk', label: 'Қазақша', flag: 'KZ' },
    { code: 'ky', label: 'Кыргызча', flag: 'KG' },
    { code: 'uz', label: "O'zbekcha", flag: 'UZ' },
    { code: 'uk', label: 'Українська', flag: 'UA' },
    { code: 'tr', label: 'Türkçe', flag: 'TR' },
    { code: 'de', label: 'Deutsch', flag: 'DE' },
    { code: 'pl', label: 'Polski', flag: 'PL' },
    { code: 'fr', label: 'Français', flag: 'FR' },
    { code: 'it', label: 'Italiano', flag: 'IT' },
    { code: 'es', label: 'Español', flag: 'ES' },
    { code: 'es-MX', label: 'Español (México)', flag: 'MX' },
    { code: 'pt', label: 'Português', flag: 'PT' },
    { code: 'en', label: 'English', flag: 'US' },
    { code: 'ar', label: 'العربية', flag: 'AE' },
    { code: 'hi', label: 'हिन्दी', flag: 'IN' },
    { code: 'zh', label: '中文', flag: 'CN' },
];

const page = usePage();

const currentLocale = computed(() => ((page.props as any)?.locale as string) || 'ru');
const current = computed<LocaleOption>(() => locales.find((l) => l.code === currentLocale.value) ?? locales[0]);
const dropdownDirectionClass = computed(() => {
    switch (props.direction) {
        case 'down':
            return 'dropdown-bottom';
        case 'left':
            return 'dropdown-left';
        case 'right':
            return 'dropdown-right';
        case 'up':
        default:
            return 'dropdown-top';
    }
});
const dropdownAlignClass = computed(() => {
    switch (props.align) {
        case 'center':
            return 'dropdown-center';
        case 'end':
            return 'dropdown-end';
        case 'start':
        default:
            return 'dropdown-start';
    }
});

function switchLocale(code: string) {
    if (code === currentLocale.value) return;
    router.get(`/lang/${code}`, {}, { preserveScroll: true, preserveState: false });
}
</script>

<template>
    <div class="dropdown w-full" :class="[dropdownDirectionClass, dropdownAlignClass]">
        <div tabindex="0" role="button" class="btn btn-ghost w-full justify-start">
            <FlagIcon :code="current.flag" size="M" class="mr-2" />
            <span class="font-semibold">{{ current.label }}</span>
        </div>
        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow">
            <li v-for="locale in locales" :key="locale.code">
                <button type="button" class="flex items-center gap-2" :class="{ active: locale.code === currentLocale }" @click="switchLocale(locale.code)">
                    <FlagIcon :code="locale.flag" size="S" />
                    <span>{{ locale.label }}</span>
                </button>
            </li>
        </ul>
    </div>
</template>

