import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'home',
    component: () => import('@/views/home/HomeView.vue'),
    meta: {
      title: 'Inicio - Alcaldía'
    }
  },
  {
    path: '/noticias',
    name: 'noticias',
    component: () => import('@/views/noticias/NoticiasView.vue'),
    meta: {
      title: 'Noticias - Alcaldía'
    }
  },
  {
    path: '/transparencia',
    name: 'transparencia',
    component: () => import('@/views/transparencia/TransparenciaView.vue'),
    meta: {
      title: 'Transparencia - Alcaldía'
    }
  },
  {
    path: '/pqrs',
    name: 'pqrs',
    component: () => import('@/views/pqrs/PqrsView.vue'),
    meta: {
      title: 'PQRS - Alcaldía'
    }
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(_to, _from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Actualizar título de página
router.afterEach((to) => {
  document.title = (to.meta.title as string) || 'Alcaldía - Gobierno Digital'
})

export default router
