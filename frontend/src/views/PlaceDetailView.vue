
<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  ArrowLeft,
  CalendarDays,
  MapPin,
  Pencil,
  Trash2,
  Star,
  Coffee,
  Utensils,
  Trees,
  Landmark,
  ShoppingBag,
  Clapperboard,
} from 'lucide-vue-next'

import AppShell from '@/components/placelog/AppShell.vue'
import MemoryMap from '@/components/placelog/MemoryMap.vue'
import Sparkle from '@/components/placelog/bits/Sparkle.vue'
import { getPlace, deletePlace } from '@/services/places'
import type { Place } from '@/types/place'

const route = useRoute()
const router = useRouter()

const place = ref<Place | null>(null)
const loading = ref(true)
const error = ref('')
const confirmDelete = ref(false)
const deleting = ref(false)
const deleteError = ref('')

const placeId = computed(() => Number(route.params.id))

const hasCoordinates = computed(() => {
  if (!place.value) return false

  const lat = place.value.latitude
  const lng = place.value.longitude

  return (
    lat != null &&
    lng != null &&
    Number.isFinite(Number(lat)) &&
    Number.isFinite(Number(lng)) &&
    Number(lat) >= -90 &&
    Number(lat) <= 90 &&
    Number(lng) >= -180 &&
    Number(lng) <= 180
  )
})

const categoryIcons = {
  cafe: Coffee,
  restaurant: Utensils,
  nature: Trees,
  historical: Landmark,
  shopping: ShoppingBag,
  entertainment: Clapperboard,
}

function getCategoryIcon(category: string) {
  return (
    categoryIcons[category.toLowerCase() as keyof typeof categoryIcons] ??
    MapPin
  )
}

function getCategoryColor(category: string) {
  const colors: Record<string, string> = {
    cafe: 'bg-[#F0DDE0] text-[#873B50]',
    restaurant: 'bg-[#F6E5CD] text-[#A16A30]',
    nature: 'bg-[#DDECE1] text-[#4C7968]',
    historical: 'bg-[#E9DFED] text-[#9472A0]',
    shopping: 'bg-[#F4DDE3] text-[#C16C75]',
    entertainment: 'bg-[#DDE5F2] text-[#657CA0]',
  }

  return colors[category.toLowerCase()] ?? 'bg-cream text-maroon'
}

