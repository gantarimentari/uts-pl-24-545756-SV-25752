
<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import {
  Plus,
  Search,
  MapPin,
  Star,
  CalendarDays,
  Coffee,
  Utensils,
  Trees,
  Landmark,
  ShoppingBag,
  Clapperboard,
  Image as ImageIcon,
  ArrowUpRight,
} from 'lucide-vue-next'

import AppShell from '@/components/placelog/AppShell.vue'
import EmptyState from '@/components/placelog/EmptyState.vue'
import MemoryMap from '@/components/placelog/MemoryMap.vue'
import Sparkle from '@/components/placelog/bits/Sparkle.vue'

import { getPlaces } from '@/services/places'
import type { Place } from '@/types/place'

const places = ref<Place[]>([])
const search = ref('')
const loading = ref(true)
const error = ref('')

const filteredPlaces = computed(() =>
  places.value.filter((place) =>
    place.name.toLowerCase().includes(search.value.toLowerCase()),
  ),
)

const recentPlaces = computed(() =>
  [...filteredPlaces.value].sort((a, b) => {
    const dateA = a.visit_date ?? a.created_at
    const dateB = b.visit_date ?? b.created_at
    return dateB.localeCompare(dateA)
  }),
)

const mappedPlaces = computed(() =>
  filteredPlaces.value.filter(
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

const averageRating = computed(() => {
  const rated = places.value.filter((place) => place.rating != null)

  if (rated.length === 0) return '0.0'

  const total = rated.reduce(
    (sum, place) => sum + Number(place.rating),
    0,
  )

  return (total / rated.length).toFixed(1)
})

const stats = computed(() => [
  {
    label: 'Places Visited',
    value: places.value.length,
    icon: MapPin,
  },
  {
    label: 'Average Rating',
    value: averageRating.value,
    icon: Star,
  },
])

function formatDate(date: string | null): string {
  if (!date) return 'Unknown date'

  const parsed = new Date(date)

  if (Number.isNaN(parsed.getTime())) return date

  return parsed.toLocaleDateString('en-US', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

function categoryIcon(category: string) {
  const icons: Record<string, typeof MapPin> = {
    cafe: Coffee,
    restaurant: Utensils,
    nature: Trees,
    historical: Landmark,
    shopping: ShoppingBag,
    entertainment: Clapperboard,
  }

  return icons[category.toLowerCase()] ?? MapPin
}

function categoryColor(category: string): string {
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

async function loadPlaces() {
  loading.value = true
  error.value = ''

  try {
    places.value = await getPlaces()
  } catch (err) {
    error.value =
      err instanceof Error
        ? err.message
        : 'Failed to load your journal.'
  } finally {
    loading.value = false
  }
}

onMounted(loadPlaces)
</script>

<template>
  <AppShell>
    <!-- Header -->
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

      <div class="relative md:w-72">
        <Search
          :size="17"
          class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
        />

        <input
          v-model="search"
          type="text"
          class="field w-full pl-10"
          placeholder="Search your places..."
        />
      </div>
    </div>

    <!-- Loading -->
    <div
      v-if="loading"
      class="card-paper mt-10 p-8 text-center text-muted-foreground"
    >
      Loading your journal...
    </div>

    <!-- Error -->
    <div v-else-if="error" class="card-paper mt-10 p-8 text-center">
      <p class="mb-4 text-destructive">{{ error }}</p>

      <button
        type="button"
        class="btn btn-primary"
        @click="loadPlaces"
      >
        Try again
      </button>
    </div>

    <!-- Empty state -->
    <div v-else-if="places.length === 0" class="mt-12">
      <EmptyState />
    </div>

    <template v-else>
      <!-- Statistics -->
      <div class="mt-8 grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-5">
        <div
          v-for="(stat, index) in stats"
          :key="stat.label"
          class="card-paper relative overflow-hidden px-5 py-4"
          :style="{
            transform: `rotate(${[-0.8, 0.5][index]}deg)`,
          }"
        >
          <component
            :is="stat.icon"
            :size="22"
            class="absolute right-4 top-4 text-maroon/60"
          />

          <p class="font-display text-4xl font-semibold text-maroon">
            {{ stat.value }}
          </p>

          <p class="mt-1 text-sm text-muted-foreground">
            {{ stat.label }}
          </p>
        </div>
      </div>

      <!-- Memory Map + Recently Visited -->
      <div class="mt-8 grid gap-6 lg:grid-cols-[1.9fr_1fr]">
        <!-- Memory Map -->
        <div class="min-w-0">
          <div class="mb-3 flex items-baseline justify-between">
            <h2 class="font-display text-2xl font-semibold">
              Memory map
            </h2>

            <RouterLink
              to="/map"
              class="text-sm font-medium text-primary hover:underline"
            >
              Open full map →
            </RouterLink>
          </div>

          <MemoryMap
            :places="mappedPlaces"
            class="h-[420px] md:h-[560px]"
          />
        </div>

        <!-- Recently visited -->
        <div class="min-w-0">
          <div class="mb-3 flex items-baseline justify-between">
            <h2 class="font-display text-2xl font-semibold">
              Recently visited
            </h2>

            <RouterLink
              to="/places"
              class="text-sm font-medium text-primary hover:underline"
            >
              All places →
            </RouterLink>
          </div>

          <div class="space-y-3">
            <div
              v-for="place in recentPlaces.slice(0, 5)"
              :key="place.id"
              class="card-paper flex gap-3 p-2.5 transition hover:-translate-y-0.5 hover:shadow-lift"
            >
              <!-- Placeholder instead of photo -->
              <div
                :class="[
                  'flex h-20 w-20 shrink-0 items-center justify-center rounded-lg',
                  categoryColor(place.category),
                ]"
              >
                <component
                  :is="categoryIcon(place.category)"
                  :size="30"
                  :stroke-width="1.5"
                />
              </div>

              <div class="min-w-0 py-0.5">
                <h3
                  class="truncate font-display text-lg font-semibold leading-tight"
                >
                  {{ place.name }}
                </h3>

                <p class="text-sm text-muted-foreground">
                  {{ place.category }}
                </p>

                <div
                  class="mt-1 flex items-center gap-1.5 text-sm"
                >
                  <Star
                    :size="13"
                    class="fill-maroon text-maroon"
                  />

                  <span class="font-semibold text-maroon">
                    {{
                      place.rating != null
                        ? Number(place.rating).toFixed(1)
                        : '–'
                    }}
                  </span>
                </div>

                <p class="mt-1 text-xs text-muted-foreground">
                  Visited {{ formatDate(place.visit_date) }}
                </p>
              </div>
            </div>

            <p
              v-if="recentPlaces.length === 0"
              class="text-sm text-muted-foreground"
            >
              No places match "{{ search }}".
            </p>
          </div>
        </div>
      </div>

      <!-- Scrapbook -->
      


    </template>
  </AppShell>
</template>
