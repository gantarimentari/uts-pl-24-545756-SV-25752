
<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import {
  BookHeart,
  List,
  Map as MapIcon,
  Plus,
  User,
  LogOut,
  ChevronDown,
} from 'lucide-vue-next'

import Logo from '@/components/placelog/bits/Logo.vue'
import { apiRequest } from '@/services/api'

withDefaults(
  defineProps<{
    hideFab?: boolean
  }>(),
  {
    hideFab: false,
  },
)

const router = useRouter()

const showProfileMenu = ref(false)
const loggingOut = ref(false)

// Navigation
const navItems = [
  {
    to: '/journal',
    label: 'My Journal',
    short: 'Journal',
    icon: BookHeart,
  },
  {
    to: '/places',
    label: 'Places',
    short: 'Places',
    icon: List,
  },
  {
    to: '/map',
    label: 'Map',
    short: 'Map',
    icon: MapIcon,
  },
]

// Data pengguna yang sedang login
const currentUser = ref<{ name?: string } | null>(null)

function loadCurrentUser() {
  const storedUser = localStorage.getItem('auth_user')

  if (!storedUser) {
    currentUser.value = null
    return
  }

  try {
    const parsed: unknown = JSON.parse(storedUser)

    if (parsed && typeof parsed === 'object' && 'name' in parsed) {
      const name = (parsed as { name?: unknown }).name

      currentUser.value = {
        name: typeof name === 'string' ? name : '',
      }
    } else {
      currentUser.value = null
    }
  } catch {
    currentUser.value = null
  }
}

// Mengambil inisial dari field name
const userInitial = computed(() => {
  const name = currentUser.value?.name?.trim()

  if (!name) return '?'

  return name
    .split(/\s+/)
    .slice(0, 2)
    .map((word) => word.charAt(0).toUpperCase())
    .join('')
})

// Profile dropdown
function toggleProfileMenu() {
  showProfileMenu.value = !showProfileMenu.value
}

function closeProfileMenu() {
  showProfileMenu.value = false
}

function handleOutsideClick(event: MouseEvent) {
  const target = event.target

  if (!(target instanceof Node)) return

  const menus = document.querySelectorAll('[data-profile-menu]')

  const clickedInside = Array.from(menus).some((menu) =>
    menu.contains(target),
  )

  if (!clickedInside) {
    closeProfileMenu()
  }
}

function handleEscape(event: KeyboardEvent) {
  if (event.key === 'Escape') {
    closeProfileMenu()
  }
}

// Logout
async function handleLogout() {
  if (loggingOut.value) return

  loggingOut.value = true

  try {
    await apiRequest('/logout', {
      method: 'POST',
    })
  } catch (err) {
    console.error('Logout request failed:', err)
  } finally {
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_user')

    currentUser.value = null
    closeProfileMenu()
    loggingOut.value = false

    await router.replace('/login')
  }
}

// Lifecycle
onMounted(async () => {
  loadCurrentUser()

  // Ambil data pengguna dari Laravel jika belum tersimpan
  if (!currentUser.value?.name && localStorage.getItem('auth_token')) {
    try {
      const response = await apiRequest<{
        id: number
        name: string
        email: string
      }>('/user')

      currentUser.value = response

      localStorage.setItem(
        'auth_user',
        JSON.stringify(response),
      )
    } catch (err) {
      console.error('Failed to load current user:', err)
    }
  }

  document.addEventListener('click', handleOutsideClick)
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('click', handleOutsideClick)
  document.removeEventListener('keydown', handleEscape)
})
</script>

