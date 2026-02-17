/**
 * Configuración de Vue Router
 * 
 * Define las rutas de la aplicación
 */

import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'

/**
 * Definición de rutas
 */
const routes: RouteRecordRaw[] = [
  {
    path: '/',
    redirect: '/servers',
  },
  {
    path: '/servers',
    name: 'server-list',
    component: () => import('@/views/ServerList.vue'),
    meta: {
      title: 'Servidores VPS',
    },
  },
  {
    path: '/servers/create',
    name: 'server-create',
    component: () => import('@/views/ServerForm.vue'),
    meta: {
      title: 'Nuevo Servidor',
    },
  },
  {
    path: '/servers/:id/edit',
    name: 'server-edit',
    component: () => import('@/views/ServerForm.vue'),
    meta: {
      title: 'Editar Servidor',
    },
  },
  {
    path: '/ssh-keys',
    name: 'ssh-keys',
    component: () => import('@/views/SSHKeys.vue'),
    meta: {
      title: 'Llaves SSH',
    },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFound.vue'),
    meta: {
      title: 'Página No Encontrada',
    },
  },
]

/**
 * Crear instancia del router
 */
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

/**
 * Guard de navegación para actualizar el título de la página
 */
router.beforeEach((to, from, next) => {
  const title = to.meta.title as string | undefined
  if (title) {
    document.title = `${title} - Portal VPS`
  }
  next()
})

export default router
