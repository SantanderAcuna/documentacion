# Frontend Público - Sitio Web Gubernamental

Sitio web público desarrollado con Vue 3, TypeScript y diseño GOV.CO.

## Requisitos

- Node.js 18.x LTS
- npm 9.x o pnpm 8.x

## Instalación

### Con Docker (Recomendado)
```bash
docker-compose up -d frontend-public
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
- **Design System:** GOV.CO (MinTIC)
- **UI Library:** Bootstrap 5
- **Estilos:** SASS
- **Estado:** Pinia
- **HTTP Client:** Axios
- **Cache/Query:** @tanstack/vue-query
- **Validación:** VeeValidate 4 + Yup
- **Routing:** Vue Router 4 (mode: history)
- **Iconos:** FontAwesome 6 (FREE)
- **Notificaciones:** Vue Toastification
- **Build Tool:** Vite

## Estructura

```
frontend-public/
├── src/
│   ├── assets/
│   │   ├── images/
│   │   ├── fonts/
│   │   └── scss/
│   │       ├── _variables.scss    # Colores GOV.CO
│   │       ├── _mixins.scss
│   │       └── main.scss
│   ├── components/
│   │   ├── common/                # Botones, Cards, etc.
│   │   ├── layout/                # Header, Footer, Nav
│   │   ├── transparency/          # Componentes transparencia
│   │   └── pqrs/                  # PQRS components
│   ├── views/
│   │   ├── home/                  # Página principal
│   │   ├── noticias/              # Noticias
│   │   ├── transparencia/         # Transparencia activa
│   │   ├── pqrs/                  # Formulario PQRS
│   │   └── datos-abiertos/        # Open data
│   ├── stores/
│   ├── composables/
│   ├── router/
│   ├── types/
│   ├── utils/
│   ├── App.vue
│   └── main.ts
├── public/
│   ├── robots.txt
│   └── sitemap.xml
├── tests/
│   ├── unit/
│   ├── e2e/
│   └── a11y/                      # Tests de accesibilidad
├── .env.example
├── package.json
└── vite.config.ts
```

## Scripts

```bash
# Desarrollo
npm run dev

# Build
npm run build
npm run preview

# Testing
npm run test
npm run test:a11y       # Tests accesibilidad WCAG 2.1 AA

# Linting
npm run lint
```

## Diseño GOV.CO

Este frontend implementa el diseño oficial del Gobierno de Colombia.

### Paleta de Colores

```scss
// assets/scss/_variables.scss

// Colores Principales
$azul-institucional: #004884;
$amarillo-bandera: #FFD500;
$azul-bandera: #003DA5;
$rojo-bandera: #CE1126;

// Colores Neutros
$gris-oscuro: #2C2C2C;
$gris-medio: #6B6B6B;
$gris-claro: #F5F5F5;

// Textos (WCAG 2.1 AA compliant)
$texto-principal: #2C2C2C;      // Contraste 13.2:1
$texto-secundario: #6B6B6B;     // Contraste 5.7:1

// Enlaces
$enlace-normal: #004884;        // Contraste 8.6:1
$enlace-hover: #003366;         // Contraste 11.2:1

// Estados
$exito: #2E7D32;                // Contraste 5.2:1
$advertencia: #F57C00;          // Contraste 4.5:1
$error: #C62828;                // Contraste 6.8:1
$informacion: #1976D2;          // Contraste 4.6:1
```

### Tipografía

```scss
// Fuentes GOV.CO
$font-family-primary: 'Work Sans', -apple-system, sans-serif;
$font-family-secondary: 'Montserrat', sans-serif;

// Tamaños
$font-size-base: 16px;          // Base accesible
$line-height-normal: 1.5;       // Mínimo WCAG
```

### Componentes GOV.CO

Basado en https://cdn.www.gov.co/v5/

```vue
<template>
  <!-- Header GOV.CO -->
  <header class="govco-header">
    <div class="govco-header-top">
      <img src="/logo-govco.svg" alt="GOV.CO" />
    </div>
  </header>

  <!-- Footer GOV.CO -->
  <footer class="govco-footer">
    <!-- Contenido oficial -->
  </footer>
</template>
```

## Accesibilidad WCAG 2.1 AA

### Requisitos Obligatorios

- ✅ Contraste mínimo 4.5:1 (texto normal)
- ✅ Contraste mínimo 3:1 (texto grande)
- ✅ Navegación completa por teclado
- ✅ Textos alternativos en imágenes
- ✅ Labels en formularios
- ✅ Subtítulos en videos
- ✅ Lengua de Señas en alocuciones oficiales
- ✅ Sin trampas de teclado
- ✅ Validación de errores descriptiva

### Herramientas de Validación

```bash
# Axe DevTools
npm run test:a11y

# WAVE
# Manual en navegador

# Lighthouse
npm run lighthouse
```

### Ejemplo Componente Accesible

```vue
<template>
  <form @submit.prevent="onSubmit">
    <!-- Label asociado correctamente -->
    <label for="nombre">
      Nombre completo
      <span class="required" aria-label="campo obligatorio">*</span>
    </label>
    <input
      id="nombre"
      v-model="nombre"
      type="text"
      required
      aria-required="true"
      aria-describedby="nombre-error"
    />
    <span
      v-if="errors.nombre"
      id="nombre-error"
      role="alert"
      class="error"
    >
      {{ errors.nombre }}
    </span>

    <!-- Botón con texto descriptivo -->
    <button type="submit" aria-label="Enviar formulario de contacto">
      Enviar
    </button>
  </form>
</template>
```

## Performance

### Objetivos
- Google PageSpeed: >90
- First Contentful Paint: <1.5s
- Largest Contentful Paint: <2.5s
- Total Blocking Time: <200ms
- Cumulative Layout Shift: <0.1

### Optimizaciones
- Code splitting por ruta
- Lazy loading de imágenes
- Preconnect a dominios externos
- Minificación y compresión
- Tree shaking

## SEO

```vue
<!-- src/App.vue -->
<script setup lang="ts">
import { useHead } from '@vueuse/head'

useHead({
  title: 'Alcaldía - Gobierno Digital',
  meta: [
    { name: 'description', content: 'Portal oficial...' },
    { property: 'og:title', content: 'Alcaldía' },
    { property: 'og:type', content: 'website' },
  ],
})
</script>
```

## Próximos Pasos

1. [ ] Configurar proyecto Vue 3
2. [ ] Implementar diseño GOV.CO
3. [ ] Configurar paleta de colores
4. [ ] Crear componentes base accesibles
5. [ ] Implementar página principal
6. [ ] Sección de transparencia
7. [ ] Formulario PQRS
8. [ ] Tests de accesibilidad
9. [ ] Optimización de performance
