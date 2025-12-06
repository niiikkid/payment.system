<script setup lang="ts">
import PaymentFormLayout from '@/layouts/PaymentFormLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import axios from 'axios';
import UidCopy from '@/components/ui/UidCopy.vue';
import PaidInvoiceCard from './elements/PaidInvoiceCard.vue';
import ExpiredInvoiceCard from './elements/ExpiredInvoiceCard.vue';
import PaymentQrSection from './elements/PaymentQrSection.vue';
import PaymentAmountSection from './elements/PaymentAmountSection.vue';
import PaymentAddressSection from './elements/PaymentAddressSection.vue';
import PaymentCountdownSection from './elements/PaymentCountdownSection.vue';
import PaymentTransactionSection from './elements/PaymentTransactionSection.vue';
import { vueLang } from '@erag/lang-sync-inertia';

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
const { __ } = vueLang();

const invoice = ref<Invoice>(initial);

const qrUrl = computed(() => `/pay/${invoice.value.id}/qr`);

const isFinal = computed(() => statuses.value.final?.includes(invoice.value.status) ?? false);

let timer: number | null = null;

async function refresh() {
  try {
    const { data } = await axios.get(`/pay/${invoice.value.id}/data`);
    invoice.value = data as Invoice;
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
});

onBeforeUnmount(() => {
  if (timer) clearInterval(timer);
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
  if (s === 'paid') return __('frontend.payment_form.status.paid');
  if (s === 'processing') return __('frontend.payment_form.status.processing');
  if (s === 'expired') return __('frontend.payment_form.status.expired');
  if (s === 'cancelled') return __('frontend.payment_form.status.cancelled');
  return __('frontend.payment_form.status.pending');
});

</script>

<template>
  <PaymentFormLayout>
    <div class="min-h-screen flex items-center mx-4">
        <div class="mx-auto max-w-7xl py-6 grid gap-4">
            <div class="sm:flex items-center justify-between gap-4">
                <h1 class="text-xl font-semibold flex items-center gap-2">
                    {{ __('frontend.payment_form.page_title', { id: '' }) }}
                    <UidCopy v-if="invoice?.id" :uid="invoice.id" size="xl" class="text-primary" />
                </h1>
                <div class="badge" :class="statusBadgeClass">{{ statusText }}</div>
            </div>

            <PaidInvoiceCard v-if="invoice.status === 'paid'" :tx-explorer-url="invoice.tx_explorer_url" />

            <ExpiredInvoiceCard v-else-if="invoice.status === 'expired'" />

            <!-- Обычная карточка для остальных статусов -->
            <div v-else class="card bg-base-100 shadow">
                <div class="card-body p-4 lg:p-6">
                    <div class="grid gap-6 md:grid-cols-1">
                        <div class="grid gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-error">
                                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                  </svg>
                                </span>
                                <span class="text-base-content text-xs sm:text-sm lg:text-base">{{ __('frontend.payment_form.important') }}</span>
                            </div>
                            <PaymentQrSection
                                :qr-url="qrUrl"
                                :currency-label="invoice.currency_label"
                                :currency="invoice.currency"
                                :network-label="invoice.network_label"
                                :network="invoice.network"
                                :address="invoice.address"
                            />

                            <PaymentAmountSection
                                :amount="invoice.amount"
                                :currency-label="invoice.currency_label"
                                :currency="invoice.currency"
                                :network-label="invoice.network_label"
                                :network="invoice.network"
                            />

                            <PaymentAddressSection
                                v-if="invoice.address"
                                :address="invoice.address"
                            />

                            <PaymentCountdownSection
                                :expires-at="invoice.expires_at"
                            />

                            <PaymentTransactionSection
                                :txid="invoice.txid"
                                :tx-explorer-url="invoice.tx_explorer_url"
                            />

                            <div class="divider my-0"></div>
                            <div class="text-sm opacity-70">
                                {{ __('frontend.payment_form.rules.exact_amount') }}<br />
                                {{ __('frontend.payment_form.rules.network_match') }}<br />
                                {{ __('frontend.payment_form.rules.auto_refresh') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </PaymentFormLayout>

</template>


