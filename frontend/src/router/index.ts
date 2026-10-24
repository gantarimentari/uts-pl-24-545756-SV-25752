
import { createRouter, createWebHistory } from 'vue-router'

import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import JournalView from '@/views/JournalView.vue'
import AddPlaceView from '@/views/AddPlaceView.vue'
import MapView from '@/views/MapView.vue'
import PlacesView from '@/views/PlacesView.vue'
import PlaceDetailView from '@/views/PlaceDetailView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/journal',
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
    },
    {
      path: '/journal',
      name: 'journal',
      component: JournalView,
      meta: { requiresAuth: true },
    },
    {
      path: '/places/new',
      name: 'add-place',
      component: AddPlaceView,
      meta: { requiresAuth: true },
    },
    {
      path: '/map',
      name: 'map',
      component: MapView,
      meta: { requiresAuth: true },
    },
    {
    path: '/places',
    name: 'places',
    component: PlacesView,
    meta: { requiresAuth: true },
},
  {
    path: '/places/:id',
    name: 'place-detail',
    component: PlaceDetailView,
    meta: { requiresAuth: true },
  },
  ],
})
router.beforeEach((to) => {
  const token = localStorage.getItem('auth_token')

  if (to.meta.requiresAuth && !token) {
    return { name: 'login' }
  }


  if (
    token &&
    (to.name === 'login' || to.name === 'register')
  ) {
    return { name: 'journal' }
  }
})

export default router
