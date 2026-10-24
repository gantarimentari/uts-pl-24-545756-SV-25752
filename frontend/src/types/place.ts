
export interface Place {
  id: number
  user_id: number
  name: string
  category: string
  rating: number | null
  description: string | null
  address: string | null
  latitude: number | string | null
  longitude: number | string | null
  visit_date: string | null
  created_at: string
  updated_at: string
}

export interface PlacePayload {
  name: string
  category: string
  rating?: number | null
  description?: string | null
  address?: string | null
  latitude?: number | null
  longitude?: number | null
  visit_date?: string | null
}

export interface PlaceResponse {
  message: string
  data: Place
}

export interface PlacesResponse {
  message: string
  data: Place[]
}
