<script setup lang="ts">
interface Props {
    id?: string;
    modelValue: string | number;
    type?: string;
    placeholder?: string;
    required?: boolean;
    disabled?: boolean;
    autofocus?: boolean;
    autocomplete?: string;
    name?: string;
    step?: string | number;
    min?: string | number;
    max?: string | number;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'text',
});

const emit = defineEmits<{
    'update:modelValue': [value: string | number];
}>();

function handleInput(event: Event) {
    const target = event.target as HTMLInputElement;
    emit('update:modelValue', props.type === 'number' ? Number(target.value) : target.value);
}
</script>

<template>
    <input
        :id="props.id"
        :type="props.type"
        :name="props.name"
        :value="props.modelValue"
        :placeholder="props.placeholder"
        :required="props.required"
        :disabled="props.disabled"
        :autofocus="props.autofocus"
        :autocomplete="props.autocomplete"
        :step="props.step"
        :min="props.min"
        :max="props.max"
        class="input input-bordered"
        @input="handleInput"
    />
</template>

