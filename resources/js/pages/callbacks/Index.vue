<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import DateTimeFormat from '@/components/ui/DateTimeFormat.vue';
import CallbackDetailsModal from '@/components/modals/callbacks/CallbackDetailsModal.vue';

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
    <div class="grid gap-6">
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

    <CallbackDetailsModal
      v-model="showModal"
      :log="selected"
      @close="closeDetails"
    />

  </AppSidebarLayout>

</template>


