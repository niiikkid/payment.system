<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { vueLang } from '@erag/lang-sync-inertia';
import MerchantSelect from '@/components/merchants/MerchantSelect.vue';
import { usePage } from '@inertiajs/vue3'
import type { CurrencyAmountRule } from '@/utils/currencyAmount'
import { normalizeCurrencyAmountOnBlur, sanitizeCurrencyAmountInput } from '@/utils/currencyAmount'

interface Props {
    apiKey: string;
    apiBase: string;
}

const props = defineProps<Props>();
const { __ } = vueLang();
const page = usePage()

const currencyAmountRules = computed<Record<string, CurrencyAmountRule>>(
    () => (page.props.currencyAmountRules as any) ?? {},
)

type MerchantOption = {
    value: string | number;
    label: string;
    initials?: string | null;
    logo_url?: string | null;
    description?: string | null;
};

function pretty(obj: unknown) {
    try {
        const json = JSON.stringify(obj, null, 2);
        return json.split('\n').map(line => '    ' + line).join('\n');
    } catch {
        return String(obj);
    }
}

async function requestJson(path: string, init?: RequestInit) {
    const res = await fetch(props.apiBase + path, {
        ...init,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Api-Key': props.apiKey ?? '',
            ...(init?.headers || {}),
        },
    });
    const isJson = res.headers.get('content-type')?.includes('application/json');
    const body = isJson ? await res.json() : await res.text();
    return { ok: res.ok, status: res.status, body } as const;
}

async function requestBlob(path: string) {
    const res = await fetch(props.apiBase + path, {
        headers: {
            'Accept': 'application/json',
            'X-Api-Key': props.apiKey ?? '',
        },
    });
    const blob = await res.blob();
    return { ok: res.ok, status: res.status, blob } as const;
}

// Forms state
const createForm = reactive({
    currency: 'USDT',
    network: 'tron',
    amount: '12.50',
    external_invoice_id: 'ORDER-123',
    callback_url: '',
    tag: '',
    merchant_id: '',
    client_id: '',
    product_name: '',
    product_description: '',
    metadata: '',
});

const createAmountRule = computed<CurrencyAmountRule>(() => {
    const currency = (createForm.currency || '').toString().toUpperCase()
    return currencyAmountRules.value[currency] ?? { decimals: 6, example: '12.123456', decimal_separator: '.' }
})

watch(
    () => createForm.amount,
    (value) => {
        const decimals = Number(createAmountRule.value.decimals ?? 0)
        const sanitized = sanitizeCurrencyAmountInput(String(value ?? ''), decimals)
        if (sanitized !== value) {
            createForm.amount = sanitized
        }
    },
)

watch(
    () => createForm.currency,
    () => {
        const decimals = Number(createAmountRule.value.decimals ?? 0)
        const sanitized = sanitizeCurrencyAmountInput(String(createForm.amount ?? ''), decimals)
        if (sanitized !== createForm.amount) {
            createForm.amount = sanitized
        }
    },
)
const createResult = ref<string>('');
const merchants = ref<MerchantOption[]>([]);
const merchantsLoading = ref(false);
const merchantsError = ref<string>('');
const tabs = reactive({
    create: 'form',
    list: 'form',
    get: 'form',
    status: 'form',
    public: 'form',
    qr: 'form',
    cancel: 'form',
    clients: 'form',
    clientCreate: 'form',
});

async function loadMerchants() {
    merchantsLoading.value = true;
    merchantsError.value = '';
    const res = await requestJson('/merchants');
    merchantsLoading.value = false;

    if (!res.ok) {
        merchants.value = [];
        merchantsError.value = __('frontend.api.requests.merchants_load_failed');
        return;
    }

    const list = Array.isArray(res.body)
        ? res.body
        : Array.isArray(res.body?.merchants)
            ? res.body.merchants
            : [];

    merchants.value = list.map((merchant: any) => ({
        value: merchant.id,
        label: merchant.name,
        initials: merchant.initials ?? null,
        logo_url: merchant.logo_url ?? null,
        description: merchant.description ?? '',
    }));
}

onMounted(loadMerchants);

