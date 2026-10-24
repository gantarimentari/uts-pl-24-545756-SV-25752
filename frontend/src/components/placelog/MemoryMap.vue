
<script setup lang="ts">
import { onMounted, onUnmounted, ref, watch } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

import type { Place } from '@/types/place'

const props = withDefaults(
  defineProps<{
    places: Place[]
    class?: string
  }>(),
  {
    class: 'h-[420px] md:h-[600px]',
  },
)

const mapElement = ref<HTMLElement | null>(null)

let map: L.Map | null = null
let markersLayer: L.FeatureGroup | null = null

const defaultPosition: L.LatLngExpression = [-7.7956, 110.3695]

function escapeHtml(value: string): string {
  return value.replace(/[&<>"']/g, (character) => {
    const entities: Record<string, string> = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;',
    }

    return entities[character] ?? character
  })
}

function getMarkerColor(category: string): string {
  const colors: Record<string, string> = {
    cafe: '#873B50',
    restaurant: '#C18443',
    nature: '#4C7968',
    historical: '#9472A0',
    shopping: '#C16C75',
    entertainment: '#657CA0',
    other: '#6F7773',
  }

  return colors[category.toLowerCase()] ?? colors.other!
}

function createPinIcon(category: string): L.DivIcon {
  const color = getMarkerColor(category)

  return L.divIcon({
    className: 'placelog-map-pin',
    html: `
      <svg
        width="36"
        height="46"
        viewBox="0 0 44 56"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M22 3C11 3 3 11 3 22c0 15 19 30 19 30s19-15 19-30C41 11 33 3 22 3Z"
          fill="${color}"
          stroke="#fff9ef"
          stroke-width="2.5"
        />
        <circle cx="22" cy="22" r="8" fill="#fff9ef"/>
      </svg>
    `,
    iconSize: [36, 46],
    iconAnchor: [18, 43],
    popupAnchor: [0, -43],
  })
}

function renderMarkers() {
  if (!map || !markersLayer) return

  markersLayer.clearLayers()

  for (const place of props.places) {
    if (place.latitude == null || place.longitude == null) continue

    const lat = Number(place.latitude)
    const lng = Number(place.longitude)

    if (
      !Number.isFinite(lat) ||
      !Number.isFinite(lng) ||
      lat < -90 ||
      lat > 90 ||
      lng < -180 ||
      lng > 180
    ) {
      continue
    }

    const marker = L.marker([lat, lng], {
      icon: createPinIcon(place.category),
    })

    const name = escapeHtml(place.name)
    const category = escapeHtml(place.category)
    const address = place.address ? escapeHtml(place.address) : ''
    const rating = place.rating != null ? `${place.rating}/5` : 'Not rated'

    marker.bindPopup(`
      <div style="min-width:180px">
        <strong style="font-size:16px">${name}</strong>
        <p style="margin:6px 0">${category}</p>
        ${address ? `<p style="margin:6px 0">${address}</p>` : ''}
        <p style="margin:6px 0;font-weight:600;color:#873B50">
          Rating: ${rating}
        </p>
      </div>
    `)

    markersLayer.addLayer(marker)
  }

  if (markersLayer.getLayers().length > 0) {
    map.fitBounds(markersLayer.getBounds(), {
      padding: [35, 35],
      maxZoom: 15,
    })
  } else {
    map.setView(defaultPosition, 12)
  }
}

onMounted(() => {
  if (!mapElement.value) return

  map = L.map(mapElement.value).setView(defaultPosition, 12)

  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19,
  }).addTo(map)

  markersLayer = L.featureGroup().addTo(map)

  renderMarkers()
})

watch(
  () => props.places,
  () => renderMarkers(),
  { deep: true },
)

onUnmounted(() => {
  map?.remove()
  map = null
  markersLayer = null
})
</script>

<template>
  <div class="card-paper overflow-hidden p-3">
    <div
      ref="mapElement"
      :class="['w-full rounded-xl', props.class]"
    />
  </div>
</template>
