
<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'

import AppShell from '@/components/placelog/AppShell.vue'
import PlaceForm from '@/components/PlaceForm.vue'

import { getPlace } from '@/services/places'
import type { Place } from '@/types/place'

const route = useRoute()

const place = ref<Place | null>(null)
const loading = ref(true)
const error = ref('')

async function loadPlace() {
  loading.value = true
  error.value = ''

  try {
    const id = Number(route.params.id)

    if (!Number.isSafeInteger(id) || id <= 0) {
      throw new Error('Invalid place ID.')
    }

    place.value = await getPlace(id)
  } catch (err) {
    error.value =
      err instanceof Error
        ? err.message
        : 'Failed to load place.'
  } finally {
    loading.value = false
  }
}

onMounted(loadPlace)
</script>

<template>
  <AppShell hide-fab>
    <div
      v-if="loading"
      class="py-24 text-center text-muted-foreground"
    >
      Loading place...
    </div>

    <template v-else-if="place">
      <p class="font-display italic text-maroon">
        Editing
      </p>

      <h1
        class="mb-8 font-display text-4xl font-semibold text-primary md:text-5xl"
      >
        {{ place.name }}
      </h1>

      <PlaceForm :key="place.id" :initial="place" />
    </template>

    <div v-else class="py-24 text-center">
      <h1 class="font-display text-3xl text-maroon">
        This place isn't in your journal.
      </h1>

      <p
        v-if="error"
        class="mt-3 text-sm text-muted-foreground"
      >
        {{ error }}
      </p>

      <RouterLink
        to="/journal"
        class="btn btn-primary mt-6 inline-flex"
      >
        Back to My Journal
      </RouterLink>
    </div>
  </AppShell>
</template>
