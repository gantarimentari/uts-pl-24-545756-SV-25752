
<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { Plus } from 'lucide-vue-next'
import Sparkle from '@/components/placelog/bits/Sparkle.vue'
</script>

<template>
  <div
    class="card-paper relative mx-auto flex max-w-2xl flex-col items-center px-6 py-14 text-center"
  >
    <div class="relative mb-8">
      <svg
        width="220"
        height="160"
        viewBox="0 0 220 160"
        aria-hidden="true"
      >
        <path
          d="M10 30l65-18 70 18 65-18v118l-65 18-70-18-65 18Z"
          fill="var(--cream)"
          stroke="var(--primary)"
          stroke-width="2"
          stroke-linejoin="round"
        />
        <path
          d="M75 12v118M145 30v118"
          stroke="var(--primary)"
          stroke-width="1.5"
          stroke-dasharray="4 5"
        />
        <path
          d="M30 110c30-30 60 10 90-20s50-10 70-40"
          fill="none"
          stroke="var(--maroon)"
          stroke-width="2"
          stroke-dasharray="5 6"
          stroke-linecap="round"
        />
      </svg>

      <svg
        width="44"
        height="54"
        viewBox="0 0 34 41"
        class="float-y absolute left-[118px] top-[18px]"
        aria-hidden="true"
      >
        <path
          d="M17 40s15-13.6 15-23A15 15 0 0 0 2 17c0 9.4 15 23 15 23Z"
          fill="var(--maroon)"
          stroke="var(--paper)"
          stroke-width="2.5"
        />
        <circle cx="17" cy="16.5" r="5" fill="var(--cream)" />
      </svg>

      <Sparkle class="absolute -right-4 -top-2 text-maroon" />
      <Sparkle
        class="absolute -left-6 bottom-4 text-primary"
        :size="16"
      />
    </div>

    <h2 class="font-display text-3xl font-semibold text-maroon">
      Your map is waiting for its first story.
    </h2>

    <p class="mt-3 max-w-md text-muted-foreground">
      You haven't added any places yet. Start with somewhere
      you've visited recently.
    </p>

    <RouterLink to="/places/new" class="btn btn-primary mt-7">
      <Plus :size="16" />
      Add Your First Place
    </RouterLink>
  </div>
</template>
