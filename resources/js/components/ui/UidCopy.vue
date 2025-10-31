<script setup lang="ts">
import { computed, ref } from 'vue';

interface Props {
    uid: string;
}

const props = defineProps<Props>();

const tooltipText = ref('Скопировать');
let resetTimer: number | undefined;

const shortened = computed(() => {
    const src = (props.uid ?? '').trim();
    if (src.length <= 8) return src;
    const end = src.slice(-8);
    return `${end}`; // многоточие + последние 8 символов
});

async function copyToClipboard() {
    try {
        await navigator.clipboard.writeText(props.uid);
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
            :title="props.uid"
            @click="copyToClipboard"
            @keydown="onKeydown"
        >
            {{ shortened }}
        </button>
    </div>

</template>


