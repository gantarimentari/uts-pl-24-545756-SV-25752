
<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import {
  BookHeart,
  List,
  Map as MapIcon,
  Plus,
  User,
} from 'lucide-vue-next'

import Logo from '@/components/placelog/bits/Logo.vue'

withDefaults(defineProps<{
  hideFab?: boolean
}>(), {
  hideFab: false,
})

const navItems = [
  { to: '/journal', label: 'My Journal', short: 'Journal', icon: BookHeart },
  { to: '/places', label: 'Places', short: 'Places', icon: List },
  { to: '/map', label: 'Map', short: 'Map', icon: MapIcon },
]

const userName = computed(() => {
  const storedUser = localStorage.getItem('auth_user')

  if (!storedUser) return 'You'

  try {
    const user = JSON.parse(storedUser) as { name?: string }
    return user.name || 'You'
  } catch {
    return 'You'
  }
})
</script>

<template>
  <div class="min-h-screen pb-24 md:pb-0">
    <!-- Navbar desktop -->
    <header
      class="sticky top-0 z-50 border-b border-border bg-background/90 backdrop-blur"
    >
      <div class="mx-auto flex h-16 max-w-7xl items-center gap-8 px-4 md:px-8">
        <RouterLink to="/journal">
          <Logo />
        </RouterLink>

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

        <div class="ml-auto flex items-center gap-3">
          <RouterLink
            to="/places/new"
            class="btn btn-primary hidden md:inline-flex"
          >
            <Plus :size="16" />
            Add Place
          </RouterLink>

          <RouterLink
            to="/profile"
            aria-label="Profile"
            class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-paper bg-maroon font-display text-sm font-semibold text-primary-foreground shadow-soft"
          >
            {{ userName.charAt(0).toUpperCase() }}
          </RouterLink>
        </div>
      </div>
    </header>

    <!-- Konten halaman -->
    <main class="mx-auto max-w-7xl px-4 py-8 md:px-8">
      <slot />
    </main>

    <!-- Tombol tambah tempat untuk mobile -->
    <RouterLink
      v-if="!hideFab"
      to="/places/new"
      aria-label="Add Place"
      class="btn btn-primary fixed bottom-20 right-4 z-50 h-14 w-14 rounded-full p-0 shadow-lift md:hidden"
    >
      <Plus :size="24" />
    </RouterLink>

    <!-- Navbar mobile -->
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

      <RouterLink
        to="/profile"
        class="flex flex-col items-center gap-1 py-2.5 text-xs text-muted-foreground"
        active-class="!text-primary font-semibold"
      >
        <User :size="20" />
        Profile
      </RouterLink>
    </nav>
  </div>
</template>
