
<script setup lang="ts">
withDefaults(
  defineProps<{
    className?: string
    size?: number
  }>(),
  {
    className: '',
    size: 22,
  },
)
</script>

<template>
  <svg
    :width="size"
    :height="size"
    viewBox="0 0 24 24"
    :class="className"
    aria-hidden="true"
  >
    <path
      d="M12 2c.6 4.6 2.4 7.4 9 10-6.6 2.6-8.4 5.4-9 10-.6-4.6-2.4-7.4-9-10 6.6-2.6 8.4-5.4 9-10Z"
      fill="none"
      stroke="currentColor"
      stroke-width="1.6"
      stroke-linejoin="round"
    />
  </svg>
</template>
