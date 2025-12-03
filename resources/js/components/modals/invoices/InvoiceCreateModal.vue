<script setup lang="ts">
import { computed } from 'vue'
import ModalDialog from '@/components/ui/modal/ModalDialog.vue'
import FormControl from '@/components/form/FormControl.vue'
import Label from '@/components/form/Label.vue'
import Input from '@/components/form/Input.vue'
import Select from '@/components/form/Select.vue'
import Textarea from '@/components/form/Textarea.vue'

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
        title="Создать инвойс"
        description="Заполните данные для выставления инвойса"
        size="xl"
        placement="bottom"
        @update:modelValue="emit('update:modelValue', $event)"
        @close="close"
    >
        <form class="mt-4 grid gap-4" @submit.prevent="submit">
            <FormControl hint="Напр.: BTC, ETH, USDT" :error="fieldErrors.currency">
                <Label for="currency" required>Валюта</Label>
                <Select
                    id="currency"
                    v-model="form.currency"
                    :options="currencyOptions"
                    placeholder="Выберите валюту"
                    required
                />
            </FormControl>
            <FormControl hint="Выберите сеть для валюты" :error="fieldErrors.network">
                <Label for="network" required>Сеть</Label>
                <Select
                    id="network"
                    v-model="form.network"
                    :options="networkOptions"
                    placeholder="Выберите сеть"
                    required
                />
            </FormControl>
            <FormControl hint="Десятичный формат" :error="fieldErrors.amount">
                <Label for="amount" required>Сумма</Label>
                <Input
                    id="amount"
                    v-model="form.amount"
                    type="text"
                    placeholder="Например: 12.34"
                    required
                />
            </FormControl>
            <FormControl :error="fieldErrors.external_invoice_id">
                <Label for="external_invoice_id">Внешний ID (опц.)</Label>
                <Input
                    id="external_invoice_id"
                    v-model="form.external_invoice_id"
                    type="text"
                />
            </FormControl>
            <FormControl :error="fieldErrors.callback_url">
                <Label for="callback_url">Callback URL (опц.)</Label>
                <Input
                    id="callback_url"
                    v-model="form.callback_url"
                    type="url"
                />
            </FormControl>
            <FormControl :error="fieldErrors.tag">
                <Label for="tag">Тег (опц.)</Label>
                <Input
                    id="tag"
                    v-model="form.tag"
                    type="text"
                />
            </FormControl>
            <FormControl :error="fieldErrors.metadata">
                <Label for="metadata">Metadata (JSON, опц.)</Label>
                <Textarea
                    id="metadata"
                    v-model="form.metadata"
                    placeholder='{"key":"value"}'
                    :rows="4"
                />
            </FormControl>
        </form>

        <template #actions>
            <button type="button" class="btn" @click="close" :disabled="loading">Отмена</button>
            <button type="button" class="btn btn-primary" :class="{ loading }" :disabled="loading" @click="submit">Создать</button>
        </template>
    </ModalDialog>
</template>


