# Guía de Implementación UI - Dual Design System

## Introducción

Este proyecto implementa **dos diseños diferenciados**:

1. **Panel Administrativo:** Vuestic UI Framework (moderno, profesional)
2. **Vista Pública:** Gov.co Design System (Ministerio TIC Colombia)

---

## Arquitectura de Diseños

```
Portal VPS
│
├─ /admin/*           → Vuestic UI (Panel Administrativo)
│  ├─ Dashboard
│  ├─ Gestión Usuarios
│  ├─ Gestión Contenido
│  ├─ Analytics
│  └─ Configuración
│
└─ /                  → Gov.co Design (Vista Pública)
   ├─ Home
   ├─ Documentación
   ├─ Búsqueda
   └─ Login/Register
```

---

## 1. Panel Administrativo - Vuestic UI

### ¿Qué es Vuestic?

Vuestic UI es un framework de componentes Vue 3 diseñado específicamente para paneles administrativos profesionales.

**Características:**
- 40+ componentes preconstruidos
- Theming avanzado
- Responsive by default
- TypeScript support
- Composition API ready
- Accesibilidad WCAG 2.1

**Website:** https://vuestic.dev/

### Instalación

```bash
cd frontend

# Instalar Vuestic
npm install vuestic-ui

# Instalar dependencias de iconos
npm install @fortawesome/fontawesome-free
```

### Configuración

**main.ts:**
```typescript
import { createApp } from 'vue'
import { createVuestic } from 'vuestic-ui'
import 'vuestic-ui/styles/essential.css'
import 'vuestic-ui/styles/typography.css'

import App from './App.vue'
import router from './router'
import pinia from './stores'

const app = createApp(App)

// Configurar Vuestic
app.use(createVuestic({
  config: {
    colors: {
      variables: {
        // Colores personalizados para admin
        primary: '#2b6cb0',
        secondary: '#4299e1',
        success: '#48bb78',
        info: '#4299e1',
        danger: '#f56565',
        warning: '#ed8936',
      }
    },
    icons: {
      aliases: [
        {
          name: 'bell',
          to: 'fa-bell'
        },
        {
          name: 'user',
          to: 'fa-user'
        }
      ],
    },
  }
}))

app.use(router)
app.use(pinia)
app.mount('#app')
```

### Estructura de Layouts

**layouts/AdminLayout.vue:**
```vue
<template>
  <va-layout>
    <!-- Sidebar -->
    <template #sidebar>
      <va-sidebar v-model="sidebarVisible" :minimized="sidebarMinimized">
        <va-sidebar-item-content>
          <div class="va-title">Portal VPS Admin</div>
        </va-sidebar-item-content>
        
        <va-sidebar-item to="/admin/dashboard" icon="dashboard">
          Dashboard
        </va-sidebar-item>
        
        <va-sidebar-item to="/admin/documents" icon="file-text">
          Documentos
        </va-sidebar-item>
        
        <va-sidebar-item to="/admin/users" icon="users">
          Usuarios
        </va-sidebar-item>
        
        <va-sidebar-item to="/admin/categories" icon="folder">
          Categorías
        </va-sidebar-item>
        
        <va-sidebar-item to="/admin/analytics" icon="bar-chart">
          Analytics
        </va-sidebar-item>
        
        <va-sidebar-item to="/admin/settings" icon="cog">
          Configuración
        </va-sidebar-item>
      </va-sidebar>
    </template>
    
    <!-- Header -->
    <template #header>
      <va-navbar>
        <template #left>
          <va-button
            icon="menu"
            flat
            @click="sidebarMinimized = !sidebarMinimized"
          />
        </template>
        
        <template #right>
          <!-- Notificaciones -->
          <va-dropdown placement="bottom-end">
            <template #anchor>
              <va-button icon="bell" flat>
                <va-badge text="3" color="danger" overlap />
              </va-button>
            </template>
            
            <va-dropdown-content>
              <div class="notification-list">
                <div class="notification-item">
                  Nuevo documento publicado
                </div>
                <div class="notification-item">
                  Usuario registrado
                </div>
              </div>
            </va-dropdown-content>
          </va-dropdown>
          
          <!-- Usuario -->
          <va-dropdown placement="bottom-end">
            <template #anchor>
              <va-button>
                <va-avatar src="/avatar.jpg" size="small" />
                <span class="ml-2">{{ user.name }}</span>
              </va-button>
            </template>
            
            <va-dropdown-content>
              <va-list>
                <va-list-item to="/admin/profile">
                  <va-icon name="user" class="mr-2" />
                  Mi Perfil
                </va-list-item>
                <va-list-separator />
                <va-list-item @click="logout">
                  <va-icon name="sign-out" class="mr-2" />
                  Cerrar Sesión
                </va-list-item>
              </va-list>
            </va-dropdown-content>
          </va-dropdown>
        </template>
      </va-navbar>
    </template>
    
    <!-- Content -->
    <template #content>
      <main class="admin-content">
        <router-view />
      </main>
    </template>
  </va-layout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const sidebarVisible = ref(true)
const sidebarMinimized = ref(false)
const user = authStore.user

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.admin-content {
  padding: 1.5rem;
}

.notification-list {
  max-width: 300px;
  max-height: 400px;
  overflow-y: auto;
}

.notification-item {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid var(--va-background-border);
  cursor: pointer;
}

.notification-item:hover {
  background-color: var(--va-background-element);
}
</style>
```

