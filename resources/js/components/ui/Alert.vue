<script setup lang="ts">
interface Props {
    type?: 'error' | 'success' | 'info' | 'warning';
    message?: string;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'info',
});

const alertClass = {
    error: 'alert-error',
    success: 'alert-success',
    info: 'alert-info',
    warning: 'alert-warning',
};
</script>

<template>
    <div v-if="message" :class="['alert', alertClass[props.type]]">
        <span>{{ message }}</span>
    </div>
</template>



