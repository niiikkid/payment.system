<script setup lang="ts">
import { reactive, ref } from 'vue';

interface Props {
    apiKey: string;
    apiBase: string;
}

const props = defineProps<Props>();

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
        headers: { 'X-Api-Key': props.apiKey ?? '' },
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
    <div class="space-y-4">
        <div class="collapse collapse-arrow bg-base-100 border">
            <input type="checkbox" />
            <div class="collapse-title text-md font-medium">POST /invoices — Создать инвойс</div>
            <div class="collapse-content space-y-4">
                <p class="text-sm text-base-content/70">Создаёт инвойс и возвращает объект. Требует: currency, network, amount. Опционально: external_invoice_id, callback_url, tag, metadata.</p>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="card-body gap-4 p-0 pt-3 w-full">
                        <label class="floating-label">
                            <span>Валюта</span>
                            <input class="input input-md w-full" v-model="createForm.currency" placeholder="USDT" />
                        </label>
                        <label class="floating-label">
                            <span>Сеть</span>
                            <input class="input input-md w-full" v-model="createForm.network" placeholder="tron" />
                        </label>
                        <label class="floating-label">
                            <span>Сумма</span>
                            <input class="input input-md w-full" v-model="createForm.amount" placeholder="12.50" />
                        </label>
                        <label class="floating-label">
                            <span>Внешний ID</span>
                            <input class="input input-md w-full" v-model="createForm.external_invoice_id" placeholder="ORDER-123" />
                        </label>
                        <label class="floating-label">
                            <span>Callback URL</span>
                            <input class="input input-md w-full" v-model="createForm.callback_url" placeholder="https://example.com/callback" />
                        </label>
                        <label class="floating-label">
                            <span>Тег</span>
                            <input class="input input-md w-full" v-model="createForm.tag" placeholder="vip" />
                        </label>
                        <label class="floating-label">
                            <span>Metadata (JSON)</span>
                            <textarea class="textarea textarea-md textarea-bordered w-full" v-model="createForm.metadata" rows="4" placeholder='{"note":"vip"}'></textarea>
                        </label>
                        <div class="card-actions items-center gap-6">
                            <button class="btn btn-primary" @click="createInvoice">Отправить</button>
                        </div>
                    </div>
                    <div class="min-w-0 w-full sm:col-span-1 md:col-span-2">
                        <label class="label"><span class="label-text">Ответ</span></label>
                        <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full"><code class="block">{{ createResult }}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100 border">
            <input type="checkbox" />
            <div class="collapse-title text-md font-medium">GET /invoices/{id} — Получить инвойс</div>
            <div class="collapse-content space-y-4">
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="card-body gap-4 p-0 pt-3 w-full">
                        <label class="floating-label">
                            <span>ID инвойса</span>
                            <input class="input input-md w-full" v-model="getByIdForm.id" placeholder="e.g. inv_123" />
                        </label>
                        <div class="card-actions items-center gap-6">
                            <button class="btn btn-primary" @click="getInvoice">Запросить</button>
                        </div>
                    </div>
                    <div class="min-w-0 w-full sm:col-span-1 md:col-span-2">
                        <label class="label"><span class="label-text">Ответ</span></label>
                        <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full"><code class="block">{{ getByIdResult }}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100 border">
            <input type="checkbox" />
            <div class="collapse-title text-md font-medium">GET /invoices/{id}/status — Статус инвойса</div>
            <div class="collapse-content space-y-4">
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="card-body gap-4 p-0 pt-3 w-full">
                        <label class="floating-label">
                            <span>ID инвойса</span>
                            <input class="input input-md w-full" v-model="getStatusForm.id" placeholder="e.g. inv_123" />
                        </label>
                        <div class="card-actions items-center gap-6">
                            <button class="btn btn-primary" @click="getStatus">Запросить</button>
                        </div>
                    </div>
                    <div class="min-w-0 w-full sm:col-span-1 md:col-span-2">
                        <label class="label"><span class="label-text">Ответ</span></label>
                        <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full"><code class="block">{{ getStatusResult }}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100 border">
            <input type="checkbox" />
            <div class="collapse-title text-md font-medium">GET /invoices/{id}/public — Публичные данные</div>
            <div class="collapse-content space-y-4">
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="card-body gap-4 p-0 pt-3 w-full">
                        <label class="floating-label">
                            <span>ID инвойса</span>
                            <input class="input input-md w-full" v-model="publicForm.id" placeholder="e.g. inv_123" />
                        </label>
                        <div class="card-actions items-center gap-6">
                            <button class="btn btn-primary" @click="getPublic">Запросить</button>
                        </div>
                    </div>
                    <div class="min-w-0 w-full sm:col-span-1 md:col-span-2">
                        <label class="label"><span class="label-text">Ответ</span></label>
                        <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full"><code class="block">{{ publicResult }}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100 border">
            <input type="checkbox" />
            <div class="collapse-title text-md font-medium">GET /invoices/{id}/qr — QR адреса</div>
            <div class="collapse-content space-y-4">
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="card-body gap-4 p-0 pt-3 w-full">
                        <label class="floating-label">
                            <span>ID инвойса</span>
                            <input class="input input-md w-full" v-model="qrForm.id" placeholder="e.g. inv_123" />
                        </label>
                        <div class="card-actions items-center gap-6">
                            <button class="btn btn-primary" @click="getQr">Получить QR</button>
                        </div>
                    </div>
                    <div class="min-w-0 w-full sm:col-span-1 md:col-span-2">
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
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="card-body gap-4 p-0 pt-3 w-full">
                        <label class="floating-label">
                            <span>ID инвойса</span>
                            <input class="input input-md w-full" v-model="cancelForm.id" placeholder="e.g. inv_123" />
                        </label>
                        <div class="card-actions items-center gap-6">
                            <button class="btn btn-warning" @click="cancelInvoice">Отменить</button>
                        </div>
                    </div>
                    <div class="min-w-0 w-full sm:col-span-1 md:col-span-2">
                        <label class="label"><span class="label-text">Ответ</span></label>
                        <pre class="mockup-code whitespace-pre overflow-x-auto max-w-full w-full"><code class="block">{{ cancelResult }}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

