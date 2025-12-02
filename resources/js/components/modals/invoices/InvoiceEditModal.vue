<script setup lang="ts">
import { computed } from 'vue'
import ModalDialog from '@/components/ui/modal/ModalDialog.vue'
import Alert from '@/components/ui/Alert.vue'
import FormControl from '@/components/form/FormControl.vue'
import Label from '@/components/form/Label.vue'
import Input from '@/components/form/Input.vue'
import Select from '@/components/form/Select.vue'

interface Option { value: string; label: string }

export interface InvoiceEditForm {
    status: string
    txid: string | null
}

interface Props {
    modelValue: boolean
    form: InvoiceEditForm
    statusOptions: Option[]
    error: string | null
    loading: boolean
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: false,
    error: null,
    loading: false,
    form: () => ({ status: '', txid: null }),
    statusOptions: () => [],
})

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void
    (e: 'update:form', value: InvoiceEditForm): void
    (e: 'submit'): void
    (e: 'close'): void
}>()

const form = computed({
    get: () => props.form,
    set: (value: InvoiceEditForm) => emit('update:form', value),
})

const txidInput = computed({
    get: () => form.value.txid || '',
    set: (value: string) => {
        emit('update:form', {
            ...form.value,
            txid: value.trim() || null,
        })
    },
})

function close() {
    emit('update:modelValue', false)
    emit('close')
}

function submit() {
    const trimmedForm = {
        ...form.value,
        txid: form.value.txid ? form.value.txid.trim() : null,
    }
    emit('update:form', trimmedForm)
    emit('submit')
}
</script>

<template>
    <ModalDialog
        :model-value="modelValue"
        title="Редактировать инвойс"
        description="Сменить статус и при необходимости указать TXID"
        size="xl"
        placement="bottom"
        @update:modelValue="emit('update:modelValue', $event)"
        @close="close"
    >
        <Alert v-if="error" type="error" :message="error" />
        <form class="mt-4 grid gap-4" @submit.prevent="submit">
            <FormControl>
                <Label for="status" required>Статус</Label>
                <Select
                    id="status"
                    v-model="form.status"
                    :options="statusOptions"
                    required
                />
            </FormControl>
            <FormControl v-if="form.status === 'paid'" hint="Обязателен для статуса paid">
                <Label for="txid" required>TXID</Label>
                <Input
                    id="txid"
                    v-model="txidInput"
                    type="text"
                    placeholder="Введите хэш транзакции"
                    required
                />
            </FormControl>

            <div class="modal-action">
                <button type="button" class="btn" @click="close" :disabled="loading">Отмена</button>
                <button type="submit" class="btn btn-primary" :class="{ loading }" :disabled="loading">Сохранить</button>
            </div>
        </form>
    </ModalDialog>
</template>