async function createInvoice() {
    let metadata: unknown = undefined;
    if (createForm.metadata) {
        try { metadata = JSON.parse(createForm.metadata); } catch { metadata = undefined; }
    }
    const payload: Record<string, unknown> = {
        currency: createForm.currency,
        network: createForm.network,
        amount: createForm.amount,
        external_invoice_id: createForm.external_invoice_id || undefined,
        callback_url: createForm.callback_url || undefined,
        tag: createForm.tag || undefined,
        merchant_id: createForm.merchant_id || undefined,
        client_id: createForm.client_id || undefined,
        product_name: createForm.product_name || undefined,
        product_description: createForm.product_description || undefined,
        metadata: metadata,
    };
    const res = await requestJson('/invoices', { method: 'POST', body: JSON.stringify(payload) });
    createResult.value = pretty({ status: res.status, body: res.body });
    if (res.ok && (res.body?.id || res.body?.invoice?.id)) {
        const id = res.body?.id ?? res.body?.invoice?.id;
        getByIdForm.id = id as string;
        getStatusForm.id = id as string;
        publicForm.id = id as string;
        qrForm.id = id as string;
        cancelForm.id = id as string;
    }
}

const listForm = reactive({
    search: '',
    status: '',
    currency: '',
    network: '',
    merchant_id: '',
    client_id: '',
    external_invoice_id: '',
    tag: '',
    has_callback: false,
    page: 1,
    per_page: 20,
});
const listResult = ref<string>('');

function buildQuery(params: Record<string, unknown>): string {
    const query = new URLSearchParams();

    Object.entries(params).forEach(([key, value]) => {
        if (value === undefined || value === null) {
            return;
        }

        if (typeof value === 'boolean') {
            if (value) {
                query.append(key, '1');
            }
            return;
        }

        const asString = String(value).trim();
        if (asString !== '') {
            query.append(key, asString);
        }
    });

    const qs = query.toString();
    return qs ? `?${qs}` : '';
}

async function listInvoices() {
    const res = await requestJson(`/invoices${buildQuery(listForm)}`);
    listResult.value = pretty({ status: res.status, body: res.body });
}

const clientsResult = ref<string>('');
const clientsForm = reactive({
    page: 1,
    per_page: 20,
});

async function loadClientsList() {
    const res = await requestJson(`/clients${buildQuery(clientsForm)}`);
    clientsResult.value = pretty({ status: res.status, body: res.body });
}

const createClientForm = reactive({
    external_id: 'customer-123',
    name: '',
    telegram: '',
    contact: '',
});
const createClientResult = ref<string>('');

async function createClientRequest() {
    const payload: Record<string, unknown> = {
        external_id: createClientForm.external_id || undefined,
        name: createClientForm.name || undefined,
        telegram: createClientForm.telegram || undefined,
        contact: createClientForm.contact || undefined,
    };

    const res = await requestJson('/clients', { method: 'POST', body: JSON.stringify(payload) });
    createClientResult.value = pretty({ status: res.status, body: res.body });

    if (res.ok) {
        loadClientsList();
    }
}

const getByIdForm = reactive({ id: '' });
const getByIdResult = ref<string>('');
async function getInvoice() {
    if (!getByIdForm.id) return;
    const res = await requestJson(`/invoices/${encodeURIComponent(getByIdForm.id)}`);
    getByIdResult.value = pretty({ status: res.status, body: res.body });
}

const getStatusForm = reactive({ id: '' });
const getStatusResult = ref<string>('');
async function getStatus() {
    if (!getStatusForm.id) return;
    const res = await requestJson(`/invoices/${encodeURIComponent(getStatusForm.id)}/status`);
    getStatusResult.value = pretty({ status: res.status, body: res.body });
}

const publicForm = reactive({ id: '' });
const publicResult = ref<string>('');
async function getPublic() {
    if (!publicForm.id) return;
    const res = await requestJson(`/invoices/${encodeURIComponent(publicForm.id)}/public`);
    publicResult.value = pretty({ status: res.status, body: res.body });
}

const qrForm = reactive({ id: '' });
const qrUrl = ref<string>('');
async function getQr() {
    if (!qrForm.id) return;
    const res = await requestBlob(`/invoices/${encodeURIComponent(qrForm.id)}/qr`);
    if (res.ok) {
        const url = URL.createObjectURL(res.blob);
        qrUrl.value = url;
    } else {
        qrUrl.value = '';
    }
}

