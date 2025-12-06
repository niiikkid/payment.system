<script setup lang="ts">
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import FlagIcon from './FlagIcon.vue';

type LocaleOption = {
    code: string;
    label: string;
    flag: string;
};

const locales: LocaleOption[] = [
    { code: 'ru', label: 'Русский', flag: 'RU' },
    { code: 'en', label: 'English', flag: 'US' },
];

const page = usePage();

const currentLocale = computed(() => ((page.props as any)?.locale as string) || 'ru');
const current = computed<LocaleOption>(() => locales.find((l) => l.code === currentLocale.value) ?? locales[0]);

function switchLocale(code: string) {
    if (code === currentLocale.value) return;
    router.get(`/lang/${code}`, {}, { preserveScroll: true, preserveState: false });
}
</script>

<template>
    <div class="dropdown dropdown-end w-full">
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

