
<script setup lang="ts">
import AppShell from '@/components/placelog/AppShell.vue'
import PlaceForm from '@/components/PlaceForm.vue'
</script>

<template>
  <AppShell hide-fab>
    <p class="font-display italic text-maroon">
      New entry
    </p>

    <h1
      class="mb-8 font-display text-4xl font-semibold text-primary md:text-5xl"
    >
      Where did you go?
    </h1>

    <PlaceForm />
  </AppShell>
</template>
