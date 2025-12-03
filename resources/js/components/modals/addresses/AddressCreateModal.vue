<script setup lang="ts">
import { computed } from 'vue'
import ModalDialog from '@/components/ui/modal/ModalDialog.vue'
import Alert from '@/components/ui/Alert.vue'
import FormControl from '@/components/form/FormControl.vue'
import Label from '@/components/form/Label.vue'
import Input from '@/components/form/Input.vue'
import Select from '@/components/form/Select.vue'

interface Option { value: string; label: string }

export interface AddressCreateForm {
    currency: string
    network: string
    address: string
}

interface Props {
    modelValue: boolean
    form: AddressCreateForm
    currencyOptions: Option[]
    networkOptions: Option[]
    error: string | null
    loading: boolean
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: false,
    error: null,
    loading: false,
    form: () => ({
        currency: '',
        network: '',
        address: '',
    }),
    currencyOptions: () => [],
    networkOptions: () => [],
})

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void
    (e: 'update:form', value: AddressCreateForm): void
    (e: 'submit'): void
    (e: 'close'): void
}>()

const form = computed({
    get: () => props.form,
    set: (value: AddressCreateForm) => emit('update:form', value),
})

function close() {
    emit('update:modelValue', false)
    emit('close')
}

function submit() {
    const trimmedForm = {
        ...form.value,
        address: form.value.address.trim(),
    }
    emit('update:form', trimmedForm)
    emit('submit')
}
</script>

<template>
    <ModalDialog
        :model-value="modelValue"
        title="Добавить новый адрес"
        description="Заполните данные для добавления нового адреса"
        size="xl"
        placement="bottom"
        @update:modelValue="emit('update:modelValue', $event)"
        @close="close"
    >
        <Alert v-if="error" type="error" :message="error" />
        <form class="mt-4 grid gap-4" @submit.prevent="submit">
            <FormControl>
                <Label for="currency" required>Валюта</Label>
                <Select
                    id="currency"
                    v-model="form.currency"
                    :options="currencyOptions"
                    placeholder="Выберите валюту"
                    required
                />
            </FormControl>
            <FormControl>
                <Label for="network" required>Сеть</Label>
                <Select
                    id="network"
                    v-model="form.network"
                    :options="networkOptions"
                    placeholder="Выберите сеть"
                    required
                />
            </FormControl>
            <FormControl>
                <Label for="address" required>Адрес</Label>
                <Input
                    id="address"
                    v-model="form.address"
                    type="text"
                    placeholder="Например: T..."
                    required
                />
            </FormControl>

            <div class="modal-action">
                <button type="button" class="btn" @click="close" :disabled="loading">Отмена</button>
                <button type="submit" class="btn btn-primary" :disabled="loading">
                    <span v-if="loading" class="loading loading-spinner loading-sm mr-2" />
                    Добавить
                </button>
            </div>
        </form>
    </ModalDialog>
</template>

