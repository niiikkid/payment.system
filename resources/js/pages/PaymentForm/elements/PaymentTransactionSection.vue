<script setup lang="ts">
interface Props {
  txid: string | null
  txExplorerUrl?: string | null
}

const props = defineProps<Props>()
</script>

<template>
  <div class="grid gap-2" v-if="props.txid">
    <div class="text-base opacity-60">Транзакция</div>
    <div class="flex items-center gap-2">
      <a v-if="props.txExplorerUrl" :href="props.txExplorerUrl || '#'" target="_blank" rel="noopener noreferrer" class="link link-primary">Обозреватель</a>
    </div>
  </div>
</template>

