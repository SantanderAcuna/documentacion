# Frontend Admin - Panel Administrativo

Panel administrativo desarrollado con Vue 3, TypeScript y Vuestic UI.

## Requisitos

- Node.js 18.x LTS
- npm 9.x o pnpm 8.x

## Instalación

### Con Docker (Recomendado)
```bash
docker-compose up -d frontend-admin
```

### Sin Docker
```bash
npm install
cp .env.example .env
npm run dev
```

## Stack Tecnológico

- **Framework:** Vue 3 (Composition API)
- **Lenguaje:** TypeScript (strict mode)
- **UI Library:** Vuestic UI
- **Estado:** Pinia
- **HTTP Client:** Axios (withCredentials)
- **Cache/Query:** @tanstack/vue-query
- **Validación:** VeeValidate 4 + Yup
- **Routing:** Vue Router 4
- **Notificaciones:** Vue Toastification
- **Build Tool:** Vite

## Estructura

```
frontend-admin/
├── src/
│   ├── assets/              # Imágenes, fonts, etc.
│   ├── components/          # Componentes reutilizables
│   │   ├── common/         # Botones, inputs, etc.
│   │   ├── layout/         # Header, Sidebar, Footer
│   │   └── forms/          # Formularios complejos
│   ├── views/              # Páginas/Vistas
│   │   ├── auth/           # Login, Register
│   │   ├── dashboard/      # Dashboard principal
│   │   ├── contents/       # Gestión de contenidos
│   │   ├── users/          # Gestión de usuarios
│   │   └── settings/       # Configuración
│   ├── stores/             # Pinia stores
│   │   ├── auth.ts
│   │   ├── contents.ts
│   │   └── ui.ts
│   ├── composables/        # Composables reutilizables
│   │   ├── useApi.ts
│   │   ├── useAuth.ts
│   │   └── useNotification.ts
│   ├── router/             # Vue Router
│   │   ├── index.ts
│   │   └── guards.ts
│   ├── types/              # TypeScript types
│   │   ├── models.ts
│   │   └── api.ts
│   ├── utils/              # Utilidades
│   ├── App.vue
│   └── main.ts
├── public/
├── tests/
│   ├── unit/
│   └── e2e/
├── .env.example
├── package.json
├── tsconfig.json
└── vite.config.ts
```

## Scripts

```bash
# Desarrollo
npm run dev          # Inicia servidor de desarrollo

# Build
npm run build        # Build de producción
npm run preview      # Preview del build

# Testing
npm run test         # Ejecuta tests
npm run test:unit    # Tests unitarios
npm run test:e2e     # Tests end-to-end

# Linting
npm run lint         # ESLint
npm run type-check   # TypeScript check
```

## Configuración

### Variables de Entorno (.env)

```env
VITE_API_URL=http://localhost:8000/api/v1
VITE_APP_NAME=CMS Admin
VITE_APP_ENV=development
```

## Características Principales

### Autenticación
- Login con Sanctum cookies
- Recuperación de contraseña
- Gestión de sesión
- Guards en rutas protegidas

### Dashboard
- Métricas principales
- Gráficas interactivas
- Actividad reciente

### Gestión de Contenidos
- CRUD completo
- Editor WYSIWYG
- Multimedia
- Programación de publicaciones

### Gestión de Usuarios
- Listado con filtros
- Roles y permisos
- Auditoría de actividad

### Transparencia
- Información mínima obligatoria
- Reportes ITA y FURAG
- Datos abiertos

## Componentes Vuestic

Vuestic UI proporciona componentes listos para usar:

```vue
<script setup lang="ts">
import { VaButton, VaDataTable, VaModal } from 'vuestic-ui'
</script>

<template>
  <VaButton color="primary">Click me</VaButton>
  <VaDataTable :items="items" :columns="columns" />
</template>
```

Ver documentación: https://vuestic.dev/

## Testing

```bash
# Unit tests con Vitest
npm run test:unit

# E2E tests con Cypress
npm run test:e2e

# Cobertura
npm run test:coverage
```

## Accesibilidad

Aunque el admin no requiere WCAG 2.1 AA estricto, seguimos buenas prácticas:
- Navegación por teclado
- Labels en formularios
- Contraste adecuado
- ARIA labels cuando necesario

## Próximos Pasos

1. [ ] Configurar proyecto Vue 3 con Vite
2. [ ] Instalar Vuestic UI
3. [ ] Configurar Pinia stores
4. [ ] Implementar autenticación
5. [ ] Crear layout principal
6. [ ] Implementar dashboard
7. [ ] CRUD de contenidos
