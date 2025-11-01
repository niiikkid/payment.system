<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';
import CurrencyNetworkBadge from '@/components/ui/CurrencyNetworkBadge.vue';
import AddressCopy from '@/components/ui/AddressCopy.vue';
import UidCopy from '@/components/ui/UidCopy.vue';

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

function openDetails(inv: Invoice) {
  selected.value = inv;
  showModal.value = true;
}

function closeDetails() {
  showModal.value = false;
}

const createPayload = ref({
  currency: '',
  network: '',
  amount: '',
  external_invoice_id: '',
  callback_url: '',
  tag: '',
  metadata: '' as any,
});
const createLoading = ref(false);
const createError = ref<string | null>(null);

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
      createPayload.value = { currency: '', network: '', amount: '', external_invoice_id: '', callback_url: '', tag: '', metadata: '' } as any;
    } else {
      createError.value = res.data?.message || 'Ошибка при создании инвойса';
    }
  } catch (e: any) {
    createError.value = e?.response?.data?.message || e?.message || 'Ошибка при создании инвойса';
  } finally {
    createLoading.value = false;
  }
}

</script>

<template>
  <AppSidebarLayout :breadcrumbs="[{ title: 'Главная', href: '/' }, { title: 'Invoices', href: '/invoices' }]">
    <div class="flex items-center justify-between gap-4">
      <h1 class="text-xl font-semibold">Инвойсы</h1>
      <div class="flex items-center gap-2">
        <button class="btn btn-primary btn-sm" @click="showCreate = true">Создать инвойс</button>
        <Link href="/dashboard" class="btn btn-ghost btn-sm">На дашборд</Link>
      </div>
    </div>

    <div class="mt-6 grid gap-6">
      <!-- List -->
      <div class="card bg-base-100 shadow">
        <div class="card-body">
          <h2 class="card-title">Список инвойсов</h2>
          <div class="overflow-x-auto">
            <table class="table w-full">
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
                    <span class="badge" :class="{
                      'badge-warning': statuses.active.includes(inv.status),
                      'badge-success': inv.status === 'paid',
                      'badge-ghost': statuses.final.includes(inv.status) && inv.status !== 'paid',
                    }">{{ inv.status }}</span>
                  </td>
                  <td>{{ inv.created_at }}</td>
                  <td>
                    <button class="btn btn-sm" @click="openDetails(inv)">Подробнее</button>
                  </td>
                </tr>
                <tr v-if="!invoices.data.length">
                  <td colspan="8" class="text-center text-sm opacity-70 py-6">Пока нет инвойсов</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-4 flex gap-2 flex-wrap">
            <Link v-for="l in invoices.links" :key="l.url || l.label" :href="l.url || '#'" class="btn btn-sm" :class="{ 'btn-disabled': !l.url, 'btn-active': l.active }" v-html="l.label" />
          </div>
        </div>
      </div>
    </div>

    <!-- Details Modal -->
    <dialog class="modal modal-bottom md:modal-middle" :open="showModal">
      <div class="modal-box max-w-3xl">
        <h3 class="font-bold text-lg">Детали инвойса</h3>
        <p class="text-sm opacity-70 mt-1">Полная информация по выбранному инвойсу</p>

        <div v-if="selected" class="mt-5 grid gap-4">
          <div class="grid gap-3 grid-cols-1">
            <div class="card bg-base-200 shadow-sm">
              <div class="card-body p-4">
                <div class="text-xs opacity-60">ID</div>
                <div class="font-mono break-all">
                  <UidCopy v-if="selected?.id" :uid="selected.id" />
                </div>
              </div>
            </div>
            <div class="card bg-base-200 shadow-sm">
              <div class="card-body p-4">
                <div class="text-xs opacity-60">Адрес</div>
                <div class="font-mono">
                  <AddressCopy v-if="selected?.address" :address="selected.address" />
                  <span v-else class="opacity-60">#{{ selected.address_id }}</span>
                </div>
              </div>
            </div>
            <div class="card bg-base-200 shadow-sm">
              <div class="card-body p-4">
                <div class="text-xs opacity-60">Сумма</div>
                <div class="font-mono">{{ selected.amount }}</div>
              </div>
            </div>
            <div class="card bg-base-200 shadow-sm">
              <div class="card-body p-4">
                <div class="text-xs opacity-60">Валюта / Сеть</div>
                <CurrencyNetworkBadge :currency-label="selected.currency_label || selected.currency" :network-label="selected.network_label || selected.network" />
              </div>
            </div>
            <div class="card bg-base-200 shadow-sm md:col-span-2">
              <div class="card-body p-4">
                <div class="text-xs opacity-60">Статус</div>
                <div>
                  <span class="badge" :class="{
                    'badge-warning': statuses.active.includes(selected.status),
                    'badge-success': selected.status === 'paid',
                    'badge-ghost': statuses.final.includes(selected.status) && selected.status !== 'paid',
                  }">{{ selected.status }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="divider my-0"></div>

          <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
              <div class="text-xs opacity-60">TXID</div>
              <div class="flex items-center gap-2" v-if="selected.txid">
                <span class="font-mono break-all">{{ selected.txid }}</span>
                <a v-if="selected.status === 'paid' && selected.tx_explorer_url" :href="selected.tx_explorer_url" target="_blank" rel="noopener noreferrer" class="link link-primary">
                  Открыть в обозревателе
                </a>
              </div>
              <div class="opacity-60" v-else>—</div>
            </div>
            <div class="grid gap-2">
              <div class="text-xs opacity-60">Получено / Подтв.</div>
              <div class="font-mono">{{ selected.amount_received }} / {{ selected.confirmations }}</div>
            </div>
            <div class="grid gap-2">
              <div class="text-xs opacity-60">Истекает</div>
              <div>{{ selected.expires_at || '—' }}</div>
            </div>
            <div class="grid gap-2">
              <div class="text-xs opacity-60">Создан</div>
              <div>{{ selected.created_at }}</div>
            </div>
          </div>

          <div class="divider my-0"></div>

          <div class="grid gap-3">
            <div class="text-xs opacity-60">Метаданные</div>
            <div class="mockup-code">
              <pre><code>{{ JSON.stringify(selected.metadata || {}, null, 2) }}</code></pre>
            </div>
          </div>
        </div>

        <div class="modal-action">
          <button class="btn btn-ghost" @click="closeDetails">Закрыть</button>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop" @submit.prevent="closeDetails">
        <button>close</button>
      </form>
    </dialog>

    <!-- Create Modal -->
    <dialog class="modal modal-bottom md:modal-middle" :open="showCreate">
      <div class="modal-box max-w-xl">
        <h3 class="font-bold text-lg">Создать инвойс</h3>
        <p class="text-sm opacity-70 mt-1">Заполните данные для выставления инвойса</p>
        <div v-if="createError" class="alert alert-error mt-3">
          <span>{{ createError }}</span>
        </div>
        <form class="mt-4 grid gap-4" @submit.prevent="submitCreate">
          <div class="form-control w-full">
            <label class="label"><span class="label-text">Валюта</span></label>
            <select v-model="createPayload.currency" class="select select-bordered w-full" required>
              <option value="" disabled>Выберите валюту</option>
              <option v-for="opt in currencyOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </select>
            <label class="label"><span class="label-text-alt opacity-60">Напр.: BTC, ETH, USDT</span></label>
          </div>
          <div class="form-control w-full">
            <label class="label"><span class="label-text">Сеть</span></label>
            <select v-model="createPayload.network" class="select select-bordered w-full" required>
              <option value="" disabled>Выберите сеть</option>
              <option v-for="opt in networkOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </select>
            <label class="label"><span class="label-text-alt opacity-60">Выберите соответствующую сети для валюты</span></label>
          </div>
          <div class="form-control w-full">
            <label class="label"><span class="label-text">Сумма</span></label>
            <input v-model.trim="createPayload.amount" type="text" class="input input-bordered w-full" placeholder="Например: 12.34" required />
            <label class="label"><span class="label-text-alt opacity-60">Десятичный формат</span></label>
          </div>
          <div class="form-control w-full">
            <label class="label"><span class="label-text">Внешний ID (опц.)</span></label>
            <input v-model.trim="createPayload.external_invoice_id" type="text" class="input input-bordered w-full" />
          </div>
          <div class="form-control w-full">
            <label class="label"><span class="label-text">Callback URL (опц.)</span></label>
            <input v-model.trim="createPayload.callback_url" type="url" class="input input-bordered w-full" />
          </div>
          <div class="form-control w-full">
            <label class="label"><span class="label-text">Тег (опц.)</span></label>
            <input v-model.trim="createPayload.tag" type="text" class="input input-bordered w-full" />
          </div>
          <div class="form-control w-full">
            <label class="label"><span class="label-text">Metadata (JSON, опц.)</span></label>
            <textarea v-model="createPayload.metadata" class="textarea textarea-bordered w-full" rows="4" placeholder='{"key":"value"}'></textarea>
          </div>
          <div class="modal-action">
            <button type="button" class="btn" @click="showCreate = false" :disabled="createLoading">Отмена</button>
            <button type="submit" class="btn btn-primary" :class="{ loading: createLoading }" :disabled="createLoading">Создать</button>
          </div>
        </form>
      </div>
      <form method="dialog" class="modal-backdrop" @submit.prevent="showCreate = false">
        <button>close</button>
      </form>
    </dialog>
  </AppSidebarLayout>
</template>


