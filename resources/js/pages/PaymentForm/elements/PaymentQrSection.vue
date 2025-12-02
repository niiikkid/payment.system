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
      <div class="flex items-center gap-2 mb-3">
        <span class="text-error">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
          </svg>
        </span>
        <span class="text-base-content">Важно: переведите сумму точно до копейки, чтобы мы автоматически зачли ваш платёж.</span>
      </div>
      <div class="text-xs text-center opacity-70 mt-1">
        Валюта: {{ props.currencyLabel || props.currency }} • Сеть: {{ props.networkLabel || props.network }}
      </div>
    </div>
    <div class="mx-auto rounded-box bg-neutral/90 aspect-square w-50 max-w-full overflow-hidden">
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

