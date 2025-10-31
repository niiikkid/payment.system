<script setup lang="ts">
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <div class="grid min-h-screen grid-cols-1 bg-base-200 lg:grid-cols-[260px_1fr]">
        <aside class="hidden border-base-200 bg-base-100 lg:block lg:border-r">
            <div class="p-4 lg:p-6">
                <nav class="menu">
                    <ul>
                        <li class="menu-title">Навигация</li>
                        <!-- Добавьте ссылки меню при необходимости -->
                    </ul>
                </nav>
            </div>
        </aside>
        <main class="overflow-x-hidden">
            <div class="sticky top-0 z-10 border-b border-base-200 bg-base-100/60 px-4 py-3 backdrop-blur supports-[backdrop-filter]:bg-base-100/60 lg:px-6">
                <div class="breadcrumbs text-sm">
                    <ul>
                        <li v-for="(bc, idx) in breadcrumbs" :key="idx">
                            <a :href="bc.href" v-if="bc.href" class="link link-hover">{{ bc.title }}</a>
                            <span v-else>{{ bc.title }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="p-4 lg:p-6">
                <slot />
            </div>
        </main>
    </div>
</template>
