
<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import AuthLayout from '@/components/placelog/AuthLayout.vue'
import { apiRequest } from '@/services/api'

interface LoginResponse {
  message: string
  token: string
  user: {
    id: number
    name: string
    email: string
  }
}

const router = useRouter()

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

async function handleLogin() {
  errorMessage.value = ''
  isLoading.value = true

  try {
    const data = await apiRequest<LoginResponse>('/login', {
      method: 'POST',
      body: JSON.stringify({
        email: email.value,
        password: password.value,
      }),
    })

    if (!data.token) {
      errorMessage.value = 'Login gagal. Silakan periksa email dan password.'
      return
    }

    // Simpan token autentikasi Laravel Sanctum
    localStorage.setItem('auth_token', data.token)

    // Arahkan pengguna ke halaman jurnal
    await router.push('/journal')

  } catch (error) {
    const message =
      error instanceof Error ? error.message : ''

    const lowerMessage = message.toLowerCase()

    if (
      lowerMessage.includes('unauthenticated') ||
      lowerMessage.includes('invalid') ||
      lowerMessage.includes('credentials') ||
      lowerMessage.includes('incorrect')
    ) {
      errorMessage.value =
        'Email atau password salah. Silakan periksa kembali.'
    } else {
      errorMessage.value =
        message || 'Login gagal. Silakan coba lagi.'
    }

  } finally {
    isLoading.value = false
  }
}

</script>

<template>
  <AuthLayout
    title="Welcome back."
    subtitle="Your map missed you. Let's pick up where you left off."
  >
    <form class="space-y-4" @submit.prevent="handleLogin">
      <!-- Email -->
      <label class="block">
        <span class="mb-1.5 block text-sm font-medium">
          Email
        </span>

        <input
          v-model="email"
          required
          type="email"
          class="field"
          placeholder="you@example.com"
          autocomplete="email"
        />
      </label>

      <!-- Password -->
      <label class="block">
        <span class="mb-1.5 block text-sm font-medium">
          Password
        </span>

        <input
          v-model="password"
          required
          type="password"
          class="field"
          placeholder="••••••••"
          autocomplete="current-password"
        />
      </label>

      <!-- Pesan error -->
      <p
        v-if="errorMessage"
        role="alert"
        class="text-sm text-destructive"
      >
        {{ errorMessage }}
      </p>

      <!-- Tombol Login -->
      <button
        type="submit"
        class="btn btn-primary w-full py-3"
        :disabled="isLoading"
      >
        {{ isLoading ? 'Logging in...' : 'Log In' }}
      </button>

      <!-- Tombol Register -->
      <RouterLink
        to="/register"
        class="btn btn-ghost w-full py-3"
      >
        Continue to Register
      </RouterLink>
    </form>
  </AuthLayout>
</template>
