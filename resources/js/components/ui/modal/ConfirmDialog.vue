<script setup lang="ts">
import { computed } from 'vue'
import ModalDialog from '@/components/ui/modal/ModalDialog.vue'

interface Props {
    modelValue: boolean
    title?: string
    message?: string
    confirmText?: string
    cancelText?: string
    danger?: boolean
    loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: false,
    title: 'Подтверждение',
    message: 'Вы уверены, что хотите выполнить это действие?',
    confirmText: 'Подтвердить',
    cancelText: 'Отмена',
    danger: false,
    loading: false,
})

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void
    (e: 'confirm'): void
    (e: 'cancel'): void
}>()

const confirmBtnClass = computed(() => props.danger ? 'btn btn-error' : 'btn btn-primary')

function close() {
    emit('update:modelValue', false)
}

function onCancel() {
    emit('cancel')
    close()
}

function onConfirm() {
    emit('confirm')
}
</script>

<template>
    <ModalDialog
        :model-value="modelValue"
        :title="title"
        :description="message"
        size="sm"
        @update:modelValue="emit('update:modelValue', $event)"
    >
        <template #actions>
            <button type="button" class="btn btn-ghost" :disabled="loading" @click="onCancel">
                {{ cancelText }}
            </button>
            <button type="button" :class="[confirmBtnClass, loading ? 'btn-disabled' : '']" @click="onConfirm">
                <span v-if="loading" class="loading loading-spinner loading-sm mr-2" />
                {{ confirmText }}
            </button>
        </template>
    </ModalDialog>
</template>


