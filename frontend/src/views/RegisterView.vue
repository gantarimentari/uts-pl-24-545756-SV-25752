
<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import AuthLayout from '@/components/placelog/AuthLayout.vue'
import { apiRequest } from '@/services/api'

interface RegisterResponse {
  message: string
  token: string
  user: {
    id: number
    name: string
    email: string
  }
}

const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')

const errorMessage = ref('')
const isLoading = ref(false)

async function handleRegister() {
  errorMessage.value = ''

  // Validasi password sebelum mengirim ke backend
  if (password.value.length < 8) {
    errorMessage.value = 'Password should be at least 8 characters.'
    return
  }

  if (password.value !== passwordConfirmation.value) {
    errorMessage.value = "Passwords don't match."
    return
  }

  isLoading.value = true

  try {
    const data = await apiRequest<RegisterResponse>('/register', {
      method: 'POST',
      body: JSON.stringify({
        name: name.value,
        email: email.value,
        password: password.value,
        password_confirmation: passwordConfirmation.value,
      }),
    })

    // Simpan token autentikasi Laravel Sanctum
    localStorage.setItem('auth_token', data.token)

    // Arahkan pengguna ke halaman jurnal
    await router.push('/journal')
  } catch (error) {
    errorMessage.value =
      error instanceof Error
        ? error.message
        : 'Registration failed. Please try again.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <AuthLayout
    title="Start your journal."
    subtitle="A quiet little place for all the places you love."
  >
    <form class="space-y-4" @submit.prevent="handleRegister">
      <!-- Name -->
      <label class="block">
        <span class="mb-1.5 block text-sm font-medium">
          Name
        </span>

        <input
          v-model="name"
          required
          type="text"
          class="field"
          placeholder="Nadia"
          autocomplete="name"
        />
      </label>

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
          placeholder="At least 8 characters"
          autocomplete="new-password"
        />
      </label>

      <!-- Confirm Password -->
      <label class="block">
        <span class="mb-1.5 block text-sm font-medium">
          Confirm Password
        </span>

        <input
          v-model="passwordConfirmation"
          required
          type="password"
          class="field"
          placeholder="One more time"
          autocomplete="new-password"
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

      <!-- Tombol Register -->
      <button
        type="submit"
        class="btn btn-primary w-full py-3"
        :disabled="isLoading"
      >
        {{ isLoading ? 'Creating Account...' : 'Create Account' }}
      </button>

      <!-- Link Login -->
      <p class="text-center text-sm text-muted-foreground">
        Already have a journal?
        <RouterLink
          to="/login"
          class="font-semibold text-primary underline-offset-4 hover:underline"
        >
          Log in
        </RouterLink>
      </p>
    </form>
  </AuthLayout>
</template>
