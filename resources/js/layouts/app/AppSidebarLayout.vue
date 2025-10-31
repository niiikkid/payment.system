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
const isInvoicesActive = computed(() => page.url.startsWith('/invoices'));
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
                            <li>
                                <Link href="/invoices" :class="{ 'menu-active': isInvoicesActive, active: isInvoicesActive }" aria-current="page">
                                    <span class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 opacity-30">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        Инвойсы
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
