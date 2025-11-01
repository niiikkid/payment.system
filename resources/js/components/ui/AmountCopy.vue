<script setup lang="ts">
import { ref, computed } from 'vue';

interface Props {
  amount: string | number;
}

const props = defineProps<Props>();
const amountStr = computed(() => String(props.amount ?? ''));

const tooltipText = ref('Скопировать сумму');
let resetTimer: number | undefined;

async function copyToClipboard() {
  try {
    await navigator.clipboard.writeText(amountStr.value);
    tooltipText.value = 'Скопировано';
  } catch (_) {
    tooltipText.value = 'Не удалось скопировать';
  } finally {
    if (resetTimer) clearTimeout(resetTimer);
    resetTimer = window.setTimeout(() => {
      tooltipText.value = 'Скопировать сумму';
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
    <button type="button" class="btn btn-ghost btn-sm font-mono" @click="copyToClipboard" @keydown="onKeydown">
      {{ amountStr }}
    </button>
  </div>
</template>


