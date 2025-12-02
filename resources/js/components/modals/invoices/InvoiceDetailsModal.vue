<script setup lang="ts">
import ModalDialog from '@/components/ui/modal/ModalDialog.vue'
import CurrencyNetworkBadge from '@/components/ui/CurrencyNetworkBadge.vue'
import AddressCopy from '@/components/ui/AddressCopy.vue'
import UidCopy from '@/components/ui/UidCopy.vue'
import DateTimeFormat from '@/components/ui/DateTimeFormat.vue'
import Alert from '@/components/ui/Alert.vue'
import TxidCopy from '@/components/ui/TxidCopy.vue';

type Invoice = {
    id: string
    external_invoice_id: string | null
    address_id: number
    address?: string | null
    amount: number
    currency: string
    currency_label?: string | null
    network: string
    network_label?: string | null
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
}

interface StatusGroups {
    active: string[]
    final: string[]
}

interface Props {
    modelValue: boolean
    invoice: Invoice | null
    statuses: StatusGroups
    sendLoading: boolean
    sendError: string | null
    sendSuccess: boolean
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: false,
    statuses: () => ({ active: [], final: [] }),
    sendLoading: false,
    sendError: null,
    sendSuccess: false,
})

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void
    (e: 'edit'): void
    (e: 'send-callback'): void
    (e: 'close'): void
}>()

function close() {
    emit('update:modelValue', false)
    emit('close')
}

function toIso(input: string | null | undefined): string {
    if (!input) return ''
    if (typeof input !== 'string') return ''
    if (input.includes('T')) return input
    return `${input.replace(' ', 'T')}Z`
}
</script>

<template>
    <ModalDialog
        :model-value="modelValue"
        title="Детали инвойса"
        description="Полная информация по выбранному инвойсу"
        size="3xl"
        placement="bottom"
        @update:modelValue="emit('update:modelValue', $event)"
        @close="close"
    >
        <template v-if="invoice">
            <div class="grid gap-4">
                <div class="grid gap-3 grid-cols-4">
                    <div class="card bg-base-200 shadow-sm">
                        <div class="card-body p-4">
                            <div class="text-xs opacity-60">ID</div>
                            <div class="font-mono break-all">
                                <UidCopy v-if="invoice.id" :uid="invoice.id" />
                            </div>
                        </div>
                    </div>
                    <div class="card bg-base-200 shadow-sm">
                        <div class="card-body p-4">
                            <div class="text-xs opacity-60">Адрес</div>
                            <div class="font-mono">
                                <AddressCopy v-if="invoice.address" :address="invoice.address" />
                                <span v-else class="opacity-60">#{{ invoice.address_id }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card bg-base-200 shadow-sm">
                        <div class="card-body p-4">
                            <div class="text-xs opacity-60">Сумма</div>
                            <div class="flex items-center gap-2">
                                <div class="font-mono">{{ invoice.amount }}</div>
                                <CurrencyNetworkBadge
                                    :currency-label="invoice.currency_label || invoice.currency"
                                    :network-label="invoice.network_label || invoice.network"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="card bg-base-200 shadow-sm">
                        <div class="card-body p-4">
                            <div class="text-xs opacity-60">Статус</div>
                            <div>
                                <span
                                    class="badge"
                                    :class="{
                                        'badge-warning': statuses.active.includes(invoice.status),
                                        'badge-success': invoice.status === 'paid',
                                        'badge-error': statuses.final.includes(invoice.status) && invoice.status !== 'paid',
                                    }"
                                >{{ invoice.status }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="divider my-0"></div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="grid gap-2">
                        <div class="text-xs opacity-60">TXID</div>
                        <div class="flex items-center gap-2" v-if="invoice.txid">
                            <span class="font-mono break-all">
                                <TxidCopy :txid="invoice.txid"/>
                            </span>
                            <a
                                v-if="invoice.status === 'paid' && invoice.tx_explorer_url"
                                :href="invoice.tx_explorer_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="link link-primary"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                            </a>
                        </div>
                        <div class="opacity-60" v-else>—</div>
                    </div>
                    <div class="grid gap-2">
                        <div class="text-xs opacity-60">Получено / Подтв.</div>
                        <div class="font-mono">{{ invoice.amount_received }} / {{ invoice.confirmations }}</div>
                    </div>
                    <div class="grid gap-2">
                        <div class="text-xs opacity-60">Истекает</div>
                        <div>
                            <template v-if="invoice.expires_at">
                                <DateTimeFormat :value="toIso(invoice.expires_at)" />
                            </template>
                            <template v-else>—</template>
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <div class="text-xs opacity-60">Создан</div>
                        <div>
                            <DateTimeFormat :value="toIso(invoice.created_at)" />
                        </div>
                    </div>
                </div>

                <div class="divider my-0"></div>

                <div class="grid gap-4 md:grid-cols-1">
                    <div class="grid gap-3">
                        <div class="text-xs opacity-60">Callback URL</div>
                        <div>
                            <template v-if="invoice.callback_url">
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-outline">POST</span>
                                    <span class="break-all font-mono">{{ invoice.callback_url }}</span>
                                </div>
                            </template>
                            <template v-else>—</template>
                        </div>
                    </div>
                    <div class="grid gap-3">
                        <div class="text-xs opacity-60">Метаданные</div>
                        <div class="mockup-code">
                            <pre><code>{{ JSON.stringify(invoice.metadata || {}, null, 2) }}</code></pre>
                        </div>
                    </div>
                </div>
                <Alert v-if="sendError" type="error" :message="sendError" />
                <Alert v-if="sendSuccess" type="success" message="Колбэк отправлен" />
            </div>
        </template>

        <template #actions>
            <button class="btn" :disabled="!invoice" @click="emit('edit')">
                Редактировать
            </button>
            <button
                v-if="invoice?.callback_url"
                class="btn btn-primary"
                :class="{ loading: sendLoading }"
                :disabled="sendLoading"
                @click="emit('send-callback')"
            >
                Отправить колбэк
            </button>
            <button class="btn btn-ghost" @click="close">
                Закрыть
            </button>
        </template>
    </ModalDialog>
</template>


