<script setup lang="ts">
import type { BreadcrumbItemType } from '@/types';
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const appName = (import.meta.env.VITE_APP_NAME as string) || 'Laravel';

const page = usePage();

const isDashboardActive = computed(() => page.url === '/dashboard');
const isAddressesActive = computed(() => page.url.startsWith('/addresses'));
</script>

<template>
    <div class="drawer lg:drawer-open bg-base-200 min-h-screen">
        <input id="app-drawer" type="checkbox" class="drawer-toggle" />
        <div class="drawer-content overflow-x-hidden">
            <div class="sticky top-0 z-10 border-b border-base-200 bg-base-100/60 px-4 py-3 backdrop-blur supports-[backdrop-filter]:bg-base-100/60 lg:px-6">
                <div class="navbar">
                    <div class="navbar-start">
                        <label for="app-drawer" class="btn btn-ghost btn-sm lg:hidden">☰</label>
                    </div>
                    <div class="navbar-center w-full">
                        <div class="breadcrumbs text-sm truncate">
                            <ul>
                                <li v-for="(bc, idx) in breadcrumbs" :key="idx">
                                    <a :href="bc.href" v-if="bc.href" class="link link-hover">{{ bc.title }}</a>
                                    <span v-else>{{ bc.title }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="navbar-end"></div>
                </div>
            </div>
            <div class="p-4 lg:p-6">
                <slot />
            </div>
        </div>
        <div class="drawer-side">
            <label for="app-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <aside class="min-h-full w-60 border-r border-base-200 bg-base-100">
                <div class="p-2">
                    <a href="/dashboard" class="block px-2 py-3 text-xl font-semibold tracking-wide text-center">
                        {{ appName }}
                    </a>
                    <div>
                        <ul class="menu w-full font-semibold space-y-1">
                            <li>
                                <Link href="/dashboard" :class="{ 'menu-active': isDashboardActive, active: isDashboardActive }" aria-current="page">
                                    <span class="flex items-center gap-2 font-semibold">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 opacity-30">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                        </svg>
                                        Панель управления
                                    </span>
                                </Link>
                            </li>
                            <li>
                                <Link href="/addresses" :class="{ 'menu-active': isAddressesActive, active: isAddressesActive }" aria-current="page">
                                    <span class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 opacity-30">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                        </svg>
                                        Адреса
                                    </span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </div>

</template>
