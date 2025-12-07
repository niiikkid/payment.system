<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { vueLang } from '@erag/lang-sync-inertia';
import FilterPanel from '@/components/filters/FilterPanel.vue';
import DateTimeFormat from '@/components/ui/DateTimeFormat.vue';
import Pagination from '@/components/ui/Pagination.vue';
import FormControl from '@/components/form/FormControl.vue';
import Label from '@/components/form/Label.vue';
import Select from '@/components/form/Select.vue';
import Input from '@/components/form/Input.vue';

type Option = { value: string; label: string };

type Notification = {
  id: number;
  event: string;
  channel: string;
  status: string;
  title: string;
  body: string;
  payload: any;
  read_at: string | null;
  delivered_at: string | null;
  created_at: string | null;
};

type NotificationRule = {
  id: number;
  event: string;
  currency: string;
  statuses: string[];
  channels: string[];
  min_amount: string;
  enabled: boolean;
  created_at: string | null;
};

type TelegramState = {
  is_active: boolean;
  chat_id: string | null;
  username: string | null;
  first_name: string | null;
  last_name: string | null;
  linked_at: string | null;
  link_token: string;
  start_link: string | null;
  bot_username: string | null;
};

type FilterModel = {
  event: string;
  channel: string;
  delivery_status: string;
  only_unread: boolean;
};

const page = usePage();
const { __ } = vueLang();

const notifications = computed(() => page.props.notifications as any);
const rules = computed<NotificationRule[]>(() => (page.props.rules as NotificationRule[]) ?? []);
const events = computed<Option[]>(() => (page.props.events as Option[]) ?? []);
const channels = computed<Option[]>(() => (page.props.channels as Option[]) ?? []);
const invoiceStatuses = computed<Option[]>(() => (page.props.invoice_statuses as Option[]) ?? []);
const deliveryStatuses = computed<Option[]>(() => (page.props.delivery_statuses as Option[]) ?? []);
const currencies = computed<Option[]>(() => (page.props.currencies as Option[]) ?? []);
const telegram = computed<TelegramState | null>(() => (page.props.telegram as TelegramState | null) ?? null);
const filtersFromPage = computed<Partial<FilterModel>>(() => (page.props.filters as Partial<FilterModel> | undefined) ?? {});

function findLabel(list: Option[], value: string | undefined | null): string {
  if (!value) return '';
  return list.find(option => option.value === value)?.label ?? value;
}

const eventLabel = (value: string) => findLabel(events.value, value);
const channelLabel = (value: string) => findLabel(channels.value, value);
const statusLabel = (value: string) => findLabel(deliveryStatuses.value, value);

const filterDefaults: FilterModel = {
  event: '',
  channel: '',
  delivery_status: '',
  only_unread: false,
};

const filterForm = useForm<FilterModel>({
  ...filterDefaults,
  ...filtersFromPage.value,
});

const filtersModel = computed({
  get: () => ({
    ...filterDefaults,
    ...filterForm.data(),
  }),
  set: (value: FilterModel) => {
    filterForm.event = value.event ?? '';
    filterForm.channel = value.channel ?? '';
    filterForm.delivery_status = value.delivery_status ?? '';
    filterForm.only_unread = Boolean(value.only_unread);
  },
});

const ruleForm = useForm({
  event: events.value[0]?.value ?? 'invoice.status_changed',
  currency: currencies.value[0]?.value ?? 'USDT',
  statuses: [] as string[],
  channels: ['in_app'] as string[],
  min_amount: '0',
  enabled: true,
});
const telegramLinkForm = useForm({});
const telegramCopying = ref(false);
const telegramCopied = ref(false);
const telegramCopyError = ref<string | null>(null);

const selectedRuleId = ref<number | null>(null);
const markAllForm = useForm({});
const activeTab = ref<'notifications' | 'settings'>('notifications');

