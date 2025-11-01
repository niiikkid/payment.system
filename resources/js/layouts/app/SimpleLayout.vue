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
    <div class="flex min-h-screen flex-col bg-base-200">
        <div class="sticky top-0 z-10 border-b border-base-200 bg-base-100/60 backdrop-blur supports-[backdrop-filter]:bg-base-100/60">
            <div class="mx-auto max-w-7xl px-4 py-3 lg:px-6">
                <div class="breadcrumbs text-sm">
                    <ul>
                        <li v-for="(bc, idx) in breadcrumbs" :key="idx">
                            <a :href="bc.href" v-if="bc.href" class="link link-hover">{{ bc.title }}</a>
                            <span v-else>{{ bc.title }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="flex-1">
            <div class="mx-auto max-w-7xl p-4 lg:p-6">
                <div class="rounded-box bg-base-100 p-4 shadow-sm lg:p-6">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
