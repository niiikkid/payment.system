<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { computed } from 'vue';
import ApiTokens from './elements/ApiTokens.vue';
import ApiRequests from './elements/ApiRequests.vue';
import ApiDocumentation from './elements/ApiDocumentation.vue';

type PageProps = {
    publicApiKey: string;
    apiBaseUrl: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'API и документация', href: '/api' },
];

const page = usePage();
const props = computed(() => page.props as unknown as PageProps);

const apiKey = computed(() => props.value.publicApiKey || '');
const apiBase = computed(() => props.value.apiBaseUrl || '/api/v1');

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mt-6 space-y-8">
            <ApiTokens :api-key="apiKey" :api-base="apiBase" />
            <ApiRequests :api-key="apiKey" :api-base="apiBase" />
            <ApiDocumentation :api-base="apiBase" />
        </div>
    </AppLayout>
</template>




