
import { apiRequest } from './api'

import type {
  Place,
  PlacePayload,
  PlaceResponse,
  PlacesResponse,
} from '@/types/place'

// Mengambil seluruh tempat pengguna
export async function getPlaces(): Promise<Place[]> {
  const response = await apiRequest<PlacesResponse>('/places')

  return response.data
}

// Mengambil detail satu tempat
export async function getPlace(id: number): Promise<Place> {
  const response = await apiRequest<PlaceResponse>(`/places/${id}`)

  return response.data
}

// Menambahkan tempat baru
export async function createPlace(
  payload: PlacePayload,
): Promise<Place> {
  const response = await apiRequest<PlaceResponse>('/places', {
    method: 'POST',
    body: JSON.stringify(payload),
  })

  return response.data
}

// Memperbarui data tempat
export async function updatePlace(
  id: number,
  payload: Partial<PlacePayload>,
): Promise<Place> {
  const response = await apiRequest<PlaceResponse>(`/places/${id}`, {
    method: 'PUT',
    body: JSON.stringify(payload),
  })

  return response.data
}

// Menghapus tempat
export async function deletePlace(id: number): Promise<void> {
  await apiRequest<{ message: string }>(`/places/${id}`, {
    method: 'DELETE',
  })
}