const filterFields = computed(() => [
  {
    key: 'event',
    type: 'select',
    label: __('frontend.notifications.filters.event'),
    options: [{ value: '', label: __('frontend.notifications.filters.any') }, ...events.value],
  },
  {
    key: 'channel',
    type: 'select',
    label: __('frontend.notifications.filters.channel'),
    options: [{ value: '', label: __('frontend.notifications.filters.any') }, ...channels.value],
  },
  {
    key: 'delivery_status',
    type: 'select',
    label: __('frontend.notifications.filters.delivery_status'),
    options: [{ value: '', label: __('frontend.notifications.filters.any') }, ...deliveryStatuses.value],
  },
  {
    key: 'only_unread',
    type: 'checkpoint',
    label: __('frontend.notifications.filters.only_unread'),
  },
]);

function buildFilterPayload(extra: Record<string, unknown> = {}) {
  const payload = {
    ...filterForm.data(),
    ...extra,
  };

  const cleaned: Record<string, unknown> = {};

  Object.entries(payload).forEach(([key, value]) => {
    if (key === 'page') {
      cleaned[key] = value;
      return;
    }

    if (typeof value === 'boolean') {
      if (value) {
        cleaned[key] = 1;
      }
      return;
    }

    if (value !== undefined && value !== null && String(value).length > 0) {
      cleaned[key] = value;
    }
  });

  return cleaned;
}

function applyFilters() {
  filterForm
    .transform(() => buildFilterPayload({ page: 1 }))
    .get('/notifications', {
      preserveScroll: true,
      preserveState: true,
      replace: true,
      onFinish: () => filterForm.transform(data => data),
    });
}

function resetFilters() {
  filterForm.event = filterDefaults.event;
  filterForm.channel = filterDefaults.channel;
  filterForm.delivery_status = filterDefaults.delivery_status;
  filterForm.only_unread = filterDefaults.only_unread;
  applyFilters();
}

function refreshTelegramLink() {
  telegramCopied.value = false;
  telegramCopyError.value = null;
  telegramLinkForm.post('/notifications/telegram/link', {
    preserveScroll: true,
  });
}

async function copyTelegramLink() {
  if (!telegram.value?.start_link || !navigator?.clipboard) {
    telegramCopyError.value = __('frontend.notifications.telegram.copy_failed');
    return;
  }

  telegramCopyError.value = null;
  telegramCopying.value = true;

  try {
    await navigator.clipboard.writeText(telegram.value.start_link);
    telegramCopied.value = true;
    setTimeout(() => {
      telegramCopied.value = false;
    }, 1500);
  } catch (error) {
    telegramCopyError.value = __('frontend.notifications.telegram.copy_failed');
  } finally {
    telegramCopying.value = false;
  }
}

function submitRule() {
  selectedRuleId.value = null;
  ruleForm.post('/notifications/rules', {
    preserveScroll: true,
    onSuccess: () => {
      ruleForm.reset('min_amount');
    },
  });
}

function editRule(rule: NotificationRule) {
  selectedRuleId.value = rule.id;
  ruleForm.event = rule.event;
  ruleForm.currency = rule.currency;
  ruleForm.statuses = [...(rule.statuses ?? [])];
  ruleForm.channels = [...(rule.channels ?? [])];
  ruleForm.min_amount = rule.min_amount;
  ruleForm.enabled = rule.enabled;
}

function submitUpdateRule() {
  if (!selectedRuleId.value) {
    return;
  }

  ruleForm.patch(`/notifications/rules/${selectedRuleId.value}`, {
    preserveScroll: true,
    onSuccess: () => {
      selectedRuleId.value = null;
    },
  });
}

function deleteRule(rule: NotificationRule) {
  useForm({}).delete(`/notifications/rules/${rule.id}`, {
    preserveScroll: true,
  });
}

function toggleRule(rule: NotificationRule) {
  const form = useForm({
    event: rule.event,
    currency: rule.currency,
    statuses: rule.statuses,
    channels: rule.channels,
    min_amount: rule.min_amount,
    enabled: !rule.enabled,
  });

  form.patch(`/notifications/rules/${rule.id}`, { preserveScroll: true });
}

function markRead(notification: Notification) {
  useForm({}).patch(`/notifications/${notification.id}/read`, {
    preserveScroll: true,
  });
}

function markUnread(notification: Notification) {
  useForm({}).patch(`/notifications/${notification.id}/unread`, {
    preserveScroll: true,
  });
}

