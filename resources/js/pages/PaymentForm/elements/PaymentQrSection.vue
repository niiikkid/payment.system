<script setup lang="ts">
interface Props {
  qrUrl: string
  currencyLabel?: string | null
  currency: string
  networkLabel?: string | null
  network: string
  address?: string | null
}

const props = defineProps<Props>()
</script>

<template>
  <div>
    <div class="mb-2">
      <div class="text-xs text-center opacity-70 mt-1">
        Валюта: {{ props.currencyLabel || props.currency }} • Сеть: {{ props.networkLabel || props.network }}
      </div>
    </div>
    <div class="mx-auto rounded-box aspect-square w-40 sm:w-50 max-w-full overflow-hidden">
      <img
        v-if="props.address"
        :src="props.qrUrl"
        alt="QR для оплаты"
        class="w-full h-full object-contain"
        loading="eager"
        decoding="async"
      />
    </div>
  </div>
</template>

