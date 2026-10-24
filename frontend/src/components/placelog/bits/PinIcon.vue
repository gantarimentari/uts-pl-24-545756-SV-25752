
<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(
  defineProps<{
    category: string
    size?: number
    active?: boolean
  }>(),
  {
    size: 34,
    active: false,
  },
)

const pinColors: Record<string, string> = {
  Cafe: 'var(--pin-1)',
  'Tourist Spot': 'var(--pin-2)',
}

const pinColor = computed(
  () => pinColors[props.category] ?? 'var(--primary)',
)
</script>

<template>
  <svg
    :width="size"
    :height="size * 1.2"
    viewBox="0 0 34 41"
    aria-hidden="true"
    :class="active ? 'drop-shadow-lg' : 'drop-shadow'"
  >
    <path
      d="M17 40s15-13.6 15-23A15 15 0 0 0 2 17c0 9.4 15 23 15 23Z"
      :fill="pinColor"
      stroke="var(--paper)"
      stroke-width="2.5"
    />

    <circle cx="17" cy="16.5" r="9" fill="var(--paper)" />
    <circle cx="17" cy="16.5" r="3.5" :fill="pinColor" />
  </svg>
</template>
