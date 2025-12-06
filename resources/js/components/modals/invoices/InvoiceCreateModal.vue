<script setup lang="ts">
import { computed } from 'vue'
import ModalDialog from '@/components/ui/modal/ModalDialog.vue'
import FormControl from '@/components/form/FormControl.vue'
import Label from '@/components/form/Label.vue'
import Input from '@/components/form/Input.vue'
import Select from '@/components/form/Select.vue'
import Textarea from '@/components/form/Textarea.vue'
import { vueLang } from '@erag/lang-sync-inertia'

interface Option { value: string; label: string }

export interface InvoiceCreateForm {
    currency: string
    network: string
    amount: string
    external_invoice_id: string
    callback_url: string
    tag: string
    metadata: string
}

type InvoiceCreateErrors = Partial<Record<keyof InvoiceCreateForm, string>>

interface Props {
    modelValue: boolean
    form: InvoiceCreateForm
    currencyOptions: Option[]
    networkOptions: Option[]
    errors: InvoiceCreateErrors | null
    loading: boolean
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: false,
    loading: false,
    form: () => ({
        currency: '',
        network: '',
        amount: '',
        external_invoice_id: '',
        callback_url: '',
        tag: '',
        metadata: '',
    }),
    currencyOptions: () => [],
    networkOptions: () => [],
    errors: () => ({} as InvoiceCreateErrors),
})

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void
    (e: 'update:form', value: InvoiceCreateForm): void
    (e: 'submit'): void
    (e: 'close'): void
}>()

const form = computed({
    get: () => props.form,
    set: (value: InvoiceCreateForm) => emit('update:form', value),
})

const fieldErrors = computed<InvoiceCreateErrors>(() => props.errors ?? {})

const { __ } = vueLang()

function close() {
    emit('update:modelValue', false)
    emit('close')
}

function submit() {
    const trimmedForm = {
        ...form.value,
        amount: form.value.amount.trim(),
        external_invoice_id: form.value.external_invoice_id.trim(),
        callback_url: form.value.callback_url.trim(),
        tag: form.value.tag.trim(),
    }
    emit('update:form', trimmedForm)
    emit('submit')
}
</script>

<template>
    <ModalDialog
        :model-value="modelValue"
        :title="__('frontend.invoices.modals.create.title')"
        :description="__('frontend.invoices.modals.create.description')"
        size="xl"
        placement="bottom"
        @update:modelValue="emit('update:modelValue', $event)"
        @close="close"
    >
        <form class="mt-4 grid gap-4" @submit.prevent="submit">
            <FormControl :hint="__('frontend.invoices.fields.currency_hint')" :error="fieldErrors.currency">
                <Label for="currency" required>{{ __('frontend.invoices.fields.currency') }}</Label>
                <Select
                    id="currency"
                    v-model="form.currency"
                    :options="currencyOptions"
                    :placeholder="__('frontend.invoices.fields.currency_placeholder')"
                    required
                />
            </FormControl>
            <FormControl :hint="__('frontend.invoices.fields.network_hint')" :error="fieldErrors.network">
                <Label for="network" required>{{ __('frontend.invoices.fields.network') }}</Label>
                <Select
                    id="network"
                    v-model="form.network"
                    :options="networkOptions"
                    :placeholder="__('frontend.invoices.fields.network_placeholder')"
                    required
                />
            </FormControl>
            <FormControl :hint="__('frontend.invoices.fields.amount_hint')" :error="fieldErrors.amount">
                <Label for="amount" required>{{ __('frontend.invoices.fields.amount') }}</Label>
                <Input
                    id="amount"
                    v-model="form.amount"
                    type="text"
                    :placeholder="__('frontend.invoices.fields.amount_placeholder')"
                    required
                />
            </FormControl>
            <FormControl :error="fieldErrors.external_invoice_id">
                <Label for="external_invoice_id">{{ __('frontend.invoices.fields.external_id') }}</Label>
                <Input
                    id="external_invoice_id"
                    v-model="form.external_invoice_id"
                    type="text"
                />
            </FormControl>
            <FormControl :error="fieldErrors.callback_url">
                <Label for="callback_url">{{ __('frontend.invoices.fields.callback_url') }}</Label>
                <Input
                    id="callback_url"
                    v-model="form.callback_url"
                    type="url"
                />
            </FormControl>
            <FormControl :error="fieldErrors.tag">
                <Label for="tag">{{ __('frontend.invoices.fields.tag') }}</Label>
                <Input
                    id="tag"
                    v-model="form.tag"
                    type="text"
                />
            </FormControl>
            <FormControl :error="fieldErrors.metadata">
                <Label for="metadata">{{ __('frontend.invoices.fields.metadata') }}</Label>
                <Textarea
                    id="metadata"
                    v-model="form.metadata"
                    :placeholder="__('frontend.invoices.fields.metadata_placeholder')"
                    :rows="4"
                />
            </FormControl>
        </form>

        <template #actions>
            <button type="button" class="btn" @click="close" :disabled="loading">{{ __('frontend.common.cancel') }}</button>
            <button type="button" class="btn btn-primary" :class="{ loading }" :disabled="loading" @click="submit">
                {{ __('frontend.invoices.actions.create') }}
            </button>
        </template>
    </ModalDialog>
</template>