### Componentes Admin Principales

**Dashboard.vue:**
```vue
<template>
  <div class="dashboard">
    <h1 class="va-h1">Dashboard</h1>
    
    <!-- Stats Cards -->
    <va-card-group>
      <va-card>
        <va-card-title>Total Usuarios</va-card-title>
        <va-card-content>
          <div class="stat-value">{{ stats.totalUsers }}</div>
          <div class="stat-trend">
            <va-icon name="trending-up" color="success" />
            +12% este mes
          </div>
        </va-card-content>
      </va-card>
      
      <va-card>
        <va-card-title>Documentos</va-card-title>
        <va-card-content>
          <div class="stat-value">{{ stats.totalDocuments }}</div>
          <div class="stat-trend">
            <va-icon name="trending-up" color="success" />
            +8% este mes
          </div>
        </va-card-content>
      </va-card>
      
      <va-card>
        <va-card-title>Visitas</va-card-title>
        <va-card-content>
          <div class="stat-value">{{ stats.totalViews }}</div>
          <div class="stat-trend">
            <va-icon name="trending-up" color="success" />
            +23% este mes
          </div>
        </va-card-content>
      </va-card>
    </va-card-group>
    
    <!-- Charts -->
    <va-card class="mt-4">
      <va-card-title>Actividad Reciente</va-card-title>
      <va-card-content>
        <!-- Chart component here -->
      </va-card-content>
    </va-card>
    
    <!-- Recent Documents Table -->
    <va-card class="mt-4">
      <va-card-title>Documentos Recientes</va-card-title>
      <va-card-content>
        <va-data-table
          :items="recentDocuments"
          :columns="columns"
        >
          <template #cell(actions)="{ row }">
            <va-button
              icon="edit"
              flat
              size="small"
              @click="editDocument(row)"
            />
            <va-button
              icon="trash"
              flat
              size="small"
              color="danger"
              @click="deleteDocument(row)"
            />
          </template>
        </va-data-table>
      </va-card-content>
    </va-card>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useDocumentStore } from '@/stores/documentStore'

const documentStore = useDocumentStore()

const stats = ref({
  totalUsers: 0,
  totalDocuments: 0,
  totalViews: 0,
})

const columns = [
  { key: 'title', label: 'Título' },
  { key: 'author', label: 'Autor' },
  { key: 'category', label: 'Categoría' },
  { key: 'status', label: 'Estado' },
  { key: 'actions', label: 'Acciones' },
]

const recentDocuments = ref([])

onMounted(async () => {
  // Fetch stats and documents
})
</script>

<style scoped>
.stat-value {
  font-size: 2rem;
  font-weight: bold;
  color: var(--va-primary);
}

.stat-trend {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
  font-size: 0.875rem;
}
</style>
```

---

## 2. Vista Pública - Gov.co Design System

### ¿Qué es Gov.co?

El Design System del Gobierno de Colombia (Gov.co) es el framework oficial para sitios web gubernamentales colombianos.