function formatDate(date: string | null) {
  if (!date) return 'Unknown date'

  const parsed = new Date(date)

  if (Number.isNaN(parsed.getTime())) return date

  return parsed.toLocaleDateString('en-US', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

async function loadPlace() {
  loading.value = true
  error.value = ''
  place.value = null

  if (!Number.isInteger(placeId.value) || placeId.value <= 0) {
    error.value = 'Invalid place ID.'
    loading.value = false
    return
  }

  try {
    place.value = await getPlace(placeId.value)
  } catch (err) {
    error.value =
      err instanceof Error ? err.message : 'Failed to load place.'
  } finally {
    loading.value = false
  }
}

async function handleDelete() {
  if (!place.value || deleting.value) return

  deleting.value = true
  deleteError.value = ''

  try {
    await deletePlace(place.value.id)
    confirmDelete.value = false
    await router.push('/journal')
  } catch (err) {
    deleteError.value =
      err instanceof Error ? err.message : 'Failed to delete place.'
  } finally {
    deleting.value = false
  }
}

onMounted(loadPlace)
watch(placeId, loadPlace)
</script>

<template>
  <AppShell>
    <RouterLink
      to="/journal"
      class="mb-6 inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
    >
      <ArrowLeft :size="16" />
      My Journal
    </RouterLink>

    <div v-if="loading" class="card-paper p-10 text-center">
      Loading place...
    </div>

    <div v-else-if="error || !place" class="py-24 text-center">
      <h1 class="font-display text-3xl text-maroon">
        This place isn't in your journal.
      </h1>

      <p v-if="error" class="mt-3 text-sm text-muted-foreground">
        {{ error }}
      </p>

      <RouterLink
        to="/journal"
        class="btn btn-primary mt-6 inline-flex"
      >
        Back to My Journal
      </RouterLink>
    </div>

    <div v-else class="grid gap-8 lg:grid-cols-[1.4fr_1fr]">
      <!-- Left: photo placeholder and notes -->
      <div class="min-w-0">
        <div class="card-paper p-2.5">
          <div
            :class="[
              'flex aspect-[16/10] items-center justify-center overflow-hidden rounded-lg',
              getCategoryColor(place.category),
            ]"
          >
            <component
              :is="getCategoryIcon(place.category)"
              :size="100"
              :stroke-width="1.1"
            />
          </div>
        </div>

        <div class="mt-8">
          <h2 class="font-display text-2xl font-semibold">
            My Notes
          </h2>

          <blockquote
            class="card-paper relative mt-3 p-6 font-display text-xl italic leading-relaxed text-foreground/90"
          >
            <span
              class="absolute -left-1 -top-5 font-display text-6xl text-maroon/30"
            >
              "
            </span>

            <p class="whitespace-pre-wrap">
              {{ place.description || 'No notes yet.' }}
            </p>
          </blockquote>
        </div>
      </div>

      <!-- Right: place information -->
      <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
        <div class="card-paper relative p-6">
          <Sparkle class="absolute -right-2 -top-3 text-maroon" />

          <span
            class="inline-block rounded-full bg-maroon/10 px-3 py-1 text-xs font-semibold text-maroon"
          >
            {{ place.category }}
          </span>

          <h1
            class="mt-3 font-display text-4xl font-semibold leading-tight text-primary"
          >
            {{ place.name }}
          </h1>

          <div class="mt-3 flex items-center gap-2">
            <div class="flex gap-0.5">
              <Star
                v-for="value in 5"
                :key="value"
                :size="18"
                :class="
                  value <= (place.rating ?? 0)
                    ? 'fill-maroon text-maroon'
                    : 'text-maroon/30'
                "
              />
            </div>

            <span class="font-display text-xl font-semibold text-maroon">
              {{
                place.rating != null
                  ? Number(place.rating).toFixed(1)
                  : 'Not rated'
              }}
            </span>
          </div>

          <div class="mt-5 space-y-3 text-sm">
            <p v-if="place.address" class="flex items-start gap-2">
              <MapPin :size="16" class="mt-0.5 shrink-0 text-maroon" />
              <span>{{ place.address }}</span>
            </p>

            <p class="flex items-center gap-2">
              <CalendarDays :size="16" class="text-maroon" />
              Visited {{ formatDate(place.visit_date) }}
            </p>
          </div>
        </div>

        <!-- Location -->
        <div class="card-paper p-3">
          <h2 class="mb-2 px-1 font-display text-lg font-semibold">
            Location
          </h2>

          <MemoryMap
            v-if="hasCoordinates"
            :places="[place]"
            class="h-56"
          />

          <p v-else class="p-4 text-sm text-muted-foreground">
            No location has been saved for this place.
          </p>
        </div>

        <!-- Actions -->
        <div class="flex gap-3">
          <RouterLink
            :to="`/places/${place.id}/edit`"
            class="btn btn-primary inline-flex flex-1 items-center justify-center gap-2"
          >
            <Pencil :size="16" />
            Edit Place
          </RouterLink>

          <button
            type="button"
            class="btn btn-danger-soft inline-flex items-center gap-2"
            @click="confirmDelete = true"
          >
            <Trash2 :size="16" />
            Delete
          </button>
        </div>
      </aside>
    </div>

    <!-- Delete confirmation -->
    <div
      v-if="confirmDelete && place"
      class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/50 p-4"
      @click.self="confirmDelete = false"
    >
      <div
        role="dialog"
        aria-modal="true"
        aria-labelledby="delete-dialog-title"
        class="card-paper w-full max-w-sm p-6"
      >
        <h3
          id="delete-dialog-title"
          class="font-display text-2xl font-semibold text-maroon"
        >
          Remove this memory?
        </h3>

        <p class="mt-2 text-sm text-muted-foreground">
          "{{ place.name }}" will be removed from your journal.
          This can't be undone.
        </p>

        <p v-if="deleteError" class="mt-3 text-sm text-destructive">
          {{ deleteError }}
        </p>

        <div class="mt-6 flex justify-end gap-2">
          <button
            type="button"
            class="btn btn-ghost"
            :disabled="deleting"
            @click="confirmDelete = false"
          >
            Keep it
          </button>

          <button
            type="button"
            class="btn bg-destructive text-destructive-foreground"
            :disabled="deleting"
            @click="handleDelete"
          >
            {{ deleting ? 'Deleting...' : 'Delete Place' }}
          </button>
        </div>
      </div>
    </div>
  </AppShell>
</template>
