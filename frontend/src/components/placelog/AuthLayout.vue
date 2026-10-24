
<script setup lang="ts">
import nature from '@/assets/nature.jpg'
import temple from '@/assets/temple.jpg'
import cafe from '@/assets/cafe.jpg'

import Logo from '@/components/placelog/bits/Logo.vue'
import Sparkle from '@/components/placelog/bits/Sparkle.vue'
import DashedPath from '@/components/placelog/bits/DashedPath.vue'
import PinIcon from '@/components/placelog/bits/PinIcon.vue'

defineProps<{
  title: string
  subtitle: string
}>()
</script>

<template>
  <div class="grid min-h-screen lg:grid-cols-2">
    <div class="flex flex-col px-6 py-6 md:px-14">

      <Logo />


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

    <div
      class="relative hidden overflow-hidden border-l border-border bg-cream lg:block"
    >

      <DashedPath
        className="absolute left-10 top-24 h-72 w-[80%] text-primary/40"
      />

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

      <div class=" absolute right-[28%] top-[12%]">
        <PinIcon category="Cafe" :size="44" />
      </div>

      <div class="absolute bottom-[22%] right-[14%]">
        <PinIcon category="Tourist Spot" :size="36" />
      </div>

      <Sparkle
        className="absolute bottom-[30%] left-[10%] text-maroon"
        :size="28"
      />

      <Sparkle
        className="absolute right-[8%] top-[10%] text-primary"
        :size="20"
      />
    </div>
  </div>
</template>
