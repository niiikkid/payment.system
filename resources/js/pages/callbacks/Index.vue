<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import DateTimeFormat from '@/components/ui/DateTimeFormat.vue';

type CallbackLog = {
  id: string
  invoice_id: string
  event: string
  url: string
  request_payload: any
  response_status: number | null
  response_body: string | null
  error_message: string | null
  duration_ms: number | null
  created_at: string | null
}

const page = usePage();
const logs = computed(() => page.props.logs as any);

const selected = ref<CallbackLog | null>(null);
const showModal = ref(false);

function openDetails(log: CallbackLog) {
  selected.value = log;
  showModal.value = true;
}

function closeDetails() {
  showModal.value = false;
}

function toIso(input: string | null | undefined): string {
  if (!input) return '';
  if (typeof input !== 'string') return '';
  if (input.includes('T')) return input;
  return `${input.replace(' ', 'T')}Z`;
}
</script>

<template>
  <AppSidebarLayout :breadcrumbs="[{ title: 'Главная', href: '/' }, { title: 'Callback логи', href: '/callback-logs' }]">
    <div class="flex items-center justify-between gap-4">
      <h1 class="text-xl font-semibold flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 opacity-60">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
        </svg>
        Callback логи
      </h1>
    </div>

    <div class="mt-6 grid gap-6">
      <div class="card bg-base-100 shadow">
        <div class="card-body">
          <h2 class="card-title">Список логов</h2>
          <div class="overflow-x-auto">
            <table class="table w-full">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Invoice</th>
                  <th>Событие</th>
                  <th>HTTP</th>
                  <th>Время</th>
                  <th>Длит., мс</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="log in logs.data" :key="log.id">
                  <td class="font-mono text-xs">{{ log.id }}</td>
                  <td class="font-mono text-xs">{{ log.invoice_id }}</td>
                  <td>
                    <span class="badge badge-outline">{{ log.event }}</span>
                  </td>
                  <td>
                    <span class="badge" :class="{ 'badge-success': (log.response_status||0) >= 200 && (log.response_status||0) < 300, 'badge-error': (log.response_status||0) >= 400 }">
                      {{ log.response_status ?? '—' }}
                    </span>
                  </td>
                  <td>
                    <DateTimeFormat :value="toIso(log.created_at)" />
                  </td>
                  <td>{{ log.duration_ms ?? '—' }}</td>
                  <td>
                    <button class="btn btn-sm" @click="openDetails(log)">Подробнее</button>
                  </td>
                </tr>
                <tr v-if="!logs.data.length">
                  <td colspan="7" class="text-center text-sm opacity-70 py-6">Пока нет логов</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-4 flex gap-2 flex-wrap">
            <Link v-for="l in logs.links" :key="l.url || l.label" :href="l.url || '#'" class="btn btn-sm" :class="{ 'btn-disabled': !l.url, 'btn-active': l.active }" v-html="l.label" />
          </div>
        </div>
      </div>
    </div>

    <!-- Details Modal -->
    <dialog class="modal modal-bottom md:modal-middle" :open="showModal">
      <div class="modal-box max-w-3xl">
        <h3 class="font-bold text-lg">Детали коллбэка</h3>
        <p class="text-sm opacity-70 mt-1">Полная информация по выбранной записи</p>

        <div v-if="selected" class="mt-5 grid gap-4">
          <div class="grid gap-3">
            <div class="text-xs opacity-60">ID</div>
            <div class="font-mono break-all">{{ selected.id }}</div>
          </div>

          <div class="grid gap-2 md:grid-cols-2">
            <div class="grid gap-1">
              <div class="text-xs opacity-60">Invoice</div>
              <div class="font-mono break-all">{{ selected.invoice_id }}</div>
            </div>
            <div class="grid gap-1">
              <div class="text-xs opacity-60">Событие</div>
              <div><span class="badge badge-outline">{{ selected.event }}</span></div>
            </div>
            <div class="grid gap-1 md:col-span-2">
              <div class="text-xs opacity-60">URL</div>
              <div class="flex items-center gap-2">
                <span class="badge badge-outline">POST</span>
                <span class="break-all font-mono">{{ selected.url }}</span>
              </div>
            </div>
            <div class="grid gap-1">
              <div class="text-xs opacity-60">HTTP статус</div>
              <div>
                <span class="badge" :class="{ 'badge-success': (selected.response_status||0) >= 200 && (selected.response_status||0) < 300, 'badge-error': (selected.response_status||0) >= 400 }">
                  {{ selected.response_status ?? '—' }}
                </span>
              </div>
            </div>
            <div class="grid gap-1">
              <div class="text-xs opacity-60">Длительность, мс</div>
              <div>{{ selected.duration_ms ?? '—' }}</div>
            </div>
          </div>

          <div class="divider my-0"></div>

          <div class="grid gap-3">
            <div class="text-xs opacity-60">Запрос (payload)</div>
            <div class="mockup-code">
              <pre><code>{{ JSON.stringify(selected.request_payload || {}, null, 2) }}</code></pre>
            </div>
          </div>

          <div class="grid gap-3">
            <div class="text-xs opacity-60">Ответ (body)</div>
            <div class="mockup-code">
              <pre><code>{{ selected.response_body || '' }}</code></pre>
            </div>
          </div>

          <div class="grid gap-3" v-if="selected.error_message">
            <div class="text-xs opacity-60">Ошибка</div>
            <div class="alert alert-warning">
              <span class="break-all">{{ selected.error_message }}</span>
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
  </AppSidebarLayout>
</template>


