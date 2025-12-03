<script setup lang="ts">
import { computed, ref, onUnmounted, watch } from 'vue';

interface Props {
    apiKey: string;
    apiBase: string;
}

const props = defineProps<Props>();

const truncatedKey = computed(() => {
    const src = (props.apiKey ?? '').trim();
    if (src.length <= 20) return src;
    const start = src.slice(0, 10);
    const end = src.slice(-10);
    return `${start}...${end}`;
});

const truncatedBase = computed(() => {
    const src = (props.apiBase ?? '').trim();
    if (src.length <= 30) return src;
    const start = src.slice(0, 15);
    const end = src.slice(-15);
    return `${start}...${end}`;
});

// Tooltip state for API Key
const tooltipTextKey = ref('Скопировать');
const showTooltipKey = ref(false);
let resetTimerKey: number | undefined;
let tooltipElementKey = ref<HTMLElement | null>(null);
let triggerElementKey = ref<HTMLElement | null>(null);

// Tooltip state for API Base
const tooltipTextBase = ref('Скопировать');
const showTooltipBase = ref(false);
let resetTimerBase: number | undefined;
let tooltipElementBase = ref<HTMLElement | null>(null);
let triggerElementBase = ref<HTMLElement | null>(null);

function updateTooltipPosition(element: HTMLElement | null, trigger: HTMLElement | null) {
    if (!element || !trigger || (!showTooltipKey.value && !showTooltipBase.value)) return;

    const triggerRect = trigger.getBoundingClientRect();
    const tooltipRect = element.getBoundingClientRect();

    let top = triggerRect.top - tooltipRect.height - 8;
    let left = triggerRect.left + (triggerRect.width / 2) - (tooltipRect.width / 2);

    if (top < 0) {
        top = triggerRect.bottom + 8;
    }

    if (left < 0) {
        left = 8;
    } else if (left + tooltipRect.width > window.innerWidth) {
        left = window.innerWidth - tooltipRect.width - 8;
    }

    element.style.top = `${top}px`;
    element.style.left = `${left}px`;
}

function handleScrollKey() {
    if (showTooltipKey.value) {
        updateTooltipPosition(tooltipElementKey.value, triggerElementKey.value);
    }
}

function handleResizeKey() {
    if (showTooltipKey.value) {
        updateTooltipPosition(tooltipElementKey.value, triggerElementKey.value);
    }
}

function handleScrollBase() {
    if (showTooltipBase.value) {
        updateTooltipPosition(tooltipElementBase.value, triggerElementBase.value);
    }
}

function handleResizeBase() {
    if (showTooltipBase.value) {
        updateTooltipPosition(tooltipElementBase.value, triggerElementBase.value);
    }
}

function showTooltipHandler(type: 'key' | 'base') {
    if (type === 'key') {
        showTooltipKey.value = true;
        requestAnimationFrame(() => {
            updateTooltipPosition(tooltipElementKey.value, triggerElementKey.value);
        });
    } else {
        showTooltipBase.value = true;
        requestAnimationFrame(() => {
            updateTooltipPosition(tooltipElementBase.value, triggerElementBase.value);
        });
    }
}

function hideTooltipHandler(type: 'key' | 'base') {
    if (type === 'key') {
        showTooltipKey.value = false;
    } else {
        showTooltipBase.value = false;
    }
}

watch(showTooltipKey, (newValue) => {
    if (newValue) {
        window.addEventListener('scroll', handleScrollKey, true);
        window.addEventListener('resize', handleResizeKey);
    } else {
        window.removeEventListener('scroll', handleScrollKey, true);
        window.removeEventListener('resize', handleResizeKey);
    }
});

watch(showTooltipBase, (newValue) => {
    if (newValue) {
        window.addEventListener('scroll', handleScrollBase, true);
        window.addEventListener('resize', handleResizeBase);
    } else {
        window.removeEventListener('scroll', handleScrollBase, true);
        window.removeEventListener('resize', handleResizeBase);
    }
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScrollKey, true);
    window.removeEventListener('resize', handleResizeKey);
    window.removeEventListener('scroll', handleScrollBase, true);
    window.removeEventListener('resize', handleResizeBase);
    if (resetTimerKey) clearTimeout(resetTimerKey);
    if (resetTimerBase) clearTimeout(resetTimerBase);
});

