
<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { Search } from 'lucide-vue-next'

import AppShell from '@/components/placelog/AppShell.vue'
import EmptyState from '@/components/placelog/EmptyState.vue'
import Sparkle from '@/components/placelog/bits/Sparkle.vue'

import { getPlaces } from '@/services/places'
import type { Place } from '@/types/place'

const places = ref<Place[]>([])
const search = ref('')
const loading = ref(true)
const error = ref('')

// Mengambil data tempat dari Laravel API
async function loadPlaces() {
  loading.value = true
  error.value = ''

  try {
    places.value = await getPlaces()
  } catch (err) {
    error.value =
      err instanceof Error
        ? err.message
        : 'Failed to load places.'
  } finally {
    loading.value = false
  }
}

onMounted(loadPlaces)

// Mencari tempat berdasarkan nama
const filteredPlaces = computed(() =>
  places.value.filter((place) =>
    place.name.toLowerCase().includes(search.value.toLowerCase()),
  ),
)

// Menghitung statistik
const averageRating = computed(() => {
  const rated = places.value.filter(
    (place) => place.rating !== null,
  )

  if (rated.length === 0) return '0.0'

  const total = rated.reduce(
    (sum, place) => sum + Number(place.rating),
    0,
  )

  return (total / rated.length).toFixed(1)
})

const stats = computed(() => [
  {
    value: places.value.length,
    label: 'Places Visited',
  },
  {
    value: averageRating.value,
    label: 'Average Rating',
  },
])
</script>

<template>
  <AppShell>
    <!-- Header Journal -->
    <div
      class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between"
    >
      <div class="relative">
        <Sparkle
          class="absolute -left-5 -top-3 text-maroon"
          :size="18"
        />

        <h1
          class="font-display text-4xl font-semibold text-primary md:text-5xl"
        >
          My Journal
        </h1>

        <p class="mt-2 font-display text-lg italic text-maroon">
          A little map of everywhere you've been.
        </p>
      </div>

      <!-- Search -->
      <label class="relative block md:w-72">
        <Search
          :size="16"
          class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
        />

        <input
          v-model="search"
          type="text"
          class="field pl-9"
          placeholder="Search your places…"
        />
      </label>
    </div>

    <!-- Loading -->
    <div
      v-if="loading"
      class="mt-16 text-center text-muted-foreground"
    >
      Loading your memories...
    </div>

    <!-- Error -->
    <div
      v-else-if="error"
      class="card-paper mt-10 p-6 text-center"
    >
      <p class="text-destructive">{{ error }}</p>

      <button
        type="button"
        class="btn btn-primary mt-4"
        @click="loadPlaces"
      >
        Try Again
      </button>
    </div>

    <!-- Empty State -->
    <div v-else-if="places.length === 0" class="mt-12">
      <EmptyState />
    </div>

    <!-- Data Places -->
    <template v-else>
      <!-- Statistics -->
      <div
        class="mt-8 grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-5"
      >
        <div
          v-for="(stat, index) in stats"
          :key="stat.label"
          class="card-paper relative overflow-hidden px-5 py-4"
          :style="{
            rotate: `${[-0.8, 0.5][index]}deg`,
          }"
        >
          <p class="font-display text-4xl font-semibold text-maroon">
            {{ stat.value }}
          </p>

          <p class="mt-1 text-sm text-muted-foreground">
            {{ stat.label }}
          </p>
        </div>
      </div>

      <!-- Daftar tempat sementara -->
      <section class="mt-10">
        <h2 class="font-display text-2xl font-semibold">
          Recently visited
        </h2>

        <p
          v-if="filteredPlaces.length === 0"
          class="mt-4 text-sm text-muted-foreground"
        >
          No places match "{{ search }}".
        </p>

        <div
          v-else
          class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
        >
          <div
            v-for="place in filteredPlaces"
            :key="place.id"
            class="card-paper p-5"
          >
            <h3 class="font-display text-xl font-semibold">
              {{ place.name }}
            </h3>

            <p class="mt-1 text-sm text-muted-foreground">
              {{ place.category }}
            </p>

            <p class="mt-3 text-sm text-maroon">
              Rating:
              {{ place.rating ?? 'Not rated' }}
            </p>

            <p class="mt-2 text-sm text-muted-foreground">
              {{ place.address || 'No address added' }}
            </p>
          </div>
        </div>
      </section>
    </template>
  </AppShell>
</template>
