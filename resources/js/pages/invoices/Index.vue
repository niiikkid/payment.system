<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';
import CurrencyNetworkBadge from '@/components/ui/CurrencyNetworkBadge.vue';
import AddressCopy from '@/components/ui/AddressCopy.vue';
import UidCopy from '@/components/ui/UidCopy.vue';
import DateTimeFormat from '@/components/ui/DateTimeFormat.vue';
import Pagination from '@/components/ui/Pagination.vue';
import InvoiceDetailsModal from '@/components/modals/invoices/InvoiceDetailsModal.vue';
import InvoiceEditModal, { type InvoiceEditForm } from '@/components/modals/invoices/InvoiceEditModal.vue';
import InvoiceCreateModal, { type InvoiceCreateForm } from '@/components/modals/invoices/InvoiceCreateModal.vue';

type Invoice = {
  id: string
  external_invoice_id: string | null
  address_id: number
  amount: number
  currency: string
  network: string
  status: string
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

interface Option { value: string; label: string }

const page = usePage();
const invoices = computed(() => page.props.invoices as any);
const statuses = computed(() => page.props.statuses as { active: string[]; final: string[] });
const currencyOptions = computed(() => page.props.currencyOptions as Option[]);
const networkOptions = computed(() => page.props.networkOptions as Option[]);

const selected: any = ref<Invoice | null>(null);
const showModal = ref(false);
const showCreate = ref(false);
const showEdit = ref(false);
const sendLoading = ref(false);
const sendError = ref<string | null>(null);
const sendSuccess = ref(false);

const editPayload = ref<InvoiceEditForm>({ status: '', txid: null });
const editLoading = ref(false);
const editError = ref<string | null>(null);

function updateEditPayload(payload: InvoiceEditForm) {
  editPayload.value = payload;
}

const allStatusOptions = computed(() => {
  const a = statuses.value.active || [];
  const f = statuses.value.final || [];
  const merged = Array.from(new Set([...a, ...f]));
  return merged.map(s => ({ value: s, label: s }));
});

function openDetails(inv: Invoice) {
  selected.value = inv;
  showModal.value = true;
}

function closeDetails() {
  showModal.value = false;
}

function openEdit() {
  if (!selected.value) return;
  editError.value = null;
  editPayload.value = {
    status: selected.value.status,
    txid: selected.value.txid || null,
  };
  showEdit.value = true;
}

function closeEdit() {
  showEdit.value = false;
}

async function submitEdit() {
  if (!selected.value) return;
  editError.value = null;
  editLoading.value = true;
  try {
    const res = await axios.post(`/invoices/${selected.value.id}`, {
      _method: 'PATCH',
      status: editPayload.value.status,
      txid: editPayload.value.status === 'paid' ? (editPayload.value.txid || '') : null,
    });
    const updated = res.data;
    // Обновляем выбранный и список (перезагрузка только пропса invoices)
    selected.value = updated;
    showEdit.value = false;
    router.reload({ only: ['invoices'] });
  } catch (e: any) {
    showEdit.value = true;
    editError.value = e?.response?.data?.message || e?.response?.data?.errors?.txid?.[0] || e?.message || 'Ошибка при обновлении инвойса';
  } finally {
    editLoading.value = false;
  }
}

async function sendCallback() {
  if (!selected.value) return;
  if (!selected.value.callback_url) return;
  sendError.value = null;
  sendSuccess.value = false;
  sendLoading.value = true;
  try {
    await axios.post(`/invoices/${selected.value.id}/send-callback`);
    sendSuccess.value = true;
  } catch (e: any) {
    sendError.value = e?.response?.data?.message || e?.message || 'Ошибка при отправке колбэка';
  } finally {
    sendLoading.value = false;
  }
}

const createPayload = ref<InvoiceCreateForm>({
  currency: '',
  network: '',
  amount: '',
  external_invoice_id: '',
  callback_url: '',
  tag: '',
  metadata: '',
});
const createLoading = ref(false);
const createError = ref<string | null>(null);

function closeCreate() {
  showCreate.value = false;
}

function updateCreatePayload(payload: InvoiceCreateForm) {
  createPayload.value = payload;
}

function resetCreatePayload() {
  createPayload.value = { currency: '', network: '', amount: '', external_invoice_id: '', callback_url: '', tag: '', metadata: '' };
}

async function submitCreate() {
  createError.value = null;
  createLoading.value = true;
  let metadataParsed: any = undefined;
  if (createPayload.value.metadata) {
    try { metadataParsed = JSON.parse(createPayload.value.metadata as string); } catch (e) { metadataParsed = undefined; }
  }
  try {
    const res = await axios.post('/invoices', {
      currency: createPayload.value.currency,
      network: createPayload.value.network,
      amount: createPayload.value.amount,
      external_invoice_id: createPayload.value.external_invoice_id || null,
      callback_url: createPayload.value.callback_url || null,
      tag: createPayload.value.tag || null,
      metadata: metadataParsed || {},
    });
    if (res.data?.success) {
      showCreate.value = false;
      // Обновим только список
      router.reload({ only: ['invoices'] });
      resetCreatePayload();
    } else {
      showCreate.value = true;
      createError.value = res.data?.message || 'Ошибка при создании инвойса';
    }
  } catch (e: any) {
    showCreate.value = true;
    createError.value = e?.response?.data?.message || e?.message || 'Ошибка при создании инвойса';
  } finally {
    createLoading.value = false;
  }
}

function toIso(input: string | null | undefined): string {
  if (!input) return '';
  if (typeof input !== 'string') return '';
  if (input.includes('T')) return input; // уже ISO
  // Преобразуем 'YYYY-MM-DD HH:mm:ss' -> 'YYYY-MM-DDTHH:mm:ssZ'
  return `${input.replace(' ', 'T')}Z`;
}

</script>

<template>
  <AppLayout :breadcrumbs="[{ title: 'Главная', href: '/' }, { title: 'Invoices', href: '/invoices' }]">
    <template #header-actions>
      <div class="flex items-center gap-2">
        <button class="btn btn-primary btn-sm" @click="showCreate = true">Создать инвойс</button>
      </div>
    </template>

    <div class="grid gap-6">
      <!-- List -->
      <div class="lg:card lg:bg-base-100 lg:shadow">
        <div class="lg:card-body">
          <h2 class="hidden lg:block card-title">Список инвойсов</h2>
          <h2 class="lg:hidden card-title mb-3">Список инвойсов</h2>

          <!-- Desktop Table View (lg and above) -->
          <div class="hidden lg:block overflow-x-auto">
            <table class="table table-sm w-full">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Адрес</th>
                  <th>Сумма</th>
                  <th>Валюта</th>
                  <th>Статус</th>
                  <th>Создан</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="inv in invoices.data" :key="inv.id">
                  <td class="font-mono text-xs">
                    <UidCopy :uid="inv.id" />
                  </td>
                  <td class="font-mono text-xs">
                    <AddressCopy v-if="inv.address" :address="inv.address" />
                    <span v-else class="opacity-60">#{{ inv.address_id }}</span>
                  </td>
                  <td>{{ inv.amount }}</td>
                  <td>
                    <CurrencyNetworkBadge :currency-label="inv.currency_label || inv.currency" :network-label="inv.network_label || inv.network" />
                  </td>
                  <td>
                    <span class="badge badge-sm" :class="{
                      'badge-warning': statuses.active.includes(inv.status),
                      'badge-success': inv.status === 'paid',
                      'badge-error': statuses.final.includes(inv.status) && inv.status !== 'paid',
                    }">{{ inv.status }}</span>
                  </td>
                  <td class="text-xs font-mono">
                    <DateTimeFormat :value="toIso(inv.created_at)" />
                  </td>
                  <td class="flex items-center gap-2">
                    <button class="btn btn-xs" @click="openDetails(inv)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </button>
                    <Link :href="`/pay/${inv.id}`" target="_blank" rel="noopener" class="btn btn-ghost btn-xs" title="Открыть платёжную страницу">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                      </svg>
                    </Link>
                  </td>
                </tr>
                <tr v-if="!invoices.data.length">
                  <td colspan="8" class="text-center text-sm opacity-70 py-6">Пока нет инвойсов</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile Card View (sm to lg) -->
          <div class="hidden sm:block lg:hidden space-y-3">
            <div v-for="inv in invoices.data" :key="inv.id" class="card bg-base-100 shadow-sm">
              <div class="card-body p-4">
                <!-- Header: UUID and Date -->
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <span class="text-xs opacity-70">UUID:</span>
                    <UidCopy :uid="inv.id" />
                  </div>
                  <div class="flex items-center gap-1.5 text-xs opacity-70">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    <DateTimeFormat :value="toIso(inv.created_at)" />
                  </div>
                </div>

                <!-- Main Content Row -->
                <div class="grid grid-cols-[auto_1fr_auto] items-center gap-3">
                  <!-- Amounts -->
                  <div class="flex items-center gap-2">
                    <div class="text-sm font-semibold">{{ inv.amount }}</div>
                    <div>
                      <CurrencyNetworkBadge :currency-label="inv.currency_label || inv.currency" :network-label="inv.network_label || inv.network" />
                    </div>
                  </div>

                  <!-- Middle Info - Centered -->
                  <div class="flex justify-center min-w-0">
                    <div class="font-mono text-xs opacity-70 truncate text-center">
                      <AddressCopy v-if="inv.address" :address="inv.address" />
                      <span v-else class="opacity-60">#{{ inv.address_id }}</span>
                    </div>
                  </div>

                  <!-- Right Side: Status and Actions -->
                  <div class="flex items-center gap-2">
                    <div>
                      <span class="badge badge-sm" :class="{
                        'badge-warning': statuses.active.includes(inv.status),
                        'badge-success': inv.status === 'paid',
                        'badge-error': statuses.final.includes(inv.status) && inv.status !== 'paid',
                      }">
                        {{ inv.status }}
                      </span>
                    </div>
                    <button class="btn btn-xs" @click="openDetails(inv)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                      </svg>
                    </button>
                    <Link :href="`/pay/${inv.id}`" target="_blank" rel="noopener" class="btn btn-ghost btn-xs" title="Открыть платёжную страницу">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                      </svg>
                    </Link>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="!invoices.data.length" class="text-center text-sm opacity-70 py-6">
              Пока нет инвойсов
            </div>
          </div>

          <!-- Small Mobile Card View (below sm) -->
            <div class="sm:hidden space-y-3">
                <div v-for="inv in invoices.data" :key="inv.id" class="card bg-base-100 shadow-sm">
                    <div class="card-body p-4">
                        <!-- Header: UUID and Date -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xs opacity-70">UUID:</span>
                                <UidCopy :uid="inv.id" />
                            </div>
                            <div class="flex items-center gap-1.5 text-xs opacity-70">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                <DateTimeFormat :value="toIso(inv.created_at)" short-year hide-seconds />
                            </div>
                        </div>

                        <!-- Main Content Row -->
                        <div class="flex justify-between items-center gap-3">
                            <!-- Amounts -->
                            <div class="flex items-center gap-2">
                                <div class="text-sm font-semibold">{{ inv.amount }}</div>
                                <div>
                                    <CurrencyNetworkBadge :currency-label="inv.currency_label || inv.currency" :network-label="inv.network_label || inv.network" />
                                </div>
                            </div>

                            <!-- Right Side: Status and Actions -->
                            <div>
                                  <span class="badge badge-sm" :class="{
                                    'badge-warning': statuses.active.includes(inv.status),
                                    'badge-success': inv.status === 'paid',
                                    'badge-error': statuses.final.includes(inv.status) && inv.status !== 'paid',
                                  }">
                                    {{ inv.status }}
                                  </span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-between mt-3">
                            <div class="flex items-center">
                                <div class="font-mono text-xs opacity-70 truncate text-center">
                                    <AddressCopy v-if="inv.address" :address="inv.address" />
                                    <span v-else class="opacity-60">#{{ inv.address_id }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button class="btn btn-xs" @click="openDetails(inv)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                    </svg>
                                </button>
                                <Link :href="`/pay/${inv.id}`" target="_blank" rel="noopener" class="btn btn-ghost btn-xs" title="Открыть платёжную страницу">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="!invoices.data.length" class="text-center text-sm opacity-70 py-6">
                    Пока нет инвойсов
                </div>
            </div>

          <Pagination :links="invoices.links" />
        </div>
      </div>
    </div>

    <InvoiceDetailsModal
      v-model="showModal"
      :invoice="selected"
      :statuses="statuses"
      :send-loading="sendLoading"
      :send-error="sendError"
      :send-success="sendSuccess"
      @edit="openEdit"
      @send-callback="sendCallback"
      @close="closeDetails"
    />

    <InvoiceEditModal
      v-model="showEdit"
      :form="editPayload"
      :status-options="allStatusOptions"
      :error="editError"
      :loading="editLoading"
      @update:form="updateEditPayload"
      @submit="submitEdit"
      @close="closeEdit"
    />

    <InvoiceCreateModal
      v-model="showCreate"
      :form="createPayload"
      :currency-options="currencyOptions"
      :network-options="networkOptions"
      :error="createError"
      :loading="createLoading"
      @update:form="updateCreatePayload"
      @submit="submitCreate"
      @close="closeCreate"
    />

  </AppLayout>
</template>


