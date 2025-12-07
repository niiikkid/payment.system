<script setup lang="ts">
import { computed } from 'vue';
import ModalDialog from '@/components/ui/modal/ModalDialog.vue';
import FormControl from '@/components/form/FormControl.vue';
import Label from '@/components/form/Label.vue';
import Input from '@/components/form/Input.vue';
import { vueLang } from '@erag/lang-sync-inertia';

export interface MarketFiatForm {
    rows: number;
    pay_types: string;
    polling_interval_seconds: number;
    is_enabled: boolean;
}

type MarketFiatFormErrors = Partial<Record<keyof MarketFiatForm, string>>;

interface Props {
    modelValue: boolean;
    fiatCode: string;
    lastPolledAt?: string | null;
    form: MarketFiatForm;
    errors?: MarketFiatFormErrors | null;
    loading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: false,
    loading: false,
    lastPolledAt: null,
    form: () => ({
        rows: 5,
        pay_types: '',
        polling_interval_seconds: 30,
        is_enabled: true,
    }),
    errors: () => ({} as MarketFiatFormErrors),
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
    (e: 'update:form', value: MarketFiatForm): void;
    (e: 'submit'): void;
    (e: 'close'): void;
}>();

const form = computed({
    get: () => props.form,
    set: (value: MarketFiatForm) => emit('update:form', value),
});

const { __ } = vueLang();
const fieldErrors = computed<MarketFiatFormErrors>(() => props.errors ?? {});

const modalDescription = computed(() =>
    props.lastPolledAt
        ? `${__('frontend.markets.fields.last_polled_at')}: ${props.lastPolledAt}`
        : ''
);

function close() {
    emit('update:modelValue', false);
    emit('close');
}

function submit() {
    emit('submit');
}
</script>

<template>
    <ModalDialog
        :model-value="modelValue"
        :title="`${__('frontend.markets.actions.settings')} — USDT/${fiatCode}`"
        :description="modalDescription"
        size="lg"
        @update:modelValue="emit('update:modelValue', $event)"
        @close="close"
    >
        <form class="grid gap-4" @submit.prevent="submit">
            <FormControl :error="fieldErrors.rows">
                <Label for="fiat-rows">{{ __('frontend.markets.fields.rows') }}</Label>
                <Input
                    id="fiat-rows"
                    v-model.number="form.rows"
                    type="number"
                    min="1"
                    max="50"
                />
                <p class="text-xs opacity-70 mt-1">{{ __('frontend.markets.hints.rows') }}</p>
            </FormControl>

            <FormControl :error="fieldErrors.polling_interval_seconds">
                <Label for="fiat-polling">{{ __('frontend.markets.fields.polling_interval_seconds') }}</Label>
                <Input
                    id="fiat-polling"
                    v-model.number="form.polling_interval_seconds"
                    type="number"
                    min="5"
                    max="3600"
                />
            </FormControl>

            <FormControl :error="fieldErrors.pay_types">
                <Label for="fiat-pay-types">{{ __('frontend.markets.fields.pay_types') }}</Label>
                <Input
                    id="fiat-pay-types"
                    v-model="form.pay_types"
                    type="text"
                    placeholder="BANK, CARD"
                />
                <p class="text-xs opacity-70 mt-1">{{ __('frontend.markets.hints.pay_types') }}</p>
            </FormControl>

            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-3">
                    <input v-model="form.is_enabled" type="checkbox" class="toggle toggle-primary toggle-sm" />
                    <span class="label-text">{{ __('frontend.markets.fields.is_enabled') }}</span>
                </label>
            </div>

            <div class="modal-action col-span-full">
                <button class="btn btn-ghost" type="button" @click="close">
                    {{ __('frontend.common.cancel') }}
                </button>
                <button
                    class="btn btn-primary"
                    type="submit"
                    :disabled="loading"
                >
                    <span v-if="loading" class="loading loading-spinner loading-xs mr-2" />
                    {{ __('frontend.markets.actions.save') }}
                </button>
            </div>
        </form>
    </ModalDialog>
</template>

