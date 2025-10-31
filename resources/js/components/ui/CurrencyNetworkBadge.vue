<script setup lang="ts">
import { computed } from 'vue';
interface Props {
    currencyLabel: string;
    networkLabel: string;
}

const props = defineProps<Props>();

const formattedNetworkLabel = computed(() => {
    const source = (props.networkLabel ?? '').trim();
    if (source.length === 0) return '';
    const lower = source.toLowerCase();
    return lower.charAt(0).toUpperCase() + lower.slice(1);
});
</script>

<template>
    <span class="space-x-1">
        <span class="font-semibold">{{ props.currencyLabel }}</span>
        <span class="font-semibold badge badge-xs badge-neutral">{{ formattedNetworkLabel }}</span>
    </span>

</template>


