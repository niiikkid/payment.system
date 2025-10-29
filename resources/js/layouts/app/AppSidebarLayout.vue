<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar" class="bg-base-200">
        <AppSidebar class="bg-base-100 border-r border-base-200" />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <div class="bg-base-100/60 backdrop-blur supports-[backdrop-filter]:bg-base-100/60 sticky top-0 z-10 border-b border-base-200">
                <div class="px-4 py-3 lg:px-6">
                    <AppSidebarHeader :breadcrumbs="breadcrumbs" />
                </div>
            </div>
            <div class="p-4 lg:p-6">
                <div class="rounded-box bg-base-100 shadow-sm p-4 lg:p-6">
                    <slot />
                </div>
            </div>
        </AppContent>
    </AppShell>
</template>
