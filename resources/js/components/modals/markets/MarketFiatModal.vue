<script setup lang="ts">
import { computed } from 'vue';
import ModalDialog from '@/components/ui/modal/ModalDialog.vue';
import DateTimeFormat from '@/components/ui/DateTimeFormat.vue';
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

interface VisibleFields {
    rows?: boolean;
    pay_types?: boolean;
    polling_interval_seconds?: boolean;
    is_enabled?: boolean;
}

interface Props {
    modelValue: boolean;
    market: string;
    fiatCode: string;
    asset?: string;
    lastPolledAt?: string | null;
    form: MarketFiatForm;
    errors?: MarketFiatFormErrors | null;
    loading?: boolean;
    visibleFields?: VisibleFields;
    note?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: false,
    asset: 'USDT',
    market: 'BINANCE',
    loading: false,
    lastPolledAt: null,
    form: () => ({
        rows: 5,
        pay_types: '',
        polling_interval_seconds: 30,
        is_enabled: true,
    }),
    errors: () => ({} as MarketFiatFormErrors),
    visibleFields: () => ({
        rows: true,
        pay_types: true,
        polling_interval_seconds: true,
        is_enabled: true,
    }),
    note: '',
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

const fieldsVisibility = computed<Required<VisibleFields>>(() => ({
    rows: Boolean(props.visibleFields?.rows),
    pay_types: Boolean(props.visibleFields?.pay_types),
    polling_interval_seconds: Boolean(props.visibleFields?.polling_interval_seconds),
    is_enabled: Boolean(props.visibleFields?.is_enabled),
}));

const hasAnyField = computed(() => Object.values(fieldsVisibility.value).some(Boolean));

function close() {
    emit('update:modelValue', false);
    emit('close');
}

function submit() {
    emit('submit');
}

function toIso(input: string | null | undefined): string {
    if (!input || typeof input !== 'string') return '';
    if (input.includes('T')) return input;
    return `${input.replace(' ', 'T')}Z`;
}
</script>

<template>
    <ModalDialog
        :model-value="modelValue"
        :title="`${__('frontend.markets.actions.settings')} — ${asset}/${fiatCode}`"
        :description="note"
        size="lg"
        @update:modelValue="emit('update:modelValue', $event)"
        @close="close"
    >
        <div class="space-y-4">
            <div v-if="lastPolledAt" class="flex items-center gap-2 text-sm">
                <span class="opacity-70">{{ __('frontend.markets.fields.last_polled_at') }}:</span>
                <DateTimeFormat :value="toIso(lastPolledAt)" />
            </div>

            <p v-if="note" class="text-sm opacity-80 leading-relaxed">
                {{ note }}
            </p>

            <form v-if="hasAnyField" class="grid gap-4" @submit.prevent="submit">
                <FormControl v-if="fieldsVisibility.rows" :error="fieldErrors.rows">
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

                <FormControl v-if="fieldsVisibility.polling_interval_seconds" :error="fieldErrors.polling_interval_seconds">
                    <Label for="fiat-polling">{{ __('frontend.markets.fields.polling_interval_seconds') }}</Label>
                    <Input
                        id="fiat-polling"
                        v-model.number="form.polling_interval_seconds"
                        type="number"
                        min="5"
                        max="3600"
                    />
                </FormControl>

                <FormControl v-if="fieldsVisibility.pay_types" :error="fieldErrors.pay_types">
                    <Label for="fiat-pay-types">{{ __('frontend.markets.fields.pay_types') }}</Label>
                    <Input
                        id="fiat-pay-types"
                        v-model="form.pay_types"
                        type="text"
                        placeholder="BANK, CARD"
                    />
                    <p class="text-xs opacity-70 mt-1">{{ __('frontend.markets.hints.pay_types') }}</p>
                </FormControl>

                <div v-if="fieldsVisibility.is_enabled" class="form-control">
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

            <div v-else class="text-sm opacity-80">
                {{ __('frontend.markets.no_settings') }}
            </div>
        </div>
    </ModalDialog>
</template>

