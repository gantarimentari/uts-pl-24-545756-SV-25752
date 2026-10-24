
<script setup lang="ts">
defineProps<{
  className?: string
}>()
</script>

<template>
  <RouterLink
    to="/"
    :class="['flex items-center gap-2', className]"
  >
    <svg width="26" height="30" viewBox="0 0 26 30" aria-hidden="true">
      <path
        d="M13 29s11-10.5 11-17.5A11 11 0 0 0 2 11.5C2 18.5 13 29 13 29Z"
        fill="var(--primary)"
      />
      <circle cx="13" cy="11.5" r="4.2" fill="var(--cream)" />
    </svg>

    <span class="font-display text-xl font-semibold tracking-tight text-primary">
      PlaceLog
    </span>
  </RouterLink>
</template>