**Características:**
- Diseño estandarizado del gobierno
- Componentes accesibles
- Responsive design
- WCAG 2.1 AA compliance
- Multiidioma (ES/EN)

**CDN:** https://cdn.www.gov.co/v5/  
**Documentación:** https://www.gov.co/home/manualidentidad

### Instalación

```bash
cd frontend

# Instalar paquete Gov.co (si está disponible en npm)
# O usar CDN en index.html
```

**index.html:**
```html
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Gov.co Design System CSS -->
  <link rel="stylesheet" href="https://cdn.www.gov.co/v5/assets/css/govco.min.css">
  
  <!-- FontAwesome (Gov.co compatible) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <title>Portal VPS - Documentación</title>
</head>
<body>
  <div id="app"></div>
  
  <!-- Gov.co Design System JS -->
  <script src="https://cdn.www.gov.co/v5/assets/js/govco.min.js"></script>
  <script type="module" src="/src/main.ts"></script>
</body>
</html>
```

### Wrapper de Componentes Gov.co

**components/govco/GovHeader.vue:**
```vue
<template>
  <header class="govco-header">
    <div class="govco-header-top">
      <div class="container">
        <div class="govco-header-top-content">
          <a href="https://www.gov.co" target="_blank" class="govco-logo">
            <img src="https://cdn.www.gov.co/v5/assets/images/logo-govco.png" alt="Gov.co">
          </a>
          
          <nav class="govco-header-nav">
            <ul>
              <li><a href="https://www.gov.co">Portal del Estado Colombiano</a></li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
    
    <div class="govco-header-main">
      <div class="container">
        <div class="govco-header-main-content">
          <div class="govco-header-entity">
            <h1>{{ title }}</h1>
            <p>{{ subtitle }}</p>
          </div>
          
          <nav class="govco-header-menu">
            <ul>
              <li>
                <router-link to="/" active-class="active">
                  Inicio
                </router-link>
              </li>
              <li>
                <router-link to="/documentacion" active-class="active">
                  Documentación
                </router-link>
              </li>
              <li>
                <router-link to="/buscar" active-class="active">
                  Búsqueda
                </router-link>
              </li>
              <li v-if="!isAuthenticated">
                <router-link to="/login" active-class="active">
                  Iniciar Sesión
                </router-link>
              </li>
              <li v-else>
                <router-link to="/admin/dashboard" active-class="active">
                  Panel Admin
                </router-link>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'

interface Props {
  title?: string
  subtitle?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Portal de Configuración VPS',
  subtitle: 'Documentación técnica para administradores de sistemas'
})

const authStore = useAuthStore()
const isAuthenticated = computed(() => authStore.isAuthenticated)
</script>

<style scoped>
.govco-header {
  /* Gov.co styles are applied via CDN */
}
</style>
```

**components/govco/GovFooter.vue:**
```vue
<template>
  <footer class="govco-footer">
    <div class="govco-footer-main">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <h3>Portal VPS</h3>
            <p>Documentación técnica para configuración de servidores VPS</p>
          </div>
          
          <div class="col-md-4">
            <h4>Enlaces Útiles</h4>
            <ul class="govco-footer-links">
              <li><a href="/documentacion">Documentación</a></li>
              <li><a href="/buscar">Búsqueda</a></li>
              <li><a href="/contacto">Contacto</a></li>
            </ul>
          </div>
          
          <div class="col-md-4">
            <h4>Síguenos</h4>
            <div class="govco-social-links">
              <a href="#" aria-label="Facebook">
                <i class="fab fa-facebook"></i>
              </a>
              <a href="#" aria-label="Twitter">
                <i class="fab fa-twitter"></i>
              </a>
              <a href="#" aria-label="LinkedIn">
                <i class="fab fa-linkedin"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="govco-footer-bottom">
      <div class="container">
        <p>
          &copy; {{ currentYear }} Portal VPS - Todos los derechos reservados
        </p>
        <p>
          Desarrollado siguiendo los estándares de 
          <a href="https://www.gov.co" target="_blank">Gov.co</a>
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const currentYear = computed(() => new Date().getFullYear())
</script>
```