<template>
  <div class="min-h-screen pb-24 md:pb-0">
    <!-- Navbar desktop -->
    <header
      class="sticky top-0 z-50 border-b border-border bg-background/90 backdrop-blur"
    >
      <div
        class="mx-auto flex h-16 max-w-7xl items-center gap-8 px-4 md:px-8"
      >
        <!-- Logo -->
        <RouterLink to="/journal">
          <Logo />
        </RouterLink>

        <!-- Desktop navigation -->
        <nav class="hidden items-center gap-1 md:flex">
          <RouterLink
            v-for="item in navItems"
            :key="item.to"
            :to="item.to"
            class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition hover:text-foreground"
            active-class="!text-maroon bg-cream"
          >
            {{ item.label }}
          </RouterLink>
        </nav>

        <!-- Right side -->
        <div class="ml-auto flex items-center gap-3">
          <RouterLink
            to="/places/new"
            class="btn btn-primary hidden md:inline-flex"
          >
            <Plus :size="16" />
            Add Place
          </RouterLink>

          <!-- Desktop profile -->
          <div class="relative" data-profile-menu>
            <button
              type="button"
              class="flex items-center gap-2 rounded-full transition hover:opacity-80"
              aria-label="Profile menu"
              aria-haspopup="menu"
              :aria-expanded="showProfileMenu"
              @click="toggleProfileMenu"
            >
              <div
                class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-paper bg-maroon font-display text-sm font-semibold text-primary-foreground shadow-soft"
              >
                {{ userInitial }}
              </div>

              <ChevronDown
                :size="15"
                class="text-muted-foreground transition-transform"
                :class="{ 'rotate-180': showProfileMenu }"
              />
            </button>

            <!-- Desktop dropdown -->
            <div
              v-if="showProfileMenu"
              class="card-paper absolute right-0 top-full z-50 mt-3 w-44 p-2 shadow-lg"
              role="menu"
            >
              <button
                type="button"
                role="menuitem"
                class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-destructive transition hover:bg-destructive/10 disabled:opacity-50"
                :disabled="loggingOut"
                @click="handleLogout"
              >
                <LogOut :size="17" />
                {{ loggingOut ? 'Logging out...' : 'Log out' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Page content -->
    <main class="mx-auto max-w-7xl px-4 py-8 md:px-8">
      <slot />
    </main>

    <!-- Mobile add button -->
    <RouterLink
      v-if="!hideFab"
      to="/places/new"
      aria-label="Add Place"
      class="btn btn-primary fixed bottom-20 right-4 z-40 h-14 w-14 rounded-full p-0 shadow-lift md:hidden"
    >
      <Plus :size="24" />
    </RouterLink>

    <!-- Mobile navigation -->
    <nav
      class="fixed inset-x-0 bottom-0 z-50 grid grid-cols-4 border-t border-border bg-paper md:hidden"
    >
      <RouterLink
        v-for="item in navItems"
        :key="item.to"
        :to="item.to"
        class="flex flex-col items-center gap-1 py-2.5 text-xs text-muted-foreground"
        active-class="!text-primary font-semibold"
      >
        <component :is="item.icon" :size="20" />
        {{ item.short }}
      </RouterLink>

      <!-- Mobile profile -->
      <div class="relative" data-profile-menu>
        <button
          type="button"
          class="flex h-full w-full flex-col items-center gap-1 py-2.5 text-xs text-muted-foreground"
          aria-label="Profile menu"
          aria-haspopup="menu"
          :aria-expanded="showProfileMenu"
          @click="toggleProfileMenu"
        >
          <User :size="20" />
          Profile
        </button>

        <!-- Mobile dropdown -->
        <div
          v-if="showProfileMenu"
          class="card-paper absolute bottom-full right-2 z-50 mb-3 w-44 p-2 shadow-lg"
          role="menu"
        >
          <button
            type="button"
            role="menuitem"
            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-destructive transition hover:bg-destructive/10 disabled:opacity-50"
            :disabled="loggingOut"
            @click="handleLogout"
          >
            <LogOut :size="17" />
            {{ loggingOut ? 'Logging out...' : 'Log out' }}
          </button>
        </div>
      </div>
    </nav>
  </div>
</template>
