
<script setup lang="ts">
defineProps<{
  className?: string
}>()
</script>

<template>
  <svg
    viewBox="0 0 200 120"
    :class="className"
    aria-hidden="true"
  >
    <path
      d="M5 110C40 40 90 120 120 60S180 10 195 8"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-dasharray="6 7"
      stroke-linecap="round"
    />
  </svg>
</template>
