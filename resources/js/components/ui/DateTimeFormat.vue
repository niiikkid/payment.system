<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  /**
   * ISO-строка даты (например: "2025-11-01T12:34:56Z")
   */
  value: string
  /**
   * Поддерживаемые токены: YYYY, MM, DD, HH, mm, ss
   * По умолчанию: 'YYYY-MM-DD HH:mm:ss'
   */
  pattern?: string
}

const props = defineProps<Props>()

function pad2(n: number): string {
  return n.toString().padStart(2, '0')
}

function formatWithPattern(date: Date, pattern: string): string {
  const YYYY = String(date.getFullYear())
  const MM = pad2(date.getMonth() + 1)
  const DD = pad2(date.getDate())
  const HH = pad2(date.getHours())
  const mm = pad2(date.getMinutes())
  const ss = pad2(date.getSeconds())

  return pattern
    .replace(/YYYY/g, YYYY)
    .replace(/MM/g, MM)
    .replace(/DD/g, DD)
    .replace(/HH/g, HH)
    .replace(/mm/g, mm)
    .replace(/ss/g, ss)
}

const dateObject = computed<Date | null>(() => {
  // Ожидаем ISO-строку
  const parsed = new Date(props.value)
  return Number.isNaN(parsed.getTime()) ? null : parsed
})

const formatted = computed<string>(() => {
  if (!dateObject.value) return ''
  const pattern = props.pattern ?? 'YYYY-MM-DD HH:mm:ss'
  return formatWithPattern(dateObject.value, pattern)
})
</script>

<template>
  <span>{{ formatted }}</span>
</template>


