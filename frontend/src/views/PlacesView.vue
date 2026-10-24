
<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import {
  Search,
  Star,
  MapPin,
  Coffee,
  Utensils,
  Trees,
  Landmark,
  ShoppingBag,
  Clapperboard,
  ArrowUpRight,
  ChevronDown,
} from 'lucide-vue-next'

import AppShell from '@/components/placelog/AppShell.vue'
import EmptyState from '@/components/placelog/EmptyState.vue'
import { getPlaces } from '@/services/places'
import type { Place } from '@/types/place'

type SortOption = 'newest' | 'oldest' | 'rating-high' | 'rating-low'

const places = ref<Place[]>([])
const search = ref('')
const category = ref('All')
const sort = ref<SortOption>('newest')
const loading = ref(true)
const error = ref('')

const categories = computed(() => [
  'All',
  ...new Set(places.value.map((place) => place.category)),
])

const filteredPlaces = computed(() => {
  const result = places.value.filter((place) => {
    const matchesSearch = place.name
      .toLowerCase()
      .includes(search.value.trim().toLowerCase())

    const matchesCategory =
      category.value === 'All' || place.category === category.value

    return matchesSearch && matchesCategory
  })

  return result.sort((a, b) => {
    if (sort.value === 'rating-high') {
      return (b.rating ?? 0) - (a.rating ?? 0)
    }

    if (sort.value === 'rating-low') {
      return (a.rating ?? 0) - (b.rating ?? 0)
    }

    const dateA = a.visit_date ?? a.created_at
    const dateB = b.visit_date ?? b.created_at

    return sort.value === 'newest'
      ? dateB.localeCompare(dateA)
      : dateA.localeCompare(dateB)
  })
})

function categoryIcon(categoryName: string) {
  const icons: Record<string, typeof MapPin> = {
    cafe: Coffee,
    restaurant: Utensils,
    nature: Trees,
    historical: Landmark,
    shopping: ShoppingBag,
    entertainment: Clapperboard,
  }

  return icons[categoryName.toLowerCase()] ?? MapPin
}

function categoryColor(categoryName: string): string {
  const colors: Record<string, string> = {
    cafe: 'bg-[#F0DDE0] text-[#873B50]',
    restaurant: 'bg-[#F6E5CD] text-[#A16A30]',
    nature: 'bg-[#DDECE1] text-[#4C7968]',
    historical: 'bg-[#E9DFED] text-[#9472A0]',
    shopping: 'bg-[#F4DDE3] text-[#C16C75]',
    entertainment: 'bg-[#DDE5F2] text-[#657CA0]',
  }

  return colors[categoryName.toLowerCase()] ?? 'bg-cream text-maroon'
}

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
</script>

<template>
  <AppShell>
    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
      <div>
        <h1 class="font-display text-4xl font-semibold text-primary md:text-5xl">
          Places
        </h1>

        <p class="mt-2 text-muted-foreground">
          {{ places.length }} places, {{ filteredPlaces.length }} shown
        </p>
      </div>

      <!-- Search -->
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
      Loading your places...
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
      <!-- Filters -->
      <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
          <button
            v-for="item in categories"
            :key="item"
            type="button"
            :class="[
              'rounded-full border px-4 py-2 text-sm font-medium transition',
              category === item
                ? 'border-maroon bg-maroon text-white'
                : 'border-border bg-paper text-primary hover:bg-cream',
            ]"
            @click="category = item"
          >
            {{ item }}
          </button>
        </div>

        <div class="relative">
          <select
            v-model="sort"
            class="field appearance-none py-2 pl-3 pr-9 text-sm"
            aria-label="Sort places"
          >
            <option value="newest">Newest first</option>
            <option value="oldest">Oldest first</option>
            <option value="rating-high">Highest rated</option>
            <option value="rating-low">Lowest rated</option>
          </select>

          <ChevronDown
            :size="16"
            class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2"
          />
        </div>
      </div>

      <!-- Place cards -->
      <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <RouterLink
          v-for="(place, index) in filteredPlaces"
          :key="place.id"
          :to="`/places/${place.id}`"
          class="card-paper overflow-hidden transition hover:-translate-y-1 hover:shadow-lift"
          :style="{
            transform: `rotate(${[-0.8, 0.6, -0.4, 0.9][index % 4]}deg)`,
          }"
        >
          <!-- Photo placeholder -->
          <div
            :class="[
              'flex h-44 items-center justify-center',
              categoryColor(place.category),
            ]"
          >
            <component
              :is="categoryIcon(place.category)"
              :size="58"
              :stroke-width="1.2"
            />
          </div>

          <div class="space-y-2 p-4">
            <div class="flex items-start justify-between gap-2">
              <h3 class="font-display text-xl font-semibold">
                {{ place.name }}
              </h3>

              <ArrowUpRight :size="18" class="shrink-0 text-maroon" />
            </div>

            <p class="text-sm text-muted-foreground">
              {{ place.category }}
            </p>

            <div class="flex items-center gap-1 text-maroon">
              <Star :size="15" class="fill-current" />

              <span class="text-sm font-semibold">
                {{
                  place.rating != null
                    ? Number(place.rating).toFixed(1)
                    : 'Not rated'
                }}
              </span>
            </div>

            <p class="text-xs text-muted-foreground">
              {{ formatDate(place.visit_date) }}
            </p>
          </div>
        </RouterLink>
      </div>

      <!-- No search/filter results -->
      <p
        v-if="filteredPlaces.length === 0"
        class="mt-16 text-center font-display text-xl italic text-muted-foreground"
      >
        Nothing here yet — try another category.
      </p>
    </template>
  </AppShell>
</template>
