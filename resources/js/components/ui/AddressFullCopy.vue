<script setup lang="ts">
import { ref } from 'vue';

interface Props {
  address: string;
}

const props = defineProps<Props>();

const tooltipText = ref('Скопировать');
let resetTimer: number | undefined;

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
      class="btn btn-ghost btn-xs font-mono break-all"
      :title="props.address"
      @click="copyToClipboard"
      @keydown="onKeydown"
    >
      {{ props.address }}
    </button>
  </div>
</template>


