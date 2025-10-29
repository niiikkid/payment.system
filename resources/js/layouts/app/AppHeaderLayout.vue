<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell class="flex-col bg-base-200">
        <div class="bg-base-100/60 backdrop-blur supports-[backdrop-filter]:bg-base-100/60 sticky top-0 z-10 border-b border-base-200">
            <AppHeader :breadcrumbs="breadcrumbs" />
        </div>
        <AppContent>
            <div class="p-4 lg:p-6">
                <div class="rounded-box bg-base-100 shadow-sm p-4 lg:p-6">
                    <slot />
                </div>
            </div>
        </AppContent>
    </AppShell>
</template>
