
<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { MapPin, Star } from 'lucide-vue-next'

import LocationPicker from '@/components/placelog/LocationPicker.vue'
import Sparkle from '@/components/placelog/bits/Sparkle.vue'

import { createPlace, updatePlace } from '@/services/places'
import type { Place, PlacePayload } from '@/types/place'

const router = useRouter()

const categories = [
  'Cafe',
  'Restaurant',
  'Nature',
  'Historical',
  'Shopping',
  'Entertainment',
  'Other',
]

const form = ref<PlacePayload>({
  name: '',
  category: 'Cafe',
  rating: null,
  description: '',
  address: '',
  latitude: null,
  longitude: null,
  visit_date: new Date().toLocaleDateString('en-CA'),
})

const error = ref('')
const loading = ref(false)

function setLocation(
  latitude: number,
  longitude: number,
  address?: string,
) {
  form.value.latitude = latitude
  form.value.longitude = longitude

  if (address !== undefined) {
    form.value.address = address
  }
}
const props = defineProps<{
  initial?: Place
}>()

async function handleSubmit() {
  error.value = ''

  if (!form.value.name.trim()) {
    error.value = 'Give this place a name first.'
    return
  }

  if (!form.value.rating) {
    error.value = 'How many stars would you give it?'
    return
  }

  loading.value = true

  try {
    const place = await createPlace({
      ...form.value,
      name: form.value.name.trim(),
    })

    await router.push(`/places/${place.id}`)
    await router.push('/journal')
  } catch (err) {
    error.value =
      err instanceof Error
        ? err.message
        : 'Failed to save place.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <form
    class="grid gap-8 lg:grid-cols-[1.1fr_1fr]"
    @submit.prevent="handleSubmit"
  >
    <!-- Informasi tempat -->
    <div class="card-paper relative space-y-7 p-6 md:p-8">
      <Sparkle class="absolute -right-2 -top-3 text-maroon" />

      <div>
        <label class="mb-2 block font-display text-lg font-semibold">
          Place name
        </label>
        <input
          v-model="form.name"
          class="field font-display text-xl"
          placeholder="e.g. Tempo Gelato"
          maxlength="255"
          required
        />
      </div>

      <div class="grid gap-6 sm:grid-cols-2">
        <div>
          <label class="mb-2 block font-display text-lg font-semibold">
            Category
          </label>

          <select v-model="form.category" class="field">
            <option
              v-for="category in categories"
              :key="category"
              :value="category"
            >
              {{ category }}
            </option>
          </select>
        </div>

        <div>
          <label class="mb-2 block font-display text-lg font-semibold">
            Visit date
          </label>

          <input
            v-model="form.visit_date"
            type="date"
            class="field"
          />
        </div>
      </div>

      <!-- Rating -->
      <div>
        <label class="mb-2 block font-display text-lg font-semibold">
          Rating
        </label>

        <div class="flex items-center gap-2">
          <button
            v-for="value in 5"
            :key="value"
            type="button"
            :aria-label="`${value} stars`"
            @click="form.rating = value"
          >
            <Star
              :size="34"
              :stroke-width="1.5"
              :class="
                value <= (form.rating ?? 0)
                  ? 'fill-maroon text-maroon'
                  : 'fill-transparent text-maroon/30'
              "
            />
          </button>

          <span class="ml-2 font-display text-2xl font-semibold text-maroon">
            {{ form.rating ? form.rating.toFixed(1) : '–' }}
          </span>
        </div>
      </div>

      <!-- Catatan -->
      <div>
        <label class="mb-2 block font-display text-lg font-semibold">
          My notes
        </label>

        <textarea
          v-model="form.description"
          rows="5"
          class="field leading-relaxed"
          placeholder="Tell your future self what you remember about this place..."
        />
      </div>
    </div>

    <!-- Lokasi dan tombol simpan -->
    <div class="space-y-4">
      <div class="card-paper p-4">
        <div class="mb-2 flex items-baseline justify-between">
          <span class="font-display text-lg font-semibold">
            Location
          </span>
          <span class="text-xs text-muted-foreground">
            Tap the map to drop your pin
          </span>
        </div>

        <LocationPicker
          :latitude="form.latitude ?? null"
          :longitude="form.longitude ?? null"
          @change="setLocation"
        />

        <div class="mt-4 flex items-start gap-3 rounded-lg bg-cream/60 p-3">
          <MapPin
            :size="18"
            class="mt-1 shrink-0 text-maroon"
          />

          <div class="flex-1">
            <p class="font-semibold">
              Selected location
            </p>

            <p class="mt-1 text-xs text-muted-foreground">
              {{
                form.latitude != null && form.longitude != null
                ? `${form.latitude.toFixed(5)}, ${form.longitude.toFixed(5)}`
                : 'No location selected yet'
              }}
            </p>

            <input
              v-model="form.address"
              class="mt-3 w-full bg-transparent text-sm outline-none placeholder:text-muted-foreground/70"
              placeholder="Add a street or address (optional)"
              maxlength="255"
            />
          </div>
        </div>
      </div>

      <p
        v-if="error"
        class="rounded-md bg-destructive/10 px-3 py-2 text-sm text-destructive"
      >
        {{ error }}
      </p>

      <div class="flex gap-3">
        <button
          type="submit"
          class="btn btn-primary flex-1 py-3"
          :disabled="loading"
        >
          {{ loading ? 'Saving...' : 'Save Place' }}
        </button>

        <RouterLink to="/journal" class="btn btn-ghost py-3">
          Cancel
        </RouterLink>
      </div>
    </div>
  </form>
</template>
