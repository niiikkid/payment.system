<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { computed, reactive, ref } from 'vue';

type PageProps = {
    publicApiKey: string;
    apiBaseUrl: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'API и документация', href: '/api' },
];

const page = usePage();
const props = computed(() => page.props as unknown as PageProps);

const apiKey = ref<string>(props.value.publicApiKey || '');
const apiBase = ref<string>(props.value.apiBaseUrl || '/api/v1');
const copied = ref<boolean>(false);

function copyToken() {
    if (!apiKey.value) return;
    navigator.clipboard?.writeText(apiKey.value)
        .then(() => {
            copied.value = true;
            window.setTimeout(() => { copied.value = false; }, 2000);
        })
        .catch(() => {});
}

function pretty(obj: unknown) {
    try { return JSON.stringify(obj, null, 2); } catch { return String(obj); }
}

async function requestJson(path: string, init?: RequestInit) {
    const res = await fetch(apiBase.value + path, {
        ...init,
        headers: {
            'Content-Type': 'application/json',
            'X-Api-Key': apiKey.value ?? '',
            ...(init?.headers || {}),
        },
    });
    const isJson = res.headers.get('content-type')?.includes('application/json');
    const body = isJson ? await res.json() : await res.text();
    return { ok: res.ok, status: res.status, body } as const;
}

