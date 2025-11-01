<script setup lang="ts">
import SimpleLayout from '@/layouts/app/SimpleLayout.vue';
import AddressFullCopy from '@/components/ui/AddressFullCopy.vue';
import CurrencyNetworkBadge from '@/components/ui/CurrencyNetworkBadge.vue';
import AmountCopy from '@/components/ui/AmountCopy.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import axios from 'axios';

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
  if (s === 'expired') return 'badge-ghost';
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

function parseLocalDateTimeToMs(s: string | null): number | null {
  if (!s) return null;
  const parts = s.trim().split(' ');
  if (parts.length !== 2) return null;
  const [d, t] = parts;
  const dParts = d.split('-').map(n => parseInt(n, 10));
  const tParts = t.split(':').map(n => parseInt(n, 10));
  if (dParts.length !== 3 || tParts.length < 2) return null;
  const [year, month, day] = dParts;
  const [hours, minutes] = tParts;
  const seconds = tParts[2] ?? 0;
  const ms = new Date(year, (month - 1), day, hours, minutes, seconds, 0).getTime();
  return Number.isFinite(ms) ? ms : null;
}

const expiresAtMs = computed(() => parseLocalDateTimeToMs(invoice.value.expires_at));

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
}

function startCountdown() {
  tickCountdown();
  if (countdownTimer) clearInterval(countdownTimer);
  countdownTimer = window.setInterval(tickCountdown, 1000);
}
</script>

<template>
  <SimpleLayout>
    <div class="min-h-screen flex items-center">
        <div class="mx-auto max-w-7xl p-4 lg:p-6">
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-xl font-semibold">Платёж</h1>
                <div class="badge" :class="statusBadgeClass">{{ invoice.status }}</div>
            </div>

    <div class="mt-8 card bg-base-100 shadow">
                <div class="card-body p-4 lg:p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Левая колонка: магазин и платёж -->
                        <div class="grid gap-4">
                            <div>
                                <div class="text-xs opacity-60">Магазин</div>
                                <div class="mt-1 flex items-center gap-3">
                                    <img src="/favicon.svg" alt="logo" class="size-7 opacity-70" />
                                    <div class="font-semibold">{{ appName }}</div>
                                </div>
                            </div>

                            <div class="grid gap-3">
                                <div class="text-xs opacity-60">К оплате</div>
              <div class="flex items-center gap-3">
                <div class="text-2xl font-bold font-mono">
                  <AmountCopy :amount="invoice.amount" />
                </div>
                                    <CurrencyNetworkBadge :currency-label="invoice.currency_label || invoice.currency" :network-label="invoice.network_label || invoice.network" />
                                </div>
                                <div class="text-sm opacity-70" v-if="invoice.expires_at">
                                    Оплатите до: {{ invoice.expires_at }}
                                </div>
                            </div>

                            <div class="grid gap-2">
                                <div class="text-xs opacity-60">Статус</div>
                                <div class="flex items-center gap-2">
                                    <span class="badge" :class="statusBadgeClass">{{ statusText }}</span>
                                </div>
                            </div>

            <div class="grid gap-2" v-if="invoice.external_invoice_id">
              <div class="text-xs opacity-60">ID заказа</div>
              <div class="font-mono break-all">{{ invoice.external_invoice_id }}</div>
            </div>

            <div class="grid gap-2" v-if="invoice.tag">
              <div class="text-xs opacity-60">Тег</div>
              <div class="font-mono break-all">{{ invoice.tag }}</div>
            </div>
                        </div>

          <!-- Правая колонка: реквизиты инвойса -->
          <div class="grid gap-4">
            <!-- Placeholder под QR-код -->
            <div>
              <div class="text-xs opacity-60 mb-2">QR-code</div>
              <div class="mx-auto rounded-box bg-neutral/90 aspect-square w-64 max-w-full grid place-content-center text-neutral-content select-none">
                <span class="text-sm opacity-80">QR будет здесь</span>
              </div>
            </div>

            <!-- К оплате и сеть -->
            <div class="grid gap-3">
              <div class="text-xs opacity-60">К оплате</div>
              <div class="flex items-center gap-3">
                <div class="text-2xl font-bold font-mono">
                  <AmountCopy :amount="invoice.amount" />
                </div>
                <CurrencyNetworkBadge :currency-label="invoice.currency_label || invoice.currency" :network-label="invoice.network_label || invoice.network" />
              </div>
            </div>

            <!-- Отсчёт времени -->
            <div class="grid gap-2" v-if="invoice.expires_at">
              <div class="text-xs opacity-60">Осталось времени</div>
              <span class="countdown font-mono text-xl">
                <span :style="`--value:${hh};`" :aria-label="String(hh)">{{ hh }}</span>
                :
                <span :style="`--value:${mm}; --digits: 2;`" :aria-label="String(mm)">{{ String(mm).padStart(2,'0') }}</span>
                :
                <span :style="`--value:${ss}; --digits: 2;`" :aria-label="String(ss)">{{ String(ss).padStart(2,'0') }}</span>
              </span>
            </div>

            <div class="grid gap-2" v-if="invoice.address">
              <div class="text-xs opacity-60">Адрес для оплаты</div>
              <div class="font-mono">
                <AddressFullCopy :address="invoice.address" />
              </div>
            </div>

            <div class="grid gap-2" v-if="(invoice.amount_received && Number(invoice.amount_received) > 0) || (invoice.confirmations && Number(invoice.confirmations) > 0)">
              <div class="text-xs opacity-60">Получено / Подтв.</div>
              <div class="font-mono">{{ invoice.amount_received }} / {{ invoice.confirmations }}</div>
            </div>

            <div class="grid gap-2" v-if="invoice.txid">
              <div class="text-xs opacity-60">Транзакция</div>
              <div class="flex items-center gap-2">
                <span class="font-mono break-all">{{ invoice.txid }}</span>
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