function markAllRead() {
  markAllForm.post('/notifications/mark-all-read', {
    preserveScroll: true,
  });
}

const eventRequiresStatus = computed(() => ruleForm.event === 'invoice.status_changed');
</script>

<template>
  <AppLayout :breadcrumbs="[{ title: __('frontend.notifications.breadcrumb.home'), href: '/' }, { title: __('frontend.notifications.breadcrumb.title'), href: '/notifications' }]">
    <div class="grid gap-6">
      <div class="tabs tabs-lifted">
        <button
          role="tab"
          class="tab"
          :class="{ 'tab-active': activeTab === 'notifications' }"
          @click="activeTab = 'notifications'"
        >
          {{ __('frontend.notifications.list.title') }}
        </button>
        <button
          role="tab"
          class="tab"
          :class="{ 'tab-active': activeTab === 'settings' }"
          @click="activeTab = 'settings'"
        >
          {{ __('frontend.notifications.rules.title') }}
        </button>
      </div>

      <div v-show="activeTab === 'notifications'" class="space-y-4">
        <FilterPanel
          v-model="filtersModel"
          :fields="filterFields"
          :title="__('frontend.notifications.filters.title')"
          :apply-label="__('frontend.notifications.filters.apply')"
          :reset-label="__('frontend.notifications.filters.reset')"
          :show-label="__('frontend.notifications.filters.show')"
          :hide-label="__('frontend.notifications.filters.hide')"
          :any-option-label="__('frontend.notifications.filters.any')"
          :loading="filterForm.processing"
          @apply="applyFilters"
          @reset="resetFilters"
        />

        <div class="card bg-base-100 shadow-sm">
          <div class="card-body">
            <div class="flex items-center justify-between mb-2">
              <h3 class="card-title text-lg">{{ __('frontend.notifications.list.title') }}</h3>
              <button class="btn btn-ghost btn-sm" :disabled="filterForm.processing" @click="markAllRead">
                <span class="loading loading-spinner loading-xs mr-2" v-if="filterForm.processing" />
                {{ __('frontend.notifications.actions.mark_all_read') }}
              </button>
            </div>
            <div class="hidden lg:block overflow-x-auto">
              <table class="table table-zebra">
                <thead>
                  <tr>
                    <th>{{ __('frontend.notifications.list.title_col') }}</th>
                    <th>{{ __('frontend.notifications.list.event') }}</th>
                    <th>{{ __('frontend.notifications.list.channel') }}</th>
                    <th>{{ __('frontend.notifications.list.status') }}</th>
                    <th>{{ __('frontend.notifications.list.created_at') }}</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="notification in notifications.data" :key="notification.id">
                    <td>
                      <div class="font-semibold flex items-center gap-2">
                        <span v-if="notification.read_at === null" class="badge badge-primary badge-xs"></span>
                        {{ notification.title }}
                      </div>
                      <div class="text-sm opacity-70 whitespace-pre-wrap">{{ notification.body }}</div>
                    </td>
                    <td>
                      <span class="badge badge-ghost">{{ eventLabel(notification.event) }}</span>
                    </td>
                    <td>
                      <span class="badge badge-outline">{{ channelLabel(notification.channel) }}</span>
                    </td>
                    <td>
                      <span
                        class="badge badge-sm"
                        :class="{
                          'badge-success': notification.status === 'delivered',
                          'badge-error': notification.status === 'failed',
                          'badge-ghost': notification.status === 'pending',
                        }"
                      >
                        {{ statusLabel(notification.status) }}
                      </span>
                    </td>
                    <td>
                      <DateTimeFormat :value="notification.created_at" />
                    </td>
                    <td class="text-right">
                      <div class="flex items-center justify-end gap-2">
                        <button v-if="notification.read_at === null" class="btn btn-primary btn-xs" @click="markRead(notification)">
                          {{ __('frontend.notifications.actions.mark_read') }}
                        </button>
                        <button v-else class="btn btn-ghost btn-xs" @click="markUnread(notification)">
                          {{ __('frontend.notifications.actions.mark_unread') }}
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="notifications.data.length === 0">
                    <td colspan="6" class="text-center text-sm opacity-70 py-6">
                      {{ __('frontend.notifications.list.empty') }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="lg:hidden space-y-3">
              <div v-for="notification in notifications.data" :key="notification.id" class="card bg-base-100 shadow-sm">
                <div class="card-body p-4 space-y-3">
                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <div class="font-semibold flex items-center gap-2">
                        <span v-if="notification.read_at === null" class="badge badge-primary badge-xs"></span>
                        {{ notification.title }}
                      </div>
                      <div class="text-sm opacity-70 whitespace-pre-wrap">{{ notification.body }}</div>
                    </div>
                    <div class="text-xs opacity-70">
                      <DateTimeFormat :value="notification.created_at" />
                    </div>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <span class="badge badge-ghost">{{ eventLabel(notification.event) }}</span>
                    <span class="badge badge-outline">{{ channelLabel(notification.channel) }}</span>
                    <span
                      class="badge badge-sm"
                      :class="{
                        'badge-success': notification.status === 'delivered',
                        'badge-error': notification.status === 'failed',
                        'badge-ghost': notification.status === 'pending',
                      }"
                    >
                      {{ statusLabel(notification.status) }}
                    </span>
                  </div>
                  <div class="flex justify-end gap-2">
                    <button v-if="notification.read_at === null" class="btn btn-primary btn-xs" @click="markRead(notification)">
                      {{ __('frontend.notifications.actions.mark_read') }}
                    </button>
                    <button v-else class="btn btn-ghost btn-xs" @click="markUnread(notification)">
                      {{ __('frontend.notifications.actions.mark_unread') }}
                    </button>
                  </div>
                </div>
              </div>
              <div v-if="notifications.data.length === 0" class="text-center text-sm opacity-70 py-6">
                {{ __('frontend.notifications.list.empty') }}
              </div>
            </div>
            <Pagination :links="notifications.links" />
          </div>
        </div>
      </div>

      <div v-show="activeTab === 'settings'" class="space-y-4">
        <div class="card bg-base-100 shadow-sm">
          <div class="card-body space-y-3">
            <div class="flex items-start justify-between gap-3">
              <div>
                <h3 class="card-title text-lg">{{ __('frontend.notifications.telegram.title') }}</h3>
                <p class="text-sm opacity-70">{{ __('frontend.notifications.telegram.description') }}</p>
              </div>
              <span class="badge" :class="telegram?.is_active ? 'badge-success' : 'badge-ghost'">
                {{ telegram?.is_active ? __('frontend.notifications.telegram.status_connected') : __('frontend.notifications.telegram.status_disconnected') }}
              </span>
            </div>

            <div v-if="telegram?.bot_username" class="space-y-3">
              <FormControl>
                <Label>{{ __('frontend.notifications.telegram.start_link') }}</Label>
                <div class="join w-full">
                  <Input class="join-item w-full" :model-value="telegram?.start_link ?? ''" :readonly="true" />
                  <button class="btn btn-outline join-item" :disabled="!telegram?.start_link || telegramCopying" @click="copyTelegramLink">
                    <span class="loading loading-spinner loading-xs mr-2" v-if="telegramCopying" />
                    {{ telegramCopied ? __('frontend.notifications.telegram.copied') : __('frontend.notifications.telegram.copy_link') }}
                  </button>
                </div>
                <p class="text-xs opacity-70 mt-1">{{ __('frontend.notifications.telegram.start_hint') }}</p>
                <p v-if="telegramCopyError" class="text-error text-sm mt-1">{{ telegramCopyError }}</p>
              </FormControl>
              <div class="flex flex-wrap gap-2">
                <a
                  v-if="telegram?.start_link"
                  class="btn btn-secondary btn-sm"
                  :href="telegram.start_link"
                  target="_blank"
                  rel="noreferrer"
                >
                  {{ __('frontend.notifications.telegram.open_bot') }}
                </a>
                <button class="btn btn-primary btn-sm" :disabled="telegramLinkForm.processing" @click="refreshTelegramLink">
                  <span v-if="telegramLinkForm.processing" class="loading loading-spinner loading-xs mr-2" />
                  {{ __('frontend.notifications.telegram.refresh_link') }}
                </button>
              </div>
            </div>
            <template v-else>
              <div class="alert alert-warning">
                {{ __('frontend.notifications.telegram.bot_not_configured') }}
              </div>
            </template>
          </div>
        </div>
        <div class="card bg-base-100 shadow-sm">
          <div class="card-body">
            <h2 class="card-title text-lg">{{ selectedRuleId ? __('frontend.notifications.form.update_title') : __('frontend.notifications.form.create_title') }}</h2>
            <div class="grid lg:grid-cols-2 gap-4">
              <FormControl :error="ruleForm.errors.event">
                <Label for="event" required>{{ __('frontend.notifications.form.event') }}</Label>
                <Select
                  id="event"
                  v-model="ruleForm.event"
                  :options="events"
                  :disabled="ruleForm.processing"
                  required
                />
              </FormControl>
              <FormControl :error="ruleForm.errors.currency">
                <Label for="currency" required>{{ __('frontend.notifications.form.currency') }}</Label>
                <Select
                  id="currency"
                  v-model="ruleForm.currency"
                  :options="currencies"
                  :disabled="ruleForm.processing"
                  required
                />
              </FormControl>
              <FormControl :error="ruleForm.errors.min_amount">
                <Label for="min_amount" required>{{ __('frontend.notifications.form.min_amount') }}</Label>
                <Input
                  id="min_amount"
                  v-model="ruleForm.min_amount"
                  type="number"
                  step="0.000001"
                  min="0"
                  :placeholder="__('frontend.notifications.form.min_amount_placeholder')"
                  :disabled="ruleForm.processing"
                  required
                />
              </FormControl>
              <FormControl :error="ruleForm.errors.channels">
                <Label>{{ __('frontend.notifications.form.channels') }}</Label>
                <div class="flex flex-wrap gap-2">
                  <label v-for="option in channels" :key="option.value" class="label cursor-pointer space-x-2 p-0">
                    <input v-model="ruleForm.channels" class="checkbox checkbox-sm" type="checkbox" :value="option.value" :disabled="ruleForm.processing" />
                    <span class="label-text">{{ option.label }}</span>
                  </label>
                </div>
              </FormControl>
              <FormControl v-if="eventRequiresStatus" :error="ruleForm.errors.statuses" class="lg:col-span-2">
                <Label>{{ __('frontend.notifications.form.statuses') }}</Label>
                <div class="flex flex-wrap gap-2">
                  <label v-for="option in invoiceStatuses" :key="option.value" class="label cursor-pointer space-x-2 p-0">
                    <input v-model="ruleForm.statuses" class="checkbox checkbox-sm" type="checkbox" :value="option.value" :disabled="ruleForm.processing" />
                    <span class="label-text uppercase">{{ option.label }}</span>
                  </label>
                </div>
              </FormControl>
              <FormControl>
                <Label class="cursor-pointer space-x-3 flex items-center">
                  <input v-model="ruleForm.enabled" type="checkbox" class="toggle toggle-primary mr-2" :disabled="ruleForm.processing" />
                  <span class="label-text">{{ __('frontend.notifications.form.enabled') }}</span>
                </Label>
              </FormControl>
            </div>
            <div class="card-actions justify-end">
              <button v-if="selectedRuleId" class="btn btn-secondary btn-sm" :disabled="ruleForm.processing" @click="submitUpdateRule">
                <span v-if="ruleForm.processing" class="loading loading-spinner loading-xs mr-2" />
                {{ __('frontend.notifications.form.update') }}
              </button>
              <button v-else class="btn btn-primary btn-sm" :disabled="ruleForm.processing" @click="submitRule">
                <span v-if="ruleForm.processing" class="loading loading-spinner loading-xs mr-2" />
                {{ __('frontend.notifications.form.save') }}
              </button>
            </div>
          </div>
        </div>
        <div class="card bg-base-100 shadow-sm">
          <div class="card-body">
            <div class="flex items-center justify-between">
              <h3 class="card-title text-lg">{{ __('frontend.notifications.rules.title') }}</h3>
              <span class="badge badge-outline">{{ rules.length }}</span>
            </div>
            <div class="hidden lg:block overflow-x-auto">
              <table class="table table-zebra">
                <thead>
                  <tr>
                    <th>{{ __('frontend.notifications.form.event') }}</th>
                    <th>{{ __('frontend.notifications.form.currency') }}</th>
                    <th>{{ __('frontend.notifications.rules.min_amount', { amount: '', currency: '' }).replace(' :', '') }}</th>
                    <th>{{ __('frontend.notifications.form.statuses') }}</th>
                    <th>{{ __('frontend.notifications.form.channels') }}</th>
                    <th>{{ __('frontend.notifications.form.enabled') }}</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="rule in rules" :key="rule.id">
                    <td><span class="badge badge-ghost">{{ eventLabel(rule.event) }}</span></td>
                    <td><span class="badge badge-primary badge-outline uppercase">{{ rule.currency }}</span></td>
                    <td>{{ rule.min_amount }} {{ rule.currency }}</td>
                    <td>{{ rule.statuses?.length ? rule.statuses.join(', ') : '—' }}</td>
                    <td>{{ rule.channels.map(channelLabel).join(', ') }}</td>
                    <td>
                      <span class="badge badge-sm" :class="rule.enabled ? 'badge-success' : 'badge-ghost'">
                        {{ rule.enabled ? __('frontend.notifications.rules.enabled') : __('frontend.notifications.rules.disabled') }}
                      </span>
                    </td>
                    <td class="text-right">
                      <div class="flex justify-end gap-2">
                        <button class="btn btn-ghost btn-xs" @click="editRule(rule)">{{ __('frontend.notifications.rules.edit') }}</button>
                        <button class="btn btn-outline btn-xs" @click="toggleRule(rule)">{{ rule.enabled ? __('frontend.notifications.rules.disable') : __('frontend.notifications.rules.enable') }}</button>
                        <button class="btn btn-error btn-xs" @click="deleteRule(rule)">{{ __('frontend.notifications.rules.delete') }}</button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="rules.length === 0">
                    <td colspan="7" class="text-center text-sm opacity-70 py-6">
                      {{ __('frontend.notifications.rules.empty') }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="lg:hidden space-y-3">
              <div v-for="rule in rules" :key="rule.id" class="card bg-base-100 shadow-sm">
                <div class="card-body p-4 space-y-3">
                  <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                      <span class="badge badge-primary badge-outline uppercase">{{ rule.currency }}</span>
                      <span class="badge badge-ghost">{{ eventLabel(rule.event) }}</span>
                    </div>
                    <span class="badge badge-sm" :class="rule.enabled ? 'badge-success' : 'badge-ghost'">
                      {{ rule.enabled ? __('frontend.notifications.rules.enabled') : __('frontend.notifications.rules.disabled') }}
                    </span>
                  </div>
                  <div class="flex flex-wrap gap-2 text-sm opacity-80">
                    <span>{{ __('frontend.notifications.rules.min_amount', { amount: rule.min_amount, currency: rule.currency }) }}</span>
                    <span v-if="rule.statuses.length">{{ __('frontend.notifications.rules.statuses') }}: {{ rule.statuses.join(', ') }}</span>
                    <span>{{ __('frontend.notifications.rules.channels') }}: {{ rule.channels.map(channelLabel).join(', ') }}</span>
                  </div>
                  <div class="flex flex-wrap gap-2 justify-end">
                    <button class="btn btn-ghost btn-xs" @click="editRule(rule)">{{ __('frontend.notifications.rules.edit') }}</button>
                    <button class="btn btn-outline btn-xs" @click="toggleRule(rule)">{{ rule.enabled ? __('frontend.notifications.rules.disable') : __('frontend.notifications.rules.enable') }}</button>
                    <button class="btn btn-error btn-xs" @click="deleteRule(rule)">{{ __('frontend.notifications.rules.delete') }}</button>
                  </div>
                </div>
              </div>
              <div v-if="rules.length === 0" class="text-center text-sm opacity-70 py-6">
                {{ __('frontend.notifications.rules.empty') }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