async function copyToClipboard(text: string, type: 'key' | 'base') {
    if (!text) return;
    try {
        await navigator.clipboard.writeText(text);
        if (type === 'key') {
            tooltipTextKey.value = 'Скопировано';
        } else {
            tooltipTextBase.value = 'Скопировано';
        }
    } catch (_) {
        if (type === 'key') {
            tooltipTextKey.value = 'Не удалось скопировать';
        } else {
            tooltipTextBase.value = 'Не удалось скопировать';
        }
    } finally {
        const timer = type === 'key' ? resetTimerKey : resetTimerBase;
        if (timer) clearTimeout(timer);
        const newTimer = window.setTimeout(() => {
            if (type === 'key') {
                tooltipTextKey.value = 'Скопировать';
            } else {
                tooltipTextBase.value = 'Скопировать';
            }
        }, 1500);
        if (type === 'key') {
            resetTimerKey = newTimer;
        } else {
            resetTimerBase = newTimer;
        }
    }
}

function onKeydown(e: KeyboardEvent, type: 'key' | 'base') {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        const text = type === 'key' ? props.apiKey : props.apiBase;
        copyToClipboard(text, type);
    }
}
</script>

<template>
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h3 class="card-title">API Token</h3>
            <div class="form-control">
                <label class="label">
                    <span class="label-text">X-Api-Key</span>
                </label>
                <div class="relative inline-block w-full">
                    <span
                        ref="triggerElementKey"
                        class="font-mono cursor-pointer hover:text-primary transition-colors block p-3 bg-base-200 rounded-lg border border-base-300 overflow-hidden"
                        :title="apiKey"
                        @click="copyToClipboard(apiKey, 'key')"
                        @keydown="(e) => onKeydown(e, 'key')"
                        @mouseenter="showTooltipHandler('key')"
                        @mouseleave="hideTooltipHandler('key')"
                        @focus="showTooltipHandler('key')"
                        @blur="hideTooltipHandler('key')"
                        tabindex="0"
                        role="button"
                    >
                        <span class="hidden sm:inline break-all">{{ apiKey }}</span>
                        <span class="inline sm:hidden">{{ truncatedKey }}</span>
                    </span>
                    <Teleport to="body">
                        <div
                            v-if="showTooltipKey"
                            ref="tooltipElementKey"
                            class="fixed z-[9999] px-3 py-2 text-sm bg-base-300 text-base-content rounded-lg shadow-lg pointer-events-none whitespace-nowrap"
                            :style="{ top: '0px', left: '0px' }"
                        >
                            {{ tooltipTextKey }}
                        </div>
                    </Teleport>
                </div>
            </div>
            <div class="form-control mt-4">
                <label class="label">
                    <span class="label-text">Базовый URL API</span>
                </label>
                <div class="relative inline-block w-full">
                    <span
                        ref="triggerElementBase"
                        class="font-mono cursor-pointer hover:text-primary transition-colors block p-3 bg-base-200 rounded-lg border border-base-300 overflow-hidden"
                        :title="apiBase"
                        @click="copyToClipboard(apiBase, 'base')"
                        @keydown="(e) => onKeydown(e, 'base')"
                        @mouseenter="showTooltipHandler('base')"
                        @mouseleave="hideTooltipHandler('base')"
                        @focus="showTooltipHandler('base')"
                        @blur="hideTooltipHandler('base')"
                        tabindex="0"
                        role="button"
                    >
                        <span class="hidden sm:inline break-all">{{ apiBase }}</span>
                        <span class="inline sm:hidden">{{ truncatedBase }}</span>
                    </span>
                    <Teleport to="body">
                        <div
                            v-if="showTooltipBase"
                            ref="tooltipElementBase"
                            class="fixed z-[9999] px-3 py-2 text-sm bg-base-300 text-base-content rounded-lg shadow-lg pointer-events-none whitespace-nowrap"
                            :style="{ top: '0px', left: '0px' }"
                        >
                            {{ tooltipTextBase }}
                        </div>
                    </Teleport>
                </div>
            </div>
        </div>
    </div>
</template>

