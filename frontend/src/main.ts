/**
 * Punto de entrada principal de la aplicación Vue 3
 * 
 * Este archivo inicializa:
 * - Aplicación Vue
 * - Router
 * - Pinia (state management)
 * - TanStack Query (Vue Query)
 * - Toast notifications
 * - Bootstrap
 * - FontAwesome
 */

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { VueQueryPlugin } from '@tanstack/vue-query'
import Toast, { POSITION } from 'vue-toastification'
import App from './App.vue'
import router from './router'

// Estilos
import 'bootstrap/dist/css/bootstrap.min.css'
import '@fortawesome/fontawesome-free/css/all.min.css'
import 'vue-toastification/dist/index.css'
import './assets/main.css'

// Bootstrap JS (para componentes interactivos)
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

/**
 * Crear instancia de la aplicación
 */
const app = createApp(App)

/**
 * Configurar Pinia (State Management)
 */
const pinia = createPinia()
app.use(pinia)

/**
 * Configurar Vue Router
 */
app.use(router)

/**
 * Configurar Vue Query (TanStack Query)
 */
app.use(VueQueryPlugin, {
  queryClientConfig: {
    defaultOptions: {
      queries: {
        refetchOnWindowFocus: false,
        retry: 1,
        staleTime: 5 * 60 * 1000, // 5 minutos
      },
    },
  },
})

/**
 * Configurar Toast Notifications
 */
app.use(Toast, {
  position: POSITION.TOP_RIGHT,
  timeout: 5000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: 'button',
  icon: true,
  rtl: false,
  transition: 'Vue-Toastification__bounce',
  maxToasts: 5,
  newestOnTop: true,
})

/**
 * Montar la aplicación
 */
app.mount('#app')
