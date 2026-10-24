
<script setup lang="ts">
import { onMounted, onUnmounted, ref, watch } from 'vue'
import { Search, MapPin } from 'lucide-vue-next'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import axios from 'axios'

type SearchResult = {
  place_id: number
  display_name: string
  lat: string
  lon: string
}

const props = defineProps<{
  latitude: number | null
  longitude: number | null
}>()

const emit = defineEmits<{
  change: [latitude: number, longitude: number, address?: string]
}>()

const mapElement = ref<HTMLElement | null>(null)
const query = ref('')
const results = ref<SearchResult[]>([])
const searching = ref(false)
const searchError = ref('')

let map: L.Map | null = null
let marker: L.CircleMarker | null = null
let searchController: AbortController | null = null

const defaultPosition: L.LatLngExpression = [-7.7956, 110.3695]

// Menampilkan atau memperbarui marker pada peta
function updateMarker(lat: number, lng: number) {
  if (!map) return

  if (marker) {
    marker.setLatLng([lat, lng])
  } else {
    marker = L.circleMarker([lat, lng], {
      radius: 9,
      color: '#ffffff',
      weight: 3,
      fillColor: '#7f3346',
      fillOpacity: 1,
    }).addTo(map)
  }
}

// Memilih lokasi dan mengirim koordinat ke form
function selectLocation(lat: number, lng: number, address?: string) {
  updateMarker(lat, lng)
  map?.setView([lat, lng], 16)
  emit('change', lat, lng, address)
}

// Mencari lokasi menggunakan Axios dan Nominatim
async function searchLocation() {
  const keyword = query.value.trim()

  if (!keyword) return

  searchController?.abort()

  const controller = new AbortController()
  searchController = controller

  searching.value = true
  searchError.value = ''
  results.value = []

  try {
    const response = await axios.get<SearchResult[]>(
      'https://nominatim.openstreetmap.org/search',
      {
        params: {
          q: keyword,
          format: 'jsonv2',
          limit: 5,
          addressdetails: 1,
        },
        signal: controller.signal,
        headers: {
          Accept: 'application/json',
        },
      },
    )

    // Axios otomatis membaca respons JSON
    if (searchController !== controller) return

    results.value = response.data

    if (results.value.length === 0) {
      searchError.value = 'No locations found. Try another keyword.'
    }
  } catch (error) {
    // Mengabaikan request yang dibatalkan
    if (controller.signal.aborted) return

    searchError.value = axios.isAxiosError(error)
      ? error.message
      : 'Failed to search locations.'
  } finally {
    if (searchController === controller) {
      searching.value = false
      searchController = null
    }
  }
}

// Memilih lokasi dari hasil pencarian
function chooseResult(result: SearchResult) {
  const lat = Number(result.lat)
  const lng = Number(result.lon)

  query.value = result.display_name
  results.value = []

  selectLocation(lat, lng, result.display_name)
}

// Inisialisasi peta Leaflet
onMounted(() => {
  if (!mapElement.value) return

  const hasPosition =
    props.latitude != null && props.longitude != null

  const center: L.LatLngExpression = hasPosition
    ? [props.latitude!, props.longitude!]
    : defaultPosition

  map = L.map(mapElement.value).setView(center, 12)

  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19,
  }).addTo(map)

  if (hasPosition) {
    updateMarker(props.latitude!, props.longitude!)
  }

  // Memilih lokasi dengan mengklik peta
  map.on('click', (event: L.LeafletMouseEvent) => {
    selectLocation(event.latlng.lat, event.latlng.lng)
  })
})

// Memperbarui marker ketika koordinat berubah
watch(
  () => [props.latitude, props.longitude] as const,
  ([lat, lng]) => {
    if (lat == null || lng == null || !map) return

    updateMarker(lat, lng)
  },
)

// Membersihkan map dan request ketika komponen ditutup
onUnmounted(() => {
  searchController?.abort()
  map?.remove()
  map = null
  marker = null
})
</script>

<template>
  <div class="space-y-3">
    <!-- Search bar -->
    <form class="relative flex gap-2" @submit.prevent="searchLocation">
      <div class="relative flex-1">
        <Search
          :size="18"
          class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
        />

        <input
          v-model="query"
          type="text"
          class="field w-full pl-10"
          placeholder="Search a place or address..."
        />
      </div>

      <button
        type="submit"
        class="btn btn-primary"
        :disabled="searching || !query.trim()"
      >
        {{ searching ? 'Searching...' : 'Search' }}
      </button>
    </form>

    <!-- Search results -->
    <div
      v-if="results.length > 0"
      class="max-h-56 overflow-y-auto rounded-xl border border-border bg-paper shadow-soft"
    >
      <button
        v-for="result in results"
        :key="result.place_id"
        type="button"
        class="flex w-full items-start gap-2 border-b border-border px-4 py-3 text-left text-sm transition hover:bg-cream"
        @click="chooseResult(result)"
      >
        <MapPin
          :size="16"
          class="mt-0.5 shrink-0 text-maroon"
        />

        <span>{{ result.display_name }}</span>
      </button>
    </div>

    <p v-if="searchError" class="text-sm text-destructive">
      {{ searchError }}
    </p>

    <!-- Leaflet map -->
    <div
      ref="mapElement"
      class="h-80 w-full overflow-hidden rounded-xl md:h-96"
    />
  </div>
</template>
