<script setup lang="ts">
import { computed } from 'vue'

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

const confirmBtnClass = computed(() => props.danger ? 'btn btn-error' : 'btn btn-primary')
</script>

<template>
    <teleport to="body">
        <dialog :open="modelValue" class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg">{{ title }}</h3>
                <p class="py-3 text-base-content/80">{{ message }}</p>
                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" :disabled="loading" @click="onCancel">
                        {{ cancelText }}
                    </button>
                    <button type="button" :class="[confirmBtnClass, loading ? 'btn-disabled' : '']" @click="onConfirm">
                        <span v-if="loading" class="loading loading-spinner loading-sm mr-2" />
                        {{ confirmText }}
                    </button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop" @click="close">
                <button>close</button>
            </form>
        </dialog>
    </teleport>
</template>


