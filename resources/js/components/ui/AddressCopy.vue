<script setup lang="ts">
import { computed, ref } from 'vue';

interface Props {
    address: string;
}

const props = defineProps<Props>();

const tooltipText = ref('Скопировать');
let resetTimer: number | undefined;

const truncatedAddress = computed(() => {
    const src = (props.address ?? '').trim();
    if (src.length <= 12) return src;
    const start = src.slice(0, 6);
    const end = src.slice(-6);
    return `${start}..${end}`; // две точки по требованию
});

async function copyToClipboard() {
    try {
        await navigator.clipboard.writeText(props.address);
        tooltipText.value = 'Скопировано';
    } catch (_) {
        tooltipText.value = 'Не удалось скопировать';
    } finally {
        if (resetTimer) clearTimeout(resetTimer);
        resetTimer = window.setTimeout(() => {
            tooltipText.value = 'Скопировать';
        }, 1500);
    }
}

function onKeydown(e: KeyboardEvent) {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        copyToClipboard();
    }
}
</script>

<template>
    <div class="tooltip" :data-tip="tooltipText">
        <button
            type="button"
            class="btn btn-ghost btn-xs font-mono text-xs"
            :title="props.address"
            @click="copyToClipboard"
            @keydown="onKeydown"
        >
            {{ truncatedAddress }}
        </button>
    </div>
</template>


