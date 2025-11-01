<script setup lang="ts">
import SimpleLayout from '@/layouts/app/SimpleLayout.vue';
import CurrencyNetworkBadge from '@/components/ui/CurrencyNetworkBadge.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import axios from 'axios';
import UidCopy from '@/components/ui/UidCopy.vue';

type Invoice = {
  id: string
  external_invoice_id: string | null
  address_id: number
  address?: string | null
  amount: number
  currency: string
  currency_label?: string
  network: string
  network_label?: string
  status: 'pending' | 'processing' | 'paid' | 'expired' | 'cancelled' | string
  txid: string | null
  tx_explorer_url?: string | null
  amount_received: number
  confirmations: number
  expires_at: string | null
  callback_url: string | null
  tag: string | null
  metadata: Record<string, any> | null
  created_at: string | null
  updated_at: string | null
}

const page = usePage();
const appName = computed(() => (page.props.appName as string) || (import.meta.env.VITE_APP_NAME as string) || 'App');
const initial = page.props.invoice as Invoice;
const statuses = computed(() => page.props.statuses as { active: string[]; final: string[] });

const invoice = ref<Invoice>(initial);

const qrUrl = computed(() => `/pay/${invoice.value.id}/qr`);

const isFinal = computed(() => statuses.value.final?.includes(invoice.value.status) ?? false);

let timer: number | null = null;
let countdownTimer: number | null = null;

async function refresh() {
  try {
    const { data } = await axios.get(`/pay/${invoice.value.id}/data`);
    invoice.value = data as Invoice;
    // обновим отсчёт на случай изменения expires_at
    tickCountdown();
    if (statuses.value.final?.includes(invoice.value.status)) {
      if (timer) {
        clearInterval(timer);
        timer = null;
      }
      if (countdownTimer) {
        clearInterval(countdownTimer);
        countdownTimer = null;
      }
    }
  } catch {}
}

onMounted(() => {
  timer = window.setInterval(refresh, 10000);
  startCountdown();
});

onBeforeUnmount(() => {
  if (timer) clearInterval(timer);
  if (countdownTimer) clearInterval(countdownTimer);
});

const statusBadgeClass = computed(() => {
  const s = invoice.value.status;
  if (s === 'paid') return 'badge-success';
  if (s === 'cancelled') return 'badge-error';
  if (s === 'expired') return 'badge-error';
  return 'badge-warning';
});

const statusText = computed(() => {
  const s = invoice.value.status;
  if (s === 'paid') return 'Оплачено';
  if (s === 'processing') return 'Транзакция найдена, ждём подтверждения';
  if (s === 'expired') return 'Срок оплаты истёк';
  if (s === 'cancelled') return 'Отменено';
  return 'Ожидаем оплату';
});

function parseIsoToMs(s: string | null): number | null {
  if (!s) return null;
  const ms = Date.parse(s);
  return Number.isFinite(ms) ? ms : null;
}

const expiresAtMs = computed(() => parseIsoToMs(invoice.value.expires_at));
const remainingSeconds = ref(0);
const hh = computed(() => Math.floor(remainingSeconds.value / 3600));
const mm = computed(() => Math.floor((remainingSeconds.value % 3600) / 60));
const ss = computed(() => remainingSeconds.value % 60);

function tickCountdown() {
  if (!expiresAtMs.value) {
    remainingSeconds.value = 0;
    return;
  }
  const diff = Math.max(0, Math.floor((expiresAtMs.value - Date.now()) / 1000));
  remainingSeconds.value = diff;
  if (diff === 0 && countdownTimer) {
    clearInterval(countdownTimer);
    countdownTimer = null;
  }
}

function startCountdown() {
  tickCountdown();
  if (countdownTimer) clearInterval(countdownTimer);
  countdownTimer = window.setInterval(tickCountdown, 1000);
}

// Inline AmountCopy logic
const amountTooltipText = ref('Скопировать сумму');
let amountResetTimer: number | undefined;
async function copyAmount() {
  try {
    await navigator.clipboard.writeText(String(invoice.value.amount ?? ''));
    amountTooltipText.value = 'Скопировано';
  } catch (_) {
    amountTooltipText.value = 'Не удалось скопировать';
  } finally {
    if (amountResetTimer) clearTimeout(amountResetTimer);
    amountResetTimer = window.setTimeout(() => {
      amountTooltipText.value = 'Скопировать сумму';
    }, 1500);
  }
}
function onAmountKeydown(e: KeyboardEvent) {
  if (e.key === 'Enter' || e.key === ' ') {
    e.preventDefault();
    copyAmount();
  }
}

// Inline AddressFullCopy logic
const addressTooltipText = ref('Скопировать');
let addressResetTimer: number | undefined;
async function copyAddress() {
  try {
    await navigator.clipboard.writeText(String(invoice.value.address ?? ''));
    addressTooltipText.value = 'Скопировано';
  } catch (_) {
    addressTooltipText.value = 'Не удалось скопировать';
  } finally {
    if (addressResetTimer) clearTimeout(addressResetTimer);
    addressResetTimer = window.setTimeout(() => {
      addressTooltipText.value = 'Скопировать';
    }, 1500);
  }
}
function onAddressKeydown(e: KeyboardEvent) {
  if (e.key === 'Enter' || e.key === ' ') {
    e.preventDefault();
    copyAddress();
  }
}
</script>

