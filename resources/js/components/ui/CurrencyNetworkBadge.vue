<script setup lang="ts">
import { computed } from 'vue';
interface Props {
    currencyLabel: string;
    networkLabel: string;
    size?: 'md' | 'lg';
}

const props = defineProps<Props>();

const size = computed(() => props.size ?? 'md');

const currencyClass = computed(() => {
    return size.value === 'lg' ? 'text-lg' : '';
});

const networkBadgeClass = computed(() => {
    // current default look uses badge-xs; for lg make it one step larger
    return size.value === 'lg' ? 'badge badge-sm badge-neutral' : 'badge badge-xs badge-neutral';
});

const formattedNetworkLabel = computed(() => {
    const source = (props.networkLabel ?? '').trim();
    if (source.length === 0) return '';
    const lower = source.toLowerCase();
    return lower.charAt(0).toUpperCase() + lower.slice(1);
});
</script>

<template>
    <span class="space-x-1 flex items-center">
        <span :class="currencyClass">{{ props.currencyLabel }}</span>
        <span :class="networkBadgeClass">{{ formattedNetworkLabel }}</span>
    </span>
</template>


