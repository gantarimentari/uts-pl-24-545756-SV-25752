
<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { MapPin, RefreshCw } from 'lucide-vue-next'

import AppShell from '@/components/placelog/AppShell.vue'
import MemoryMap from '@/components/placelog/MemoryMap.vue'
import { getPlaces } from '@/services/places'
import type { Place } from '@/types/place'

const places = ref<Place[]>([])
const loading = ref(true)
const error = ref('')

const mappedPlaces = computed(() =>
  places.value.filter(
    (place) =>
      place.latitude != null &&
      place.longitude != null &&
      Number.isFinite(Number(place.latitude)) &&
      Number.isFinite(Number(place.longitude)) &&
      Number(place.latitude) >= -90 &&
      Number(place.latitude) <= 90 &&
      Number(place.longitude) >= -180 &&
      Number(place.longitude) <= 180,
  ),
)

async function loadPlaces() {
  loading.value = true
  error.value = ''

  try {
    places.value = await getPlaces()
  } catch (err) {
    error.value =
      err instanceof Error
        ? err.message
        : 'Failed to load your places.'
  } finally {
    loading.value = false
  }
}

onMounted(loadPlaces)
</script>

<template>
  <AppShell>
    <div class="mb-8">
      <p class="font-display italic text-maroon">
        Your memories, mapped
      </p>

      <h1
        class="font-display text-4xl font-semibold text-primary md:text-5xl"
      >
        Memory Map
      </h1>

      <p class="mt-3 text-muted-foreground">
        Every pin holds a story. Explore the places you've been.
      </p>
    </div>

    <div v-if="loading" class="card-paper p-8 text-center">
      Loading your memory map...
    </div>

    <div v-else-if="error" class="card-paper p-8 text-center">
      <p class="mb-4 text-destructive">{{ error }}</p>

      <button
        type="button"
        class="btn btn-primary inline-flex items-center gap-2"
        @click="loadPlaces"
      >
        <RefreshCw :size="16" />
        Try again
      </button>
    </div>

    <template v-else>
      <div class="mb-5 flex items-center gap-2 text-sm text-muted-foreground">
        <MapPin :size="16" class="text-maroon" />
        {{ mappedPlaces.length }} places pinned
      </div>

      <MemoryMap :places="mappedPlaces" />

      <p
        v-if="places.length > mappedPlaces.length"
        class="mt-4 text-sm text-muted-foreground"
      >
        {{ places.length - mappedPlaces.length }}
        place(s) aren't shown because they don't have valid map coordinates.
      </p>
    </template>
  </AppShell>
</template>
