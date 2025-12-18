<script setup lang="ts">
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import FilterPanel from '@/components/filters/FilterPanel.vue'
import Pagination from '@/components/ui/Pagination.vue'
import DateTimeFormat from '@/components/ui/DateTimeFormat.vue'
import ClientModal, { type ClientForm } from '@/components/modals/clients/ClientModal.vue'
import { vueLang } from '@erag/lang-sync-inertia'

interface ClientItem {
    id: string
    external_id: string
    name: string | null
    telegram: string | null
    contact: string | null
    created_at: string | null
}

interface PaginationLinks {
    url: string | null
    label: string
    active: boolean
}

interface PaginatedClients {
    data: ClientItem[]
    links: PaginationLinks[]
}

interface ClientFilters {
    search: string
    has_contact: boolean
}

type FilterFieldType = 'text' | 'select' | 'checkpoint'
type FilterField = {
    key: keyof ClientFilters
    type: FilterFieldType
    label: string
    placeholder?: string
}

interface Props {
    clients: PaginatedClients
    filters?: Partial<ClientFilters>
}

const props = defineProps<Props>()
const { __ } = vueLang()

const showModal = ref(false)
const modalMode = ref<'create' | 'edit'>('create')
const editingId = ref<string | null>(null)

const form = useForm<ClientForm>({
    external_id: '',
    name: '',
    telegram: '',
    contact: '',
})

const filterDefaults: ClientFilters = {
    search: '',
    has_contact: false,
}
const filters = useForm<ClientFilters>({
    ...filterDefaults,
    ...(props.filters ?? {}),
})

const filtersModel = computed({
    get: () => ({
        ...filterDefaults,
        ...filters.data(),
    }),
    set: (value: ClientFilters) => {
        filters.search = value.search ?? ''
        filters.has_contact = Boolean(value.has_contact)
    },
})

const filterFields = computed<FilterField[]>(() => [
    {
        key: 'search',
        type: 'text',
        label: __('frontend.clients.filters.search'),
        placeholder: __('frontend.clients.filters.search_placeholder'),
    },
    {
        key: 'has_contact',
        type: 'checkpoint',
        label: __('frontend.clients.filters.has_contact'),
    },
])

function buildFilterPayload(extra: Record<string, unknown> = {}) {
    const payload = {
        ...filters.data(),
        ...extra,
    }

    const cleaned: Record<string, unknown> = {}

    Object.entries(payload).forEach(([key, value]) => {
        if (key === 'page') {
            cleaned[key] = value
            return
        }

        if (typeof value === 'boolean') {
            if (value) {
                cleaned[key] = 1
            }
            return
        }

        if (value !== undefined && value !== null && String(value).length > 0) {
            cleaned[key] = value
        }
    })

    return cleaned
}

function applyFilters() {
    filters
        .transform(() => buildFilterPayload({ page: 1 }))
        .get('/clients', {
            preserveScroll: true,
            preserveState: true,
            replace: true,
            onFinish: () => filters.transform(data => data),
        })
}

function resetFilters() {
    filters.search = filterDefaults.search
    filters.has_contact = filterDefaults.has_contact

    applyFilters()
}

function openCreate() {
    modalMode.value = 'create'
    editingId.value = null
    form.reset()
    form.clearErrors()
    showModal.value = true
}

function openEdit(client: ClientItem) {
    modalMode.value = 'edit'
    editingId.value = client.id
    form.external_id = client.external_id
    form.name = client.name || ''
    form.telegram = client.telegram || ''
    form.contact = client.contact || ''
    form.clearErrors()
    showModal.value = true
}

function closeModal() {
    showModal.value = false
    form.clearErrors()
}

function updateFormPayload(payload: ClientForm) {
    form.external_id = payload.external_id
    form.name = payload.name
    form.telegram = payload.telegram
    form.contact = payload.contact
}

function submit() {
    if (modalMode.value === 'edit' && editingId.value) {
        form.patch(`/clients/${editingId.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false
            },
            onError: () => {
                showModal.value = true
            },
        })
        return
    }

    form.post('/clients', {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false
            form.reset()
        },
        onError: () => {
            showModal.value = true
        },
    })
}

function toIso(input: string | null | undefined): string {
    if (!input) return ''
    if (typeof input !== 'string') return ''
    if (input.includes('T')) return input
    return `${input.replace(' ', 'T')}Z`
}
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: __('frontend.nav.dashboard'), href: '/' }, { title: __('frontend.nav.clients'), href: '/clients' }]">
        <template #header-actions>
            <div class="flex items-center gap-2">
                <button class="btn btn-primary btn-sm" @click="openCreate">{{ __('frontend.clients.actions.create') }}</button>
            </div>
        </template>

        <div class="grid gap-6">
            <FilterPanel
                v-model="filtersModel"
                :fields="filterFields"
                :title="__('frontend.clients.filters.title')"
                :apply-label="__('frontend.clients.filters.apply')"
                :reset-label="__('frontend.clients.filters.reset')"
                :show-label="__('frontend.clients.filters.show')"
                :hide-label="__('frontend.clients.filters.hide')"
                :any-option-label="__('frontend.clients.filters.any')"
                :loading="filters.processing"
                @apply="applyFilters"
                @reset="resetFilters"
            />

            <div class="lg:card lg:bg-base-100 lg:shadow">
                <div class="lg:card-body">
                    <h2 class="hidden lg:block card-title">{{ __('frontend.clients.list.title') }}</h2>
                    <h2 class="lg:hidden card-title mb-3">{{ __('frontend.clients.list.title') }}</h2>

                    <div class="overflow-x-auto">
                        <table class="table table-sm w-full">
                            <thead>
                                <tr>
                                    <th>{{ __('frontend.common.id') }}</th>
                                    <th>{{ __('frontend.clients.table.external_id') }}</th>
                                    <th>{{ __('frontend.clients.table.name') }}</th>
                                    <th>{{ __('frontend.clients.table.contacts') }}</th>
                                    <th>{{ __('frontend.common.created_at') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="client in clients.data" :key="client.id">
                                    <td class="font-mono text-xs truncate">{{ client.id }}</td>
                                    <td class="font-mono text-xs truncate">{{ client.external_id }}</td>
                                    <td class="text-sm">
                                        <span v-if="client.name">{{ client.name }}</span>
                                        <span v-else class="opacity-60">—</span>
                                    </td>
                                    <td class="text-sm">
                                        <div class="flex flex-col">
                                            <span v-if="client.telegram" class="truncate">@{{ client.telegram.replace('@', '') }}</span>
                                            <span v-if="client.contact" class="truncate text-xs text-base-content/70">{{ client.contact }}</span>
                                            <span v-if="!client.telegram && !client.contact" class="opacity-60">—</span>
                                        </div>
                                    </td>
                                    <td class="text-xs font-mono">
                                        <DateTimeFormat v-if="client.created_at" short-year :value="toIso(client.created_at)" />
                                        <span v-else class="opacity-60">—</span>
                                    </td>
                                    <td class="flex items-center gap-2">
                                        <button class="btn btn-xs" @click="openEdit(client)">
                                            {{ __('frontend.common.edit') }}
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!clients.data.length">
                                    <td colspan="6" class="text-center text-sm opacity-70 py-6">
                                        {{ __('frontend.clients.table.empty') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <Pagination :links="clients.links" />
                </div>
            </div>
        </div>

        <ClientModal
            v-model="showModal"
            :mode="modalMode"
            :form="form"
            :errors="form.errors"
            :loading="form.processing"
            @update:form="updateFormPayload"
            @submit="submit"
            @close="closeModal"
        />
    </AppLayout>
</template>


