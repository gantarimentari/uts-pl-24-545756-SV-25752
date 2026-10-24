
<script setup lang="ts">
import nature from '@/assets/nature.jpg'
import temple from '@/assets/temple.jpg'
import cafe from '@/assets/cafe.jpg'

defineProps<{
  title: string
  subtitle: string
}>()
</script>

<template>
  <div class="grid min-h-screen lg:grid-cols-2">
    <!-- Bagian kiri: form autentikasi -->
    <div class="flex flex-col px-6 py-6 md:px-14">
      <RouterLink
        to="/login"
        class="flex items-center gap-3 font-display text-2xl font-bold text-primary"
      >
        <span class="text-3xl">📍</span>
        PlaceLog
      </RouterLink>

      <div class="mx-auto flex w-full max-w-sm flex-1 flex-col justify-center py-12">
        <h1 class="font-display text-4xl font-semibold text-primary">
          {{ title }}
        </h1>

        <p class="mt-2 text-muted-foreground">
          {{ subtitle }}
        </p>

        <div class="mt-8">
          <slot />
        </div>
      </div>
    </div>

    <!-- Bagian kanan: kolase foto scrapbook -->
    <div
      class="relative hidden overflow-hidden border-l border-border bg-cream lg:block"
    >
      <!-- Garis perjalanan -->
      <svg
        class="absolute left-10 top-24 h-72 w-[80%] text-primary/40"
        viewBox="0 0 500 300"
        fill="none"
      >
        <path
          d="M20 180 C120 20 220 280 340 100 S450 60 480 40"
          stroke="currentColor"
          stroke-width="3"
          stroke-dasharray="8 12"
          stroke-linecap="round"
        />
      </svg>

      <!-- Foto Kalibiru -->
      <div
        class="card-paper tape absolute left-[14%] top-[18%] w-60 -rotate-6 p-2.5 pb-8"
      >
        <img
          :src="nature"
          alt="Pemandangan Kalibiru"
          class="aspect-[4/5] w-full rounded-md object-cover"
        />
        <p class="absolute bottom-2 left-4 font-display text-sm italic">
          Kalibiru, sunrise
        </p>
      </div>

      <!-- Foto Prambanan -->
      <div
        class="card-paper tape absolute right-[12%] top-[34%] w-56 rotate-6 p-2.5 pb-8"
      >
        <img
          :src="temple"
          alt="Candi Prambanan"
          class="aspect-square w-full rounded-md object-cover"
        />
        <p class="absolute bottom-2 left-4 font-display text-sm italic">
          Prambanan ★ 5.0
        </p>
      </div>

      <!-- Foto kafe -->
      <div
        class="card-paper absolute bottom-[10%] left-[28%] w-48 -rotate-2 p-2.5 pb-8"
      >
        <img
          :src="cafe"
          alt="Secangkir kopi di kafe"
          class="aspect-square w-full rounded-md object-cover"
        />
        <p class="absolute bottom-2 left-4 font-display text-sm italic">
          rainy day latte
        </p>
      </div>

      <!-- Dekorasi -->
      <span class="float-y absolute right-[28%] top-[12%] text-4xl">
        📍
      </span>

      <span class="absolute bottom-[30%] left-[10%] text-3xl text-maroon">
        ✦
      </span>

      <span class="absolute right-[8%] top-[10%] text-2xl text-primary">
        ✧
      </span>
    </div>
  </div>
</template>
