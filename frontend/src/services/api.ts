
import axios from 'axios'
import type { AxiosRequestConfig } from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    Accept: 'application/json',
  },
})

// Menambahkan token login ke setiap request
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token')

  if (token) {
    config.headers.set('Authorization', `Bearer ${token}`)
  }

  return config
})

// Semua request API akan menggunakan Axios
export async function apiRequest<T>(
  endpoint: string,
  options: RequestInit = {},
): Promise<T> {
  try {
    const config: AxiosRequestConfig = {
      url: endpoint,
      method: options.method || 'GET',
    }

    if (options.headers) {
      const headers = new Headers(options.headers)
      config.headers = Object.fromEntries(headers.entries())
    }

    if (options.body != null) {
      config.data =
        typeof options.body === 'string'
          ? JSON.parse(options.body)
          : options.body
    }

    const response = await api.request<T>(config)

    return response.data
  } catch (error) {
    if (axios.isAxiosError(error)) {
      const message =
        error.response?.data?.message ||
        error.message ||
        'Request failed.'

      throw new Error(message)
    }

    throw error
  }
}

export default api