**layouts/PublicLayout.vue:**
```vue
<template>
  <div class="govco-layout">
    <GovHeader 
      title="Portal de Configuración VPS"
      subtitle="Documentación técnica para administradores de sistemas"
    />
    
    <main class="govco-main">
      <div class="container">
        <router-view />
      </div>
    </main>
    
    <GovFooter />
  </div>
</template>

<script setup lang="ts">
import GovHeader from '@/components/govco/GovHeader.vue'
import GovFooter from '@/components/govco/GovFooter.vue'
</script>

<style scoped>
.govco-main {
  min-height: calc(100vh - 200px);
  padding: 2rem 0;
}
</style>
```

---

## 3. Router Configuration

**router/index.ts:**
```typescript
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

// Layouts
import AdminLayout from '@/layouts/AdminLayout.vue'
import PublicLayout from '@/layouts/PublicLayout.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Public Routes (Gov.co Design)
    {
      path: '/',
      component: PublicLayout,
      children: [
        {
          path: '',
          name: 'home',
          component: () => import('@/views/public/Home.vue')
        },
        {
          path: 'documentacion',
          name: 'documentation',
          component: () => import('@/views/public/Documentation.vue')
        },
        {
          path: 'documentacion/:slug',
          name: 'document-detail',
          component: () => import('@/views/public/DocumentDetail.vue')
        },
        {
          path: 'buscar',
          name: 'search',
          component: () => import('@/views/public/Search.vue')
        },
        {
          path: 'login',
          name: 'login',
          component: () => import('@/views/auth/Login.vue')
        },
        {
          path: 'register',
          name: 'register',
          component: () => import('@/views/auth/Register.vue')
        }
      ]
    },
    
    // Admin Routes (Vuestic UI)
    {
      path: '/admin',
      component: AdminLayout,
      meta: { requiresAuth: true, requiresRole: ['editor', 'admin', 'superadmin'] },
      children: [
        {
          path: 'dashboard',
          name: 'admin-dashboard',
          component: () => import('@/views/admin/Dashboard.vue')
        },
        {
          path: 'documents',
          name: 'admin-documents',
          component: () => import('@/views/admin/Documents.vue')
        },
        {
          path: 'documents/create',
          name: 'admin-document-create',
          component: () => import('@/views/admin/DocumentForm.vue')
        },
        {
          path: 'documents/:id/edit',
          name: 'admin-document-edit',
          component: () => import('@/views/admin/DocumentForm.vue')
        },
        {
          path: 'users',
          name: 'admin-users',
          component: () => import('@/views/admin/Users.vue'),
          meta: { requiresRole: ['admin', 'superadmin'] }
        },
        {
          path: 'categories',
          name: 'admin-categories',
          component: () => import('@/views/admin/Categories.vue')
        },
        {
          path: 'analytics',
          name: 'admin-analytics',
          component: () => import('@/views/admin/Analytics.vue'),
          meta: { requiresRole: ['admin', 'superadmin'] }
        },
        {
          path: 'settings',
          name: 'admin-settings',
          component: () => import('@/views/admin/Settings.vue')
        }
      ]
    }
  ]
})

// Navigation Guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login', query: { redirect: to.fullPath } })
  } else if (to.meta.requiresRole) {
    const requiredRoles = to.meta.requiresRole as string[]
    const userRole = authStore.user?.role
    
    if (userRole && requiredRoles.includes(userRole)) {
      next()
    } else {
      next({ name: 'home' })
    }
  } else {
    next()
  }
})

export default router
```

---

## 4. Package.json Updates

