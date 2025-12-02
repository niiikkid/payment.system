<script setup lang="ts">
import { computed } from 'vue'

type ModalSize = 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '3xl'

interface Props {
    modelValue: boolean
    title?: string
    description?: string
    size?: ModalSize
    placement?: 'center' | 'bottom'
    closable?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: false,
    title: '',
    description: '',
    size: 'md',
    placement: 'center',
    closable: true,
})

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void
    (e: 'close'): void
}>()

const sizeClassMap: Record<ModalSize, string> = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
    '3xl': 'max-w-3xl',
}

const modalBoxClass = computed(() => sizeClassMap[props.size])
const modalPlacementClass = computed(() => props.placement === 'bottom' ? 'modal modal-bottom md:modal-middle' : 'modal')

function close() {
    if (!props.closable) return
    emit('update:modelValue', false)
    emit('close')
}
</script>

<template>
    <teleport to="body">
        <dialog :open="modelValue" :class="modalPlacementClass">
            <div class="modal-box w-full" :class="modalBoxClass">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <h3 v-if="title" class="font-bold text-lg">{{ title }}</h3>
                        <p v-if="description" class="text-sm text-base-content/70">
                            {{ description }}
                        </p>
                    </div>
                    <button
                        v-if="closable"
                        type="button"
                        class="btn btn-sm btn-circle btn-ghost"
                        @click="close"
                    >✕</button>
                </div>

                <div class="mt-4">
                    <slot />
                </div>

                <div v-if="$slots.actions" class="modal-action">
                    <slot name="actions" />
                </div>
            </div>
            <form method="dialog" class="modal-backdrop" @click.prevent="close">
                <button>close</button>
            </form>
        </dialog>
    </teleport>
</template>