async function requestBlob(path: string) {
    const res = await fetch(apiBase.value + path, {
        headers: { 'X-Api-Key': apiKey.value ?? '' },
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
    metadata: '{"note":"vip"}',
});
const createResult = ref<string>('');

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
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="API и документация" />

        <div class="flex items-center justify-between gap-4">
            <h1 class="text-xl font-semibold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 opacity-60">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z" />
                </svg>
                API и документация
            </h1>
        </div>

        <p class="mt-2 text-sm text-base-content/70">Управляйте токеном и тестируйте вызовы прямо со страницы</p>

        <div class="mt-6 space-y-8">

            <!-- Token -->
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title">API Token</h3>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Заголовок X-Api-Key</span>
                        </label>
                        <div class="join w-full">
                            <input class="input input-bordered join-item w-full" :value="apiKey" readonly />
                            <button
                                class="btn join-item"
                                :class="copied ? 'btn-success' : 'btn-primary'"
                                type="button"
                                @click="copyToken"
                            >{{ copied ? 'Скопировано' : 'Копировать' }}</button>
                        </div>
                        <p class="text-xs text-base-content/60 mt-2">Используйте это значение в заголовке <code>X-Api-Key</code> при запросах к API.</p>
                    </div>
                    <div class="form-control mt-4">
                        <label class="label">
                            <span class="label-text">Базовый URL API</span>
                        </label>
                        <input class="input input-bordered w-full" :value="apiBase" readonly />
                    </div>
                </div>
            </div>

            <!-- Docs and Playground -->
            <div class="collapse collapse-arrow bg-base-100 border">
                <input type="checkbox" />
                <div class="collapse-title text-md font-medium">POST /invoices — Создать инвойс</div>
                <div class="collapse-content space-y-4">
                    <p class="text-sm text-base-content/70">Создаёт инвойс и возвращает объект. Требует: currency, network, amount. Опционально: external_invoice_id, callback_url, tag, metadata.</p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="card-body gap-4 p-0">
                            <label class="floating-label">
                                <span>Валюта</span>
                                <input class="input input-md" v-model="createForm.currency" placeholder="USDT" />
                            </label>
                            <label class="floating-label">
                                <span>Сеть</span>
                                <input class="input input-md" v-model="createForm.network" placeholder="tron" />
                            </label>
                            <label class="floating-label">
                                <span>Сумма</span>
                                <input class="input input-md" v-model="createForm.amount" placeholder="12.50" />
                            </label>
                            <label class="floating-label">
                                <span>Внешний ID</span>
                                <input class="input input-md" v-model="createForm.external_invoice_id" placeholder="ORDER-123" />
                            </label>
                            <label class="floating-label">
                                <span>Callback URL</span>
                                <input class="input input-md" v-model="createForm.callback_url" placeholder="https://example.com/callback" />
                            </label>
                            <label class="floating-label">
                                <span>Тег</span>
                                <input class="input input-md" v-model="createForm.tag" placeholder="vip" />
                            </label>
                            <label class="floating-label">
                                <span>Metadata (JSON)</span>
                                <textarea class="textarea textarea-md textarea-bordered" v-model="createForm.metadata" rows="4" placeholder='{"note":"vip"}'></textarea>
                            </label>
                            <div class="card-actions items-center gap-6">
                                <button class="btn btn-primary" @click="createInvoice">Отправить</button>
                            </div>
                        </div>
                        <div>
                            <label class="label"><span class="label-text">Ответ</span></label>
                            <pre class="mockup-code whitespace-pre-wrap"><code>{{ createResult }}</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <div class="collapse collapse-arrow bg-base-100 border">
                <input type="checkbox" />
                <div class="collapse-title text-md font-medium">GET /invoices/{id} — Получить инвойс</div>
                <div class="collapse-content space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="card-body gap-4 p-0">
                            <label class="floating-label">
                                <span>ID инвойса</span>
                                <input class="input input-md" v-model="getByIdForm.id" placeholder="e.g. inv_123" />
                            </label>
                            <div class="card-actions items-center gap-6">
                                <button class="btn btn-primary" @click="getInvoice">Запросить</button>
                            </div>
                        </div>
                        <div>
                            <label class="label"><span class="label-text">Ответ</span></label>
                            <pre class="mockup-code whitespace-pre-wrap"><code>{{ getByIdResult }}</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <div class="collapse collapse-arrow bg-base-100 border">
                <input type="checkbox" />
                <div class="collapse-title text-md font-medium">GET /invoices/{id}/status — Статус инвойса</div>
                <div class="collapse-content space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="card-body gap-4 p-0">
                            <label class="floating-label">
                                <span>ID инвойса</span>
                                <input class="input input-md" v-model="getStatusForm.id" placeholder="e.g. inv_123" />
                            </label>
                            <div class="card-actions items-center gap-6">
                                <button class="btn btn-primary" @click="getStatus">Запросить</button>
                            </div>
                        </div>
                        <div>
                            <label class="label"><span class="label-text">Ответ</span></label>
                            <pre class="mockup-code whitespace-pre-wrap"><code>{{ getStatusResult }}</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <div class="collapse collapse-arrow bg-base-100 border">
                <input type="checkbox" />
                <div class="collapse-title text-md font-medium">GET /invoices/{id}/public — Публичные данные</div>
                <div class="collapse-content space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="card-body gap-4 p-0">
                            <label class="floating-label">
                                <span>ID инвойса</span>
                                <input class="input input-md" v-model="publicForm.id" placeholder="e.g. inv_123" />
                            </label>
                            <div class="card-actions items-center gap-6">
                                <button class="btn btn-primary" @click="getPublic">Запросить</button>
                            </div>
                        </div>
                        <div>
                            <label class="label"><span class="label-text">Ответ</span></label>
                            <pre class="mockup-code whitespace-pre-wrap"><code>{{ publicResult }}</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <div class="collapse collapse-arrow bg-base-100 border">
                <input type="checkbox" />
                <div class="collapse-title text-md font-medium">GET /invoices/{id}/qr — QR адреса</div>
                <div class="collapse-content space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="card-body gap-4 p-0">
                            <label class="floating-label">
                                <span>ID инвойса</span>
                                <input class="input input-md" v-model="qrForm.id" placeholder="e.g. inv_123" />
                            </label>
                            <div class="card-actions items-center gap-6">
                                <button class="btn btn-primary" @click="getQr">Получить QR</button>
                            </div>
                        </div>
                        <div>
                            <label class="label"><span class="label-text">Превью</span></label>
                            <div class="border rounded-box p-3 min-h-32 flex items-center justify-center bg-base-200">
                                <img v-if="qrUrl" :src="qrUrl" alt="QR" class="max-h-64" />
                                <span v-else class="text-base-content/60">Нет данных</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="collapse collapse-arrow bg-base-100 border">
                <input type="checkbox" />
                <div class="collapse-title text-md font-medium">POST /invoices/{id}/cancel — Отменить (expire)</div>
                <div class="collapse-content space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="card-body gap-4 p-0">
                            <label class="floating-label">
                                <span>ID инвойса</span>
                                <input class="input input-md" v-model="cancelForm.id" placeholder="e.g. inv_123" />
                            </label>
                            <div class="card-actions items-center gap-6">
                                <button class="btn btn-warning" @click="cancelInvoice">Отменить</button>
                            </div>
                        </div>
                        <div>
                            <label class="label"><span class="label-text">Ответ</span></label>
                            <pre class="mockup-code whitespace-pre-wrap"><code>{{ cancelResult }}</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Описание -->
            <div class="card bg-base-100 shadow">
                <div class="card-body space-y-2">
                    <h3 class="card-title">Документация</h3>
                    <p class="text-sm">Все запросы должны содержать заголовок <code>X-Api-Key</code> со значением вашего токена. Базовый URL: <code>{{ apiBase }}</code>.</p>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        <li><b>POST /invoices</b>: создать инвойс.</li>
                        <li><b>GET /invoices/{id}</b>: получить инвойс.</li>
                        <li><b>GET /invoices/{id}/status</b>: получить краткий статус.</li>
                        <li><b>GET /invoices/{id}/public</b>: публичные данные (адрес и т.п.).</li>
                        <li><b>GET /invoices/{id}/qr</b>: PNG‑QR адреса.</li>
                        <li><b>POST /invoices/{id}/cancel</b>: отменить активный инвойс.</li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>

</template>