```json
{
  "name": "vps-portal-frontend",
  "version": "2.0.0",
  "private": true,
  "scripts": {
    "dev": "vite",
    "build": "vue-tsc && vite build",
    "preview": "vite preview",
    "test:unit": "vitest",
    "lint": "eslint . --ext .vue,.js,.jsx,.cjs,.mjs,.ts,.tsx,.cts,.mts --fix"
  },
  "dependencies": {
    "vue": "^3.4.0",
    "vue-router": "^4.2.0",
    "pinia": "^2.1.0",
    "axios": "^1.6.0",
    
    "@tanstack/vue-query": "^5.17.0",
    "vee-validate": "^4.12.0",
    "yup": "^1.3.0",
    
    "bootstrap": "^5.3.0",
    "@fortawesome/fontawesome-free": "^6.5.0",
    "vue-toastification": "^2.0.0",
    
    "vuestic-ui": "^1.9.0",
    "marked": "^11.0.0",
    "dompurify": "^3.0.0"
  },
  "devDependencies": {
    "@vitejs/plugin-vue": "^5.0.0",
    "@vue/test-utils": "^2.4.0",
    "typescript": "^5.3.0",
    "vite": "^5.0.0",
    "vitest": "^1.1.0",
    "vue-tsc": "^1.8.0",
    
    "@typescript-eslint/eslint-plugin": "^6.0.0",
    "@typescript-eslint/parser": "^6.0.0",
    "eslint": "^8.56.0",
    "eslint-plugin-vue": "^9.19.0",
    "prettier": "^3.1.0",
    
    "sass": "^1.69.0",
    "sass-loader": "^13.3.0"
  }
}
```

---

## 5. Vite Configuration

**vite.config.ts:**
```typescript
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `
          @import "@/assets/styles/variables.scss";
          @import "@/assets/styles/mixins.scss";
        `
      }
    }
  },
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true
      }
    }
  }
})
```

---

## 6. Estilos Personalizados

**assets/styles/admin.scss:**
```scss
// Vuestic Admin customizations
.admin-content {
  background-color: var(--va-background-primary);
  
  .va-card {
    margin-bottom: 1.5rem;
  }
  
  .stat-card {
    text-align: center;
    padding: 2rem;
    
    .stat-value {
      font-size: 2.5rem;
      font-weight: bold;
      color: var(--va-primary);
    }
    
    .stat-label {
      font-size: 0.875rem;
      color: var(--va-text-secondary);
      margin-top: 0.5rem;
    }
  }
}
```

**assets/styles/public.scss:**
```scss
// Gov.co public site customizations
.govco-layout {
  font-family: 'Work Sans', sans-serif;
  
  .govco-main {
    .document-card {
      background: white;
      border-radius: 8px;
      padding: 1.5rem;
      margin-bottom: 1rem;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s;
      
      &:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
      }
      
      h3 {
        color: #004884; // Gov.co primary blue
        margin-bottom: 0.5rem;
      }
      
      .document-meta {
        color: #666;
        font-size: 0.875rem;
        margin-top: 0.5rem;
      }
    }
  }
}
```

---

## 7. Testing

**tests/admin/Dashboard.spec.ts:**
```typescript
import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import { createVuestic } from 'vuestic-ui'
import Dashboard from '@/views/admin/Dashboard.vue'

describe('Admin Dashboard', () => {
  it('renders with Vuestic components', () => {
    const wrapper = mount(Dashboard, {
      global: {
        plugins: [createVuestic()]
      }
    })
    
    expect(wrapper.find('.va-card').exists()).toBe(true)
  })
})
```

---

## Checklist de Implementación

### Vuestic Admin Panel
- [ ] Instalar vuestic-ui
- [ ] Configurar en main.ts
- [ ] Crear AdminLayout con sidebar
- [ ] Implementar Dashboard
- [ ] Crear vistas CRUD con componentes Vuestic
- [ ] Configurar theme colors
- [ ] Tests de componentes admin

### Gov.co Public Site
- [ ] Incluir CDN de Gov.co en index.html
- [ ] Crear componentes wrapper (GovHeader, GovFooter)
- [ ] Implementar PublicLayout
- [ ] Crear vistas públicas con Gov.co styles
- [ ] Verificar accesibilidad WCAG 2.1 AA
- [ ] Tests de componentes públicos

### Router y Guards
- [ ] Configurar rutas /admin/* para Vuestic
- [ ] Configurar rutas / para Gov.co
- [ ] Implementar navigation guards
- [ ] Redirecciones basadas en rol

### Estilos
- [ ] SCSS variables para ambos designs
- [ ] Separar estilos admin.scss y public.scss
- [ ] Responsive design en ambos
- [ ] Dark mode para admin (opcional)

---

**Última actualización:** 2026-02-17  
**Versión:** 2.0