const cancelForm = reactive({ id: '' });
const cancelResult = ref<string>('');
async function cancelInvoice() {
    if (!cancelForm.id) return;
    const res = await requestJson(`/invoices/${encodeURIComponent(cancelForm.id)}/cancel`, { method: 'POST' });
    cancelResult.value = pretty({ status: res.status, body: res.body });
}
</script>

<template>
    <div class="space-y-4">
        <div class="collapse collapse-arrow bg-base-100 border">
            <input type="checkbox" />
            <div class="collapse-title text-md font-medium">{{ __('frontend.api.requests.create_title') }}</div>
            <div class="collapse-content space-y-4">
                <div class="tabs tabs-boxed w-full">
                    <button class="tab" :class="tabs.create === 'form' ? 'tab-active' : ''" @click="tabs.create = 'form'">Форма</button>
                    <button class="tab" :class="tabs.create === 'example' ? 'tab-active' : ''" @click="tabs.create = 'example'">Пример запроса</button>
                </div>
                <p class="text-sm text-base-content/70">{{ __('frontend.api.requests.create_description') }}</p>
                <div v-if="tabs.create === 'form'" class="grid md:grid-cols-3 gap-4">
                    <div class="card-body gap-4 p-0 pt-3 w-full">
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.currency') }}</span>
                            <input class="input input-md w-full" v-model="createForm.currency" placeholder="USDT" />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.network') }}</span>
                            <input class="input input-md w-full" v-model="createForm.network" placeholder="tron" />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.amount') }}</span>
                            <input
                                class="input input-md w-full"
                                v-model="createForm.amount"
                                inputmode="decimal"
                                :placeholder="createAmountRule.example || '12.50'"
                                @blur="createForm.amount = normalizeCurrencyAmountOnBlur(createForm.amount)"
                            />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.external_id') }}</span>
                            <input class="input input-md w-full" v-model="createForm.external_invoice_id" placeholder="ORDER-123" />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.callback_url') }}</span>
                            <input class="input input-md w-full" v-model="createForm.callback_url" placeholder="https://example.com/callback" />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.tag') }}</span>
                            <input class="input input-md w-full" v-model="createForm.tag" placeholder="vip" />
                        </label>
                        <div class="space-y-2">
                            <span class="text-sm font-medium">{{ __('frontend.api.requests.fields.merchant') }}</span>
                            <MerchantSelect
                                v-model="createForm.merchant_id"
                                :options="merchants"
                                :placeholder="__('frontend.invoices.fields.merchant_placeholder')"
                                :empty-label="__('frontend.invoices.fields.merchant_empty')"
                                :disabled="merchantsLoading"
                            />
                            <p v-if="merchantsError" class="text-error text-sm">{{ merchantsError }}</p>
                        </div>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.product_name') }}</span>
                            <input
                                class="input input-md w-full"
                                v-model="createForm.product_name"
                                :placeholder="__('frontend.invoices.fields.product_name_placeholder')"
                            />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.product_description') }}</span>
                            <textarea
                                class="textarea textarea-md textarea-bordered w-full"
                                v-model="createForm.product_description"
                                rows="3"
                                :placeholder="__('frontend.invoices.fields.product_description_placeholder')"
                            ></textarea>
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.metadata') }}</span>
                            <textarea class="textarea textarea-md textarea-bordered w-full" v-model="createForm.metadata" rows="4" placeholder='{"note":"vip"}'></textarea>
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.client_id') }}</span>
                            <input class="input input-md w-full" v-model="createForm.client_id" placeholder="customer-123" />
                        </label>
                        <div class="card-actions items-center gap-6">
                            <button class="btn btn-primary" @click="createInvoice">{{ __('frontend.api.requests.send') }}</button>
                        </div>
                    </div>
                    <div class="min-w-0 w-full sm:col-span-1 md:col-span-2">
                        <label class="label"><span class="label-text">{{ __('frontend.api.requests.response') }}</span></label>
                        <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full"><code class="block">{{ createResult }}</code></pre>
                    </div>
                </div>
                <div v-else class="space-y-3 text-sm">
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Headers</p>
                        <ul class="list-disc list-inside">
                            <li>Accept: application/json</li>
                            <li>Content-Type: application/json</li>
                            <li>X-Api-Key: &lt;PUBLIC_API_KEY&gt;</li>
                        </ul>
                    </div>
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Параметры (body)</p>
                        <ul class="list-disc list-inside">
                            <li>currency (string, required)</li>
                            <li>network (string, required)</li>
                            <li>amount (string, required)</li>
                            <li>client_id, merchant_id</li>
                            <li>external_invoice_id, tag</li>
                            <li>callback_url</li>
                            <li>product_name, product_description</li>
                            <li>metadata (JSON объект)</li>
                        </ul>
                    </div>
                    <div>
                        <p class="font-semibold mb-1">Пример запроса (curl)</p>
                        <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full pl-4"><code class="block">{{ `curl -X POST '${props.apiBase}/invoices' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -H 'X-Api-Key: <PUBLIC_API_KEY>' \
  -d '{
    "currency": "USDT",
    "network": "tron",
    "amount": "12.34",
    "client_id": "customer-123",
    "external_invoice_id": "order-1"
  }'` }}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100 border">
            <input type="checkbox" />
            <div class="collapse-title text-md font-medium">{{ __('frontend.api.requests.list_title') }}</div>
            <div class="collapse-content space-y-4">
                <p class="text-sm text-base-content/70">{{ __('frontend.api.requests.list_description') }}</p>
                <div class="tabs tabs-boxed w-full">
                    <button class="tab" :class="tabs.list === 'form' ? 'tab-active' : ''" @click="tabs.list = 'form'">Форма</button>
                    <button class="tab" :class="tabs.list === 'example' ? 'tab-active' : ''" @click="tabs.list = 'example'">Пример запроса</button>
                </div>
                <div v-if="tabs.list === 'form'" class="grid md:grid-cols-3 gap-4">
                    <div class="card-body gap-4 p-0 pt-3 w-full">
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.search') }}</span>
                            <input class="input input-md w-full" v-model="listForm.search" :placeholder="__('frontend.invoices_page.filters.search_placeholder')" />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.status') }}</span>
                            <input class="input input-md w-full" v-model="listForm.status" placeholder="pending" />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.currency') }}</span>
                            <input class="input input-md w-full" v-model="listForm.currency" placeholder="USDT" />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.network') }}</span>
                            <input class="input input-md w-full" v-model="listForm.network" placeholder="tron" />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.merchant_id') }}</span>
                            <input class="input input-md w-full" v-model="listForm.merchant_id" placeholder="1" />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.client_id') }}</span>
                            <input class="input input-md w-full" v-model="listForm.client_id" placeholder="customer-123" />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.external_id') }}</span>
                            <input class="input input-md w-full" v-model="listForm.external_invoice_id" placeholder="ORDER-123" />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.tag') }}</span>
                            <input class="input input-md w-full" v-model="listForm.tag" placeholder="vip" />
                        </label>
                        <label class="flex items-center gap-3">
                            <input type="checkbox" class="checkbox checkbox-primary" v-model="listForm.has_callback" />
                            <span class="label-text">{{ __('frontend.api.requests.fields.has_callback') }}</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="floating-label">
                                <span>{{ __('frontend.api.requests.fields.page') }}</span>
                                <input class="input input-md w-full" v-model.number="listForm.page" type="number" min="1" />
                            </label>
                            <label class="floating-label">
                                <span>{{ __('frontend.api.requests.fields.per_page') }}</span>
                                <input class="input input-md w-full" v-model.number="listForm.per_page" type="number" min="1" max="100" />
                            </label>
                        </div>
                        <div class="card-actions items-center gap-6">
                            <button class="btn btn-primary" @click="listInvoices">{{ __('frontend.api.requests.list_send') }}</button>
                        </div>
                    </div>
                    <div class="min-w-0 w-full sm:col-span-1 md:col-span-2">
                        <label class="label"><span class="label-text">{{ __('frontend.api.requests.response') }}</span></label>
                        <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full"><code class="block">{{ listResult }}</code></pre>
                    </div>
                </div>
                <div v-else class="space-y-3 text-sm">
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Headers</p>
                        <ul class="list-disc list-inside">
                            <li>Accept: application/json</li>
                            <li>X-Api-Key: &lt;PUBLIC_API_KEY&gt;</li>
                        </ul>
                    </div>
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Параметры (query)</p>
                        <ul class="list-disc list-inside">
                            <li>status, currency, network</li>
                            <li>merchant_id, client_id</li>
                            <li>external_invoice_id, tag, search</li>
                            <li>has_callback (1), page (≥1), per_page (1..100)</li>
                        </ul>
                    </div>
                    <div>
                        <p class="font-semibold mb-1">Пример запроса (curl)</p>
                        <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full pl-4"><code class="block">{{ `curl -X GET '${props.apiBase}/invoices?status=pending&client_id=customer-123&per_page=20' \
  -H 'Accept: application/json' \
  -H 'X-Api-Key: <PUBLIC_API_KEY>'` }}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100 border">
            <input type="checkbox" />
            <div class="collapse-title text-md font-medium">{{ __('frontend.api.requests.get_title') }}</div>
            <div class="collapse-content space-y-4">
                <div class="tabs tabs-boxed w-full">
                    <button class="tab" :class="tabs.get === 'form' ? 'tab-active' : ''" @click="tabs.get = 'form'">Форма</button>
                    <button class="tab" :class="tabs.get === 'example' ? 'tab-active' : ''" @click="tabs.get = 'example'">Пример запроса</button>
                </div>
                <div v-if="tabs.get === 'form'" class="grid md:grid-cols-3 gap-4">
                    <div class="card-body gap-4 p-0 pt-3 w-full">
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.invoice_id') }}</span>
                            <input class="input input-md w-full" v-model="getByIdForm.id" placeholder="e.g. inv_123" />
                        </label>
                        <div class="card-actions items-center gap-6">
                            <button class="btn btn-primary" @click="getInvoice">{{ __('frontend.api.requests.request') }}</button>
                        </div>
                    </div>
                    <div class="min-w-0 w-full sm:col-span-1 md:col-span-2">
                        <label class="label"><span class="label-text">{{ __('frontend.api.requests.response') }}</span></label>
                        <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full"><code class="block">{{ getByIdResult }}</code></pre>
                    </div>
                </div>
                <div v-else class="space-y-3 text-sm">
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Headers</p>
                        <ul class="list-disc list-inside">
                            <li>Accept: application/json</li>
                            <li>X-Api-Key: &lt;PUBLIC_API_KEY&gt;</li>
                        </ul>
                    </div>
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Параметры (path)</p>
                        <ul class="list-disc list-inside">
                            <li>id (string, required)</li>
                        </ul>
                    </div>
                    <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full pl-4"><code class="block">{{ `curl -X GET '${props.apiBase}/invoices/<id>' \
  -H 'Accept: application/json' \
  -H 'X-Api-Key: <PUBLIC_API_KEY>'` }}</code></pre>
                </div>
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100 border">
            <input type="checkbox" />
            <div class="collapse-title text-md font-medium">{{ __('frontend.api.requests.status_title') }}</div>
            <div class="collapse-content space-y-4">
                <div class="tabs tabs-boxed w-full">
                    <button class="tab" :class="tabs.status === 'form' ? 'tab-active' : ''" @click="tabs.status = 'form'">Форма</button>
                    <button class="tab" :class="tabs.status === 'example' ? 'tab-active' : ''" @click="tabs.status = 'example'">Пример запроса</button>
                </div>
                <div v-if="tabs.status === 'form'" class="grid md:grid-cols-3 gap-4">
                    <div class="card-body gap-4 p-0 pt-3 w-full">
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.invoice_id') }}</span>
                            <input class="input input-md w-full" v-model="getStatusForm.id" placeholder="e.g. inv_123" />
                        </label>
                        <div class="card-actions items-center gap-6">
                            <button class="btn btn-primary" @click="getStatus">{{ __('frontend.api.requests.request') }}</button>
                        </div>
                    </div>
                    <div class="min-w-0 w-full sm:col-span-1 md:col-span-2">
                        <label class="label"><span class="label-text">{{ __('frontend.api.requests.response') }}</span></label>
                        <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full"><code class="block">{{ getStatusResult }}</code></pre>
                    </div>
                </div>
                <div v-else class="space-y-3 text-sm">
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Headers</p>
                        <ul class="list-disc list-inside">
                            <li>Accept: application/json</li>
                            <li>X-Api-Key: &lt;PUBLIC_API_KEY&gt;</li>
                        </ul>
                    </div>
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Параметры (path)</p>
                        <ul class="list-disc list-inside">
                            <li>id (string, required)</li>
                        </ul>
                    </div>
                    <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full pl-4"><code class="block">{{ `curl -X GET '${props.apiBase}/invoices/<id>/status' \
  -H 'Accept: application/json' \
  -H 'X-Api-Key: <PUBLIC_API_KEY>'` }}</code></pre>
                </div>
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100 border">
            <input type="checkbox" />
            <div class="collapse-title text-md font-medium">{{ __('frontend.api.requests.public_title') }}</div>
            <div class="collapse-content space-y-4">
                <div class="tabs tabs-boxed w-full">
                    <button class="tab" :class="tabs.public === 'form' ? 'tab-active' : ''" @click="tabs.public = 'form'">Форма</button>
                    <button class="tab" :class="tabs.public === 'example' ? 'tab-active' : ''" @click="tabs.public = 'example'">Пример запроса</button>
                </div>
                <div v-if="tabs.public === 'form'" class="grid md:grid-cols-3 gap-4">
                    <div class="card-body gap-4 p-0 pt-3 w-full">
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.invoice_id') }}</span>
                            <input class="input input-md w-full" v-model="publicForm.id" placeholder="e.g. inv_123" />
                        </label>
                        <div class="card-actions items-center gap-6">
                            <button class="btn btn-primary" @click="getPublic">{{ __('frontend.api.requests.request') }}</button>
                        </div>
                    </div>
                    <div class="min-w-0 w-full sm:col-span-1 md:col-span-2">
                        <label class="label"><span class="label-text">{{ __('frontend.api.requests.response') }}</span></label>
                        <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full"><code class="block">{{ publicResult }}</code></pre>
                    </div>
                </div>
                <div v-else class="space-y-3 text-sm">
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Headers</p>
                        <ul class="list-disc list-inside">
                            <li>Accept: application/json</li>
                            <li>X-Api-Key: &lt;PUBLIC_API_KEY&gt;</li>
                        </ul>
                    </div>
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Параметры (path)</p>
                        <ul class="list-disc list-inside">
                            <li>id (string, required)</li>
                        </ul>
                    </div>
                    <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full pl-4"><code class="block">{{ `curl -X GET '${props.apiBase}/invoices/<id>/public' \
  -H 'Accept: application/json' \
  -H 'X-Api-Key: <PUBLIC_API_KEY>'` }}</code></pre>
                </div>
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100 border">
            <input type="checkbox" />
            <div class="collapse-title text-md font-medium">{{ __('frontend.api.requests.qr_title') }}</div>
            <div class="collapse-content space-y-4">
                <div class="tabs tabs-boxed w-full">
                    <button class="tab" :class="tabs.qr === 'form' ? 'tab-active' : ''" @click="tabs.qr = 'form'">Форма</button>
                    <button class="tab" :class="tabs.qr === 'example' ? 'tab-active' : ''" @click="tabs.qr = 'example'">Пример запроса</button>
                </div>
                <div v-if="tabs.qr === 'form'" class="grid md:grid-cols-3 gap-4">
                    <div class="card-body gap-4 p-0 pt-3 w-full">
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.invoice_id') }}</span>
                            <input class="input input-md w-full" v-model="qrForm.id" placeholder="e.g. inv_123" />
                        </label>
                        <div class="card-actions items-center gap-6">
                            <button class="btn btn-primary" @click="getQr">{{ __('frontend.api.requests.request') }}</button>
                        </div>
                    </div>
                    <div class="min-w-0 w-full sm:col-span-1 md:col-span-2">
                        <label class="label"><span class="label-text">{{ __('frontend.api.requests.qr_preview') }}</span></label>
                        <div class="border rounded-box p-3 min-h-32 flex items-center justify-center bg-base-200">
                            <img v-if="qrUrl" :src="qrUrl" alt="QR" class="max-h-64" />
                            <span v-else class="text-base-content/60">{{ __('frontend.api.requests.qr_no_data') }}</span>
                        </div>
                    </div>
                </div>
                <div v-else class="space-y-3 text-sm">
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Headers</p>
                        <ul class="list-disc list-inside">
                            <li>Accept: application/json</li>
                            <li>X-Api-Key: &lt;PUBLIC_API_KEY&gt;</li>
                        </ul>
                    </div>
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Параметры (path)</p>
                        <ul class="list-disc list-inside">
                            <li>id (string, required)</li>
                        </ul>
                    </div>
                    <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full pl-4"><code class="block">{{ `curl -X GET '${props.apiBase}/invoices/<id>/qr' \
  -H 'Accept: application/json' \
  -H 'X-Api-Key: <PUBLIC_API_KEY>' \
  -o qr.png` }}</code></pre>
                </div>
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100 border">
            <input type="checkbox" />
            <div class="collapse-title text-md font-medium">{{ __('frontend.api.requests.cancel_title') }}</div>
            <div class="collapse-content space-y-4">
                <div class="tabs tabs-boxed w-full">
                    <button class="tab" :class="tabs.cancel === 'form' ? 'tab-active' : ''" @click="tabs.cancel = 'form'">Форма</button>
                    <button class="tab" :class="tabs.cancel === 'example' ? 'tab-active' : ''" @click="tabs.cancel = 'example'">Пример запроса</button>
                </div>
                <div v-if="tabs.cancel === 'form'" class="grid md:grid-cols-3 gap-4">
                    <div class="card-body gap-4 p-0 pt-3 w-full">
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.invoice_id') }}</span>
                            <input class="input input-md w-full" v-model="cancelForm.id" placeholder="e.g. inv_123" />
                        </label>
                        <div class="card-actions items-center gap-6">
                            <button class="btn btn-warning" @click="cancelInvoice">{{ __('frontend.api.requests.cancel') }}</button>
                        </div>
                    </div>
                    <div class="min-w-0 w-full sm:col-span-1 md:col-span-2">
                        <label class="label"><span class="label-text">{{ __('frontend.api.requests.response') }}</span></label>
                        <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full"><code class="block">{{ cancelResult }}</code></pre>
                    </div>
                </div>
                <div v-else class="space-y-3 text-sm">
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Headers</p>
                        <ul class="list-disc list-inside">
                            <li>Accept: application/json</li>
                            <li>Content-Type: application/json</li>
                            <li>X-Api-Key: &lt;PUBLIC_API_KEY&gt;</li>
                        </ul>
                    </div>
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Параметры (path)</p>
                        <ul class="list-disc list-inside">
                            <li>id (string, required)</li>
                        </ul>
                    </div>
                    <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full pl-4"><code class="block">{{ `curl -X POST '${props.apiBase}/invoices/<id>/cancel' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -H 'X-Api-Key: <PUBLIC_API_KEY>'` }}</code></pre>
                </div>
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100 border">
            <input type="checkbox" />
            <div class="collapse-title text-md font-medium">{{ __('frontend.api.requests.clients_title') }}</div>
            <div class="collapse-content space-y-4">
                <p class="text-sm text-base-content/70">{{ __('frontend.api.requests.clients_description') }}</p>
                <div class="tabs tabs-boxed w-full">
                    <button class="tab" :class="tabs.clients === 'form' ? 'tab-active' : ''" @click="tabs.clients = 'form'">Форма</button>
                    <button class="tab" :class="tabs.clients === 'example' ? 'tab-active' : ''" @click="tabs.clients = 'example'">Пример запроса</button>
                </div>
                <div v-if="tabs.clients === 'form'" class="grid md:grid-cols-3 gap-4">
                    <div class="card-body gap-4 p-0 pt-3 w-full">
                        <div class="grid grid-cols-2 gap-3">
                            <label class="floating-label">
                                <span>{{ __('frontend.api.requests.fields.page') }}</span>
                                <input class="input input-md w-full" v-model.number="clientsForm.page" type="number" min="1" />
                            </label>
                            <label class="floating-label">
                                <span>{{ __('frontend.api.requests.fields.per_page') }}</span>
                                <input class="input input-md w-full" v-model.number="clientsForm.per_page" type="number" min="1" max="100" />
                            </label>
                        </div>
                        <div class="card-actions items-center gap-6">
                            <button class="btn btn-primary" @click="loadClientsList">{{ __('frontend.api.requests.request') }}</button>
                        </div>
                    </div>
                    <div class="min-w-0 w-full sm:col-span-1 md:col-span-2">
                        <label class="label"><span class="label-text">{{ __('frontend.api.requests.response') }}</span></label>
                        <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full"><code class="block">{{ clientsResult }}</code></pre>
                    </div>
                </div>
                <div v-else class="space-y-3 text-sm">
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Headers</p>
                        <ul class="list-disc list-inside">
                            <li>Accept: application/json</li>
                            <li>X-Api-Key: &lt;PUBLIC_API_KEY&gt;</li>
                        </ul>
                    </div>
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Параметры (query)</p>
                        <ul class="list-disc list-inside">
                            <li>page (number, ≥1)</li>
                            <li>per_page (number, 1..100)</li>
                        </ul>
                    </div>
                    <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full pl-4"><code class="block">{{ `curl -X GET '${props.apiBase}/clients?page=1&per_page=20' \
  -H 'Accept: application/json' \
  -H 'X-Api-Key: <PUBLIC_API_KEY>'` }}</code></pre>
                </div>
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100 border">
            <input type="checkbox" />
            <div class="collapse-title text-md font-medium">{{ __('frontend.api.requests.client_create_title') }}</div>
            <div class="collapse-content space-y-4">
                <p class="text-sm text-base-content/70">{{ __('frontend.api.requests.client_create_description') }}</p>
                <div class="tabs tabs-boxed w-full">
                    <button class="tab" :class="tabs.clientCreate === 'form' ? 'tab-active' : ''" @click="tabs.clientCreate = 'form'">Форма</button>
                    <button class="tab" :class="tabs.clientCreate === 'example' ? 'tab-active' : ''" @click="tabs.clientCreate = 'example'">Пример запроса</button>
                </div>
                <div v-if="tabs.clientCreate === 'form'" class="grid md:grid-cols-3 gap-4">
                    <div class="card-body gap-4 p-0 pt-3 w-full">
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.external_client_id') }}</span>
                            <input class="input input-md w-full" v-model="createClientForm.external_id" placeholder="customer-123" />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.client_name') }}</span>
                            <input class="input input-md w-full" v-model="createClientForm.name" :placeholder="__('frontend.clients.fields.name_placeholder')" />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.client_telegram') }}</span>
                            <input class="input input-md w-full" v-model="createClientForm.telegram" placeholder="@customer" />
                        </label>
                        <label class="floating-label">
                            <span>{{ __('frontend.api.requests.fields.client_contact') }}</span>
                            <input class="input input-md w-full" v-model="createClientForm.contact" :placeholder="__('frontend.clients.fields.contact_placeholder')" />
                        </label>
                        <div class="card-actions items-center gap-6">
                            <button class="btn btn-primary" @click="createClientRequest">{{ __('frontend.api.requests.send') }}</button>
                        </div>
                    </div>
                    <div class="min-w-0 w-full sm:col-span-1 md:col-span-2">
                        <label class="label"><span class="label-text">{{ __('frontend.api.requests.response') }}</span></label>
                        <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full"><code class="block">{{ createClientResult }}</code></pre>
                    </div>
                </div>
                <div v-else class="space-y-3 text-sm">
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Headers</p>
                        <ul class="list-disc list-inside">
                            <li>Accept: application/json</li>
                            <li>Content-Type: application/json</li>
                            <li>X-Api-Key: &lt;PUBLIC_API_KEY&gt;</li>
                        </ul>
                    </div>
                    <div class="bg-base-200 rounded-box p-3">
                        <p class="font-semibold">Параметры (body)</p>
                        <ul class="list-disc list-inside">
                            <li>external_id</li>
                            <li>name</li>
                            <li>telegram</li>
                            <li>contact</li>
                        </ul>
                    </div>
                    <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full pl-4"><code class="block">{{ `curl -X POST '${props.apiBase}/clients' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -H 'X-Api-Key: <PUBLIC_API_KEY>' \
  -d '{
    "external_id": "customer-123",
    "name": "VIP Client",
    "telegram": "@vip",
    "contact": "vip@example.com"
  }'` }}</code></pre>
                </div>
            </div>
        </div>
    </div>
</template>