<template>
  <SimpleLayout>
    <div class="min-h-screen flex items-center">
        <div class="mx-auto max-w-7xl py-6 grid gap-4">
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-xl font-semibold flex items-center">Платёж ID: <UidCopy v-if="invoice?.id" :uid="invoice.id" size="xl" class="text-primary" /></h1>
                <div class="badge" :class="statusBadgeClass">{{ statusText }}</div>
            </div>

            <!-- Упрощённая карточка для оплаченного инвойса -->
            <div v-if="invoice.status === 'paid'" class="card bg-base-100 shadow">
                <div class="card-body p-6 lg:p-10">
                    <div class="flex flex-col items-center text-center gap-2">
                        <div class="text-success">
                            <!-- Иконка успеха -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-32">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <div class="text-2xl font-semibold">Оплачено</div>
                        <div class="flex items-center gap-2" v-if="invoice.tx_explorer_url">
                            <a :href="invoice.tx_explorer_url" target="_blank" rel="noopener noreferrer" class="link link-primary inline-flex items-center gap-1">
                                <span>Открыть в обозревателе</span>
                                <!-- Иконка внешней ссылки справа -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Упрощённая карточка для истёкшего инвойса -->
            <div v-else-if="invoice.status === 'expired'" class="card bg-base-100 shadow">
                <div class="card-body p-6 lg:p-10">
                    <div class="flex flex-col items-center text-center gap-2">
                        <div class="text-error">
                            <!-- Иконка опасности (X в круге) -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-32">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <div class="text-2xl font-semibold">Срок оплаты истёк</div>
                    </div>
                </div>
            </div>

            <!-- Обычная карточка для остальных статусов -->
            <div v-else class="card bg-base-100 shadow">
                <div class="card-body p-4 lg:p-6">
                    <div class="grid gap-6 md:grid-cols-1">

                        <div class="grid gap-4">
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
                                  Валюта: {{ invoice.currency_label || invoice.currency }} • Сеть: {{ invoice.network_label || invoice.network }}
                                </div>
                              </div>
                              <div class="mx-auto rounded-box bg-neutral/90 aspect-square w-50 max-w-full overflow-hidden">
                                <img
                                  v-if="invoice.address"
                                  :src="qrUrl"
                                  alt="QR для оплаты"
                                  class="w-full h-full object-contain"
                                  loading="eager"
                                  decoding="async"
                                />
                              </div>
                            </div>

                            <div class="grid gap-2">
                              <div class="text-base opacity-60">К оплате</div>
                              <div class="flex items-center gap-3">
                                <div>
                                  <div class="tooltip" :data-tip="amountTooltipText">
                                    <div
                                      class="group text-lg min-h-0 flex items-center justify-between gap-2 font-mono cursor-pointer transition-colors"
                                      tabindex="0"
                                      @click="copyAmount"
                                      @keydown="onAmountKeydown"
                                    >
                                      <span>{{ String(invoice.amount) }}</span>
                                      <span class="shrink-0 text-info opacity-70 group-hover:opacity-100 transition-opacity" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                        </svg>
                                      </span>
                                    </div>
                                  </div>
                                </div>
                                <CurrencyNetworkBadge
                                    :currency-label="invoice.currency_label || invoice.currency"
                                    :network-label="invoice.network_label || invoice.network"
                                    size="lg"
                                />
                              </div>
                            </div>

                            <div class="grid gap-2" v-if="invoice.address">
                                <div class="text-base opacity-60">Адрес для оплаты</div>
                                <div class="font-mono">
                                    <div class="tooltip" :data-tip="addressTooltipText">
                                        <div
                                            class="group text-lg min-h-0 flex items-center justify-between gap-2 font-mono cursor-pointer transition-colors"
                                            tabindex="0"
                                            @click="copyAddress"
                                            @keydown="onAddressKeydown"
                                        >
                                            <span class="break-all">{{ invoice.address }}</span>
                                            <span class="shrink-0 text-info opacity-70 group-hover:opacity-100 transition-opacity" aria-hidden="true">
                                              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                              </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-2" v-if="invoice.expires_at">
                              <div class="text-base opacity-60">Осталось времени</div>
                              <span class="countdown font-mono text-xl">
                                <span :style="`--value:${hh};`" :aria-label="String(hh)" aria-live="polite">{{ hh }}</span>
                                :
                                <span :style="`--value:${mm}; --digits: 2;`" :aria-label="String(mm)" aria-live="polite">{{ String(mm).padStart(2,'0') }}</span>
                                :
                                <span :style="`--value:${ss}; --digits: 2;`" :aria-label="String(ss)" aria-live="polite">{{ String(ss).padStart(2,'0') }}</span>
                              </span>
                            </div>

                            <div class="grid gap-2" v-if="invoice.txid">
                              <div class="text-base opacity-60">Транзакция</div>
                              <div class="flex items-center gap-2">
                                <a v-if="invoice.tx_explorer_url" :href="invoice.tx_explorer_url || '#'" target="_blank" rel="noopener noreferrer" class="link link-primary">Обозреватель</a>
                              </div>
                            </div>

                            <div class="divider my-0"></div>
                            <div class="text-sm opacity-70">
                                - Переведите точную сумму на указанный адрес.<br />
                                - Сеть должна соответствовать указанной сетке.<br />
                                - После отправки страница обновится автоматически.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </SimpleLayout>

</template>


