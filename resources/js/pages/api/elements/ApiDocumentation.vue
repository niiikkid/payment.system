<script setup lang="ts">
import { vueLang } from '@erag/lang-sync-inertia';

interface Props {
    apiBase: string;
}

const { __ } = vueLang();

defineProps<Props>();
</script>

<template>
    <div class="card bg-base-100 shadow">
        <div class="card-body space-y-2">
            <h3 class="card-title">{{ __('frontend.api.documentation.title') }}</h3>
            <p class="text-sm">
                {{ __('frontend.api.documentation.subtitle', { base: apiBase }) }}
            </p>
            <ul class="list-disc list-inside text-sm space-y-1">
                <li>{{ __('frontend.api.documentation.endpoints.create') }}</li>
                <li>{{ __('frontend.api.documentation.endpoints.list') }}</li>
                <li>{{ __('frontend.api.documentation.endpoints.get') }}</li>
                <li>{{ __('frontend.api.documentation.endpoints.status') }}</li>
                <li>{{ __('frontend.api.documentation.endpoints.public') }}</li>
                <li>{{ __('frontend.api.documentation.endpoints.qr') }}</li>
                <li>{{ __('frontend.api.documentation.endpoints.cancel') }}</li>
                <li>{{ __('frontend.api.documentation.endpoints.merchants') }}</li>
                <li>{{ __('frontend.api.documentation.endpoints.clients_get') }}</li>
                <li>{{ __('frontend.api.documentation.endpoints.clients_post') }}</li>
            </ul>
            <p class="text-sm text-info">
                {{ __('frontend.api.documentation.accept_hint') }}
            </p>
        </div>
    </div>
</template>

