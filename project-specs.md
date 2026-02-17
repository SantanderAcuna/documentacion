# Especificaciones del Proyecto - Portal de Configuración VPS

## Información General del Proyecto

### Nombre del Proyecto
Portal de Configuración VPS

### Versión
2.0.0

### Fecha de Creación
2023

### Descripción
Aplicación web full-stack de documentación y gestión centralizada para administradores de sistemas que trabajan con servidores VPS. Plataforma dinámica con backend Laravel y frontend Vue.js que proporciona acceso rápido a guías, tutoriales, gestión de contenido y mejores prácticas para la configuración y mantenimiento de servidores.

### Objetivos del Proyecto
1. Centralizar documentación técnica de configuración VPS en plataforma dinámica
2. Facilitar el acceso rápido a información crítica mediante sistema de búsqueda avanzado
3. Proporcionar guías paso a paso con contenido administrable
4. Implementar sistema de autenticación y roles para gestión de contenido
5. Promover buenas prácticas de seguridad
6. Servir como referencia escalable para administradores de sistemas

---

## Alcance del Proyecto

### Funcionalidades Incluidas

#### 1. Sistema de Autenticación y Autorización
- Registro e inicio de sesión de usuarios
- Autenticación con Laravel Sanctum (cookies HTTP-Only)
- Sistema de roles y permisos con Spatie Permission
- Recuperación de contraseña
- Gestión de perfiles de usuario

#### 2. Portal de Inicio (Dashboard)
- Dashboard dinámico con Vue.js
- Tarjetas de acceso rápido a secciones principales
- Estadísticas de uso y contenido
- Contenido personalizado según rol
- Notificaciones en tiempo real

#### 3. Sistema de Navegación
- Menú lateral reactivo (sidebar) con Vue Router
- Navegación jerárquica por secciones
- Breadcrumbs dinámicos
- Diseño responsive para móviles
- Transiciones suaves entre vistas

#### 4. Gestión de Documentación Técnica (CRUD)
- **Backend (Laravel API)**
  - Endpoints REST para documentación
  - Validación de datos con Form Requests
  - Relaciones Eloquent para categorías y tags
  - Búsqueda full-text con MySQL
  - Cache de queries con Redis
  
- **Frontend (Vue.js)**
  - Editor de markdown con vista previa
  - Gestión de categorías y tags
  - Carga de imágenes con upload
  - Versionamiento de documentos
  - Historial de cambios

#### 5. Categorías de Documentación
- **Configuración SSH**
  - Generación de claves SSH
  - Configuración de archivo SSH config
  - Mejores prácticas de seguridad
  
- **Seguridad del Servidor**
  - Configuración de firewall UFW
  - Instalación y configuración de Fail2Ban
  - Hardening de SSH
  - Actualizaciones de seguridad
  
- **Servicios Web**
  - Instalación y configuración de Nginx
  - Configuración de MySQL/MariaDB
  - Certificados SSL con Let's Encrypt
  - Optimización de rendimiento

#### 6. Búsqueda Avanzada
- Búsqueda full-text en contenido
- Filtros por categoría, tags, fecha
- Autocompletado con Vue Query
- Resultados paginados
- Historial de búsquedas

#### 7. Sistema de Favoritos
- Marcar/desmarcar documentos
- Sincronización con backend
- Vista de favoritos personalizada
- Organización en colecciones

#### 8. Panel de Administración
- Gestión de usuarios y roles
- Gestión de contenido (aprobar/rechazar)
- Estadísticas y analytics
- Configuración del sistema
- Logs de actividad

### Funcionalidades Excluidas (v1.0)
- Comentarios y discusiones
- Sistema de votación de documentos
- Integración con Git para versionamiento
- API pública para terceros
- Exportación masiva de documentación
- Modo colaborativo en tiempo real

---

## Arquitectura del Sistema

### Tipo de Aplicación
Aplicación web full-stack SPA (Single Page Application) con arquitectura cliente-servidor

### Arquitectura General
```
┌─────────────────────────────────────────────────────────────────┐
│                         ARQUITECTURA                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│ FRONTEND (Vue.js 3 + TypeScript)                                │
│ ├─ Framework: Vue.js 3 (Composition API)                       │
│ ├─ Lenguaje: TypeScript (strict mode)                          │
│ ├─ Cliente HTTP: Axios (withCredentials)                       │
│ ├─ Estado: Pinia (stores modulares)                            │
│ ├─ Queries: Vue Query (@tanstack/vue-query)                    │
│ ├─ Validación: VeeValidate 4 + Yup (esquemas tipados)          │
│ ├─ Enrutamiento: Vue Router 4 (mode history)                   │
│ ├─ UI: Bootstrap 5 + SASS personalizado                        │
│ ├─ Iconos: FontAwesome 6 (FREE únicamente)                     │
│ └─ Notificaciones: Vue Toastification                          │
│                                                                  │
│ BACKEND (Laravel 12 + PHP 8.3.1+)                               │
│ ├─ Framework: Laravel 12 (PHP 8.3.1+)                          │
│ ├─ Base de Datos: MySQL 8.0+ (InnoDB, utf8mb4)                 │
│ ├─ Autenticación: Laravel Sanctum (cookies HTTP-Only)          │
│ ├─ Autorización: Spatie Permission (RBAC dinámico)             │
│ ├─ Almacenamiento: Discos dinámicos (local/S3)                 │
│ ├─ Caché: Redis (sesiones, queries)                            │
│ ├─ Queue: Redis (jobs asíncronos)                              │
│ └─ API: RESTful (JSON responses)                               │
│                                                                  │
│ INFRAESTRUCTURA                                                  │
│ ├─ Contenedores: Docker + Docker Compose                       │
│ ├─ Orquestación: Kubernetes (producción)                       │
│ ├─ Servidor Web: Nginx (reverse proxy)                         │
│ ├─ CI/CD: GitHub Actions                                       │
│ ├─ Versionamiento: Git (GitHub)                                │
│ └─ Monitoreo: ELK Stack / Laravel Pulse                        │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### Flujo de Comunicación
```
Cliente (Browser)
    ↓ HTTP/HTTPS
Nginx (Reverse Proxy)
    ↓
    ├→ Frontend (Vue.js SPA) → Assets estáticos
    └→ Backend (Laravel API) → /api/*
           ↓
       MySQL 8.0+ (Datos)
       Redis (Cache/Sessions/Queue)
       S3/Local (Archivos)
```

### Tecnologías Detalladas

#### Backend (Laravel 12)
**Framework y Core:**
- Laravel 12.x (PHP 8.3.1+)
- Composer para gestión de dependencias
- PSR-4 Autoloading

**Base de Datos:**
- MySQL 8.0+ (motor InnoDB, charset utf8mb4)
- Migraciones con Laravel Migrations
- Seeders para datos iniciales
- Eloquent ORM para queries

**Autenticación y Autorización:**
- Laravel Sanctum (cookies HTTP-Only, stateless tokens)
- Spatie Laravel-Permission (RBAC dinámico)
- Middleware de autenticación personalizado
- Guards y Policies

**Almacenamiento:**
- Laravel Filesystem (discos dinámicos)
- Local storage (desarrollo)
- S3 compatible (producción)
- Validación de tipos de archivo

**Caché y Performance:**
- Redis para cache de queries
- Redis para sesiones
- Redis para queue de jobs
- Cache tags y invalidación

**API:**
- RESTful architecture
- API Resources para transformación
- Form Requests para validación
- Rate limiting
- CORS configurado

**Paquetes Clave:**
- spatie/laravel-permission
- predis/predis (Redis client)
- intervention/image (procesamiento imágenes)
- laravel/sanctum
- laravel/pulse (monitoreo)

#### Frontend (Vue.js 3 + TypeScript)
**Framework:**
- Vue.js 3.4+ (Composition API)
- TypeScript 5+ (strict mode)
- Vite 5+ (build tool)

**Estado y Datos:**
- Pinia (state management)
- Vue Query / TanStack Query (server state)
- Axios (HTTP client, withCredentials)

**Enrutamiento:**
- Vue Router 4 (history mode)
- Guards de navegación
- Lazy loading de componentes
- Meta tags dinámicos

**Validación:**
- VeeValidate 4 (formularios)
- Yup (esquemas de validación)
- Validación asíncrona
- Mensajes personalizados

**UI/UX:**
- **Panel Admin:** Vuestic UI 1.9+ (componentes admin profesionales)
- **Vista Pública:** Gov.co Design System v5 (Ministerio TIC Colombia)
- Bootstrap 5.3+ (base framework)
- SASS/SCSS personalizado
- FontAwesome 6 FREE
- Vue Toastification (notificaciones)
- Transiciones Vue

**Herramientas:**
- ESLint + Prettier (code style)
- Vitest (testing)
- TypeScript compiler
- Vite plugin Vue

#### Infraestructura
**Plataforma Cloud:**
- Proveedor: DigitalOcean
- Sistema Operativo: Ubuntu 24.04 LTS (Noble Numbat)
- Droplets: App, Database, Cache
- Managed Databases: MySQL 8.0+
- Managed Redis: Cache y Queue
- Spaces: Object Storage (S3-compatible)
- Load Balancers: DigitalOcean LB
- Firewall: DigitalOcean Cloud Firewall

**Desarrollo Local:**
- Docker Compose
- Servicios: nginx, php-fpm, mysql, redis, node
- Volúmenes para persistencia
- Hot reload habilitado

**Producción (DigitalOcean):**
- **App Droplet(s)**: Ubuntu 24.04, Nginx, PHP-FPM 8.3
- **Database Droplet**: DigitalOcean Managed MySQL 8.0+
- **Cache Droplet**: DigitalOcean Managed Redis
- **Storage**: DigitalOcean Spaces (S3-compatible)
- **Kubernetes (opcional)**: DigitalOcean Kubernetes (DOKS)
- **Backups**: Automated snapshots
- **CDN**: DigitalOcean CDN (opcional)

**CI/CD:**
- GitHub Actions
- Workflows: test, build, deploy
- Environments: dev, staging, production
- Docker registry: DigitalOcean Container Registry
- Deploy to DigitalOcean App Platform o Droplets
- Automated testing

**Monitoreo:**
- Laravel Pulse (métricas app)
- DigitalOcean Monitoring (droplet metrics)
- ELK Stack (logs centralizado, opcional)
- Uptime monitoring
- Alerts y notificaciones

### Estructura de Archivos
```
documentacion/
├── backend/                    # Laravel 12 API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/   # API Controllers
│   │   │   ├── Requests/      # Form Requests
│   │   │   └── Resources/     # API Resources
│   │   ├── Models/            # Eloquent Models
│   │   ├── Policies/          # Authorization Policies
│   │   └── Services/          # Business Logic
│   ├── config/                # Configuración
│   ├── database/
│   │   ├── migrations/        # Database migrations
│   │   └── seeders/           # Database seeders
│   ├── routes/
│   │   ├── api.php           # API routes
│   │   └── web.php           # Web routes
│   ├── storage/              # Storage (uploads, logs)
│   ├── tests/                # PHPUnit tests
│   ├── .env.example          # Environment template
│   ├── composer.json         # PHP dependencies
│   └── artisan               # Laravel CLI
│
├── frontend/                  # Vue.js 3 + TypeScript
│   ├── src/
│   │   ├── assets/           # Static assets
│   │   ├── components/       # Vue components
│   │   │   ├── common/       # Componentes reutilizables
│   │   │   ├── layout/       # Layout components
│   │   │   └── features/     # Feature components
│   │   ├── composables/      # Vue composables
│   │   ├── router/           # Vue Router config
│   │   ├── stores/           # Pinia stores
│   │   ├── services/         # API services
│   │   ├── types/            # TypeScript types
│   │   ├── utils/            # Utilidades
│   │   ├── views/            # Page views
│   │   ├── App.vue           # Root component
│   │   └── main.ts           # Entry point
│   ├── public/               # Public assets
│   ├── tests/                # Vitest tests
│   ├── .env.example          # Environment template
│   ├── package.json          # Node dependencies
│   ├── tsconfig.json         # TypeScript config
│   └── vite.config.ts        # Vite config
│
├── docker/                    # Docker configuration
│   ├── nginx/
│   │   └── default.conf      # Nginx config
│   ├── php/
│   │   └── Dockerfile        # PHP-FPM Dockerfile
│   └── mysql/
│       └── init.sql          # MySQL init script
│
├── .github/
│   └── workflows/
│       ├── test.yml          # Testing workflow
│       ├── build.yml         # Build workflow
│       └── deploy.yml        # Deploy workflow
│
├── deployment/                # Deployment configs
│   ├── digitalocean/
│   │   ├── app.yaml          # DO App Platform
│   │   └── setup.sh          # Droplet setup script
│   └── kubernetes/           # K8s configs (opcional)
│       ├── deployment.yaml
│       └── service.yaml
│
├── docs/                      # Documentación del proyecto
│   ├── README.md             # Documentación principal
│   ├── user-stories.md       # Historias de usuario
│   ├── tasks.md              # Tareas del proyecto
│   ├── business-rules.md     # Reglas de negocio
│   ├── project-specs.md      # Este documento
│   ├── DOCUMENTATION_SUMMARY.md
│   └── DOCUMENTATION_INDEX.md
│
├── docker-compose.yml         # Desarrollo local
├── .env.example              # Environment template
└── README.md                 # Readme principal
```

---

## Diseño y UX

### Arquitectura de Diseños Dual

El proyecto implementa **dos diseños diferenciados** según el contexto del usuario:

#### 1. Vista Pública (Gov.co Design System)
**URL:** `/`, `/documentacion`, `/buscar`  
**Framework:** Gov.co v5 (Ministerio TIC Colombia)

**Características:**
- Diseño oficial del gobierno colombiano
- Accesibilidad WCAG 2.1 AA compliance
- CDN: https://cdn.www.gov.co/v5/
- Responsive y mobile-first
- Componentes estandarizados

**Paleta de Colores Gov.co:**
```css
--govco-primary: #004884    /* Azul gobierno */
--govco-secondary: #3366CC  /* Azul secundario */
--govco-text: #333333       /* Texto principal */
--govco-background: #F5F5F5 /* Fondo */
--govco-white: #FFFFFF      /* Blanco */
```

**Componentes Principales:**
- Header oficial con logo Gov.co
- Navegación estandarizada
- Footer con enlaces institucionales
- Cards de documentación
- Breadcrumbs
- Buscador integrado

#### 2. Panel Administrativo (Vuestic UI)
**URL:** `/admin/*`  
**Framework:** Vuestic UI 1.9+

**Características:**
- Framework moderno para admin panels
- 40+ componentes preconstruidos
- Theming avanzado
- Dark mode ready
- TypeScript first

**Paleta de Colores Admin:**
```css
--va-primary: #2b6cb0       /* Azul principal */
--va-secondary: #4299e1     /* Azul claro */
--va-success: #48bb78       /* Verde éxito */
--va-danger: #f56565        /* Rojo error */
--va-warning: #ed8936       /* Naranja advertencia */
--va-info: #4299e1          /* Azul info */
```

**Componentes Principales:**
- Sidebar con navegación jerárquica
- Dashboard con stats cards
- Data tables con sorting/filtering
- Forms con validación
- Modals y dropdowns
- Charts y analytics
- Notificaciones toast

### Responsive Breakpoints
```css
--primary-dark: #1a365d      /* Azul oscuro principal */
--primary-blue: #2b6cb0      /* Azul principal */
--primary-light-blue: #4299e1 /* Azul claro */
--primary-light: #f7fafc     /* Fondo claro */
--accent-orange: #ed8936     /* Color de acento */
--sidebar-bg: #1a202c        /* Fondo del sidebar */
--sidebar-text: #e2e8f0      /* Texto del sidebar */
--sidebar-hover: #2d3748     /* Hover en sidebar */
--card-bg: #ffffff           /* Fondo de tarjetas */
```

### Tipografía
- **Fuente Principal**: "Segoe UI", system-ui, -apple-system, sans-serif
- **Tamaños**:
  - Títulos principales: 2.2rem
  - Títulos de sección: 1.5rem
  - Títulos de tarjetas: 1.2rem
  - Texto normal: 1rem (16px base)
  - Texto pequeño: 0.9rem

### Responsive Breakpoints
- **Desktop**: > 992px (sidebar visible)
- **Tablet**: 768px - 992px (sidebar colapsable)
- **Mobile**: < 768px (sidebar oculto, menú hamburguesa)
- **Small Mobile**: < 480px (ajustes adicionales)

### Componentes UI

#### Sidebar (Vue Component)
- Componente: `LayoutSidebar.vue`
- Ancho: 280px (desktop)
- Posición: Fixed con Vue Router
- Estado: Pinia store para colapsar/expandir
- Transiciones: Vue transitions
- Categorías: Dinámicas desde API

#### Tarjetas (Vue Component)
- Componente: `DashboardCard.vue`
- Props: title, description, icon, color
- Bordes redondeados: 12px
- Sombra: Bootstrap shadow utilities
- Efecto hover: CSS transitions + Vue directives
- Data binding: Props + emits

#### Botones
- Bootstrap 5 button classes
- Variantes: primary, outline-primary, warning, info
- Componente: `BaseButton.vue` (wrapper)
- Loading states con spinner
- Disabled states

#### Formularios
- VeeValidate 4 + Yup
- Componentes: `FormInput.vue`, `FormSelect.vue`, `FormTextarea.vue`
- Validación en tiempo real
- Mensajes de error tipados
- Estados: pristine, dirty, valid, invalid

#### Modales
- Bootstrap 5 modals
- Componente: `BaseModal.vue`
- Teleport para portal
- Composable: `useModal()`
- Confirmaciones y alertas

#### Notificaciones
- Vue Toastification
- Tipos: success, error, warning, info
- Posición: top-right
- Auto-dismiss configurable
- Service: `notificationService.ts`

---

## Requisitos Funcionales

### RF-001: Autenticación de Usuarios
El sistema debe permitir registro, login y logout de usuarios mediante Laravel Sanctum con cookies HTTP-Only.

### RF-002: Gestión de Roles y Permisos
El sistema debe implementar RBAC con Spatie Permission permitiendo roles: Super Admin, Admin, Editor, Viewer.

### RF-003: CRUD de Documentación
El sistema debe permitir crear, leer, actualizar y eliminar documentación mediante API REST y formularios Vue.

### RF-004: Navegación Dinámica
El sistema debe permitir navegar entre diferentes secciones mediante Vue Router con lazy loading.

### RF-005: Búsqueda Avanzada
El sistema debe implementar búsqueda full-text con filtros, autocompletado y paginación.

### RF-006: Sistema de Favoritos
El sistema debe permitir marcar documentos como favoritos con sincronización en tiempo real.

### RF-007: Dashboard Personalizado
El sistema debe mostrar un dashboard con estadísticas y accesos rápidos según el rol del usuario.

### RF-008: Gestión de Archivos
El sistema debe permitir subir y gestionar imágenes/archivos con validación y almacenamiento en S3/local.

### RF-009: Versionamiento
El sistema debe mantener historial de cambios en documentos con posibilidad de restaurar versiones.

### RF-010: Notificaciones
El sistema debe mostrar notificaciones toast para acciones de usuario (éxito, error, advertencia).

---

## Requisitos No Funcionales

### RNF-001: Rendimiento
- Tiempo de carga inicial: < 2 segundos (First Contentful Paint)
- Tiempo de respuesta API: < 200ms (95 percentil)
- Consultas DB optimizadas con índices y eager loading
- Cache Redis para queries frecuentes

### RNF-002: Compatibilidad
- Navegadores: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- Dispositivos: Desktop, Tablet, Smartphone
- Resoluciones: Desde 320px hasta 4K
- TypeScript strict mode sin errores

### RNF-003: Seguridad
- HTTPS obligatorio en producción
- CSRF protection habilitado
- XSS prevention con sanitización
- SQL Injection prevention con prepared statements
- Rate limiting en API endpoints
- Cookies HTTP-Only y Secure
- Headers de seguridad (HSTS, CSP, etc.)

### RNF-004: Escalabilidad
- Arquitectura stateless para horizontal scaling
- Redis para sesiones compartidas
- Queue jobs para tareas pesadas
- CDN para assets estáticos
- Database connection pooling
- Lazy loading de componentes Vue

### RNF-005: Mantenibilidad
- Código TypeScript tipado estrictamente
- PSR-12 coding standards en PHP
- Componentes Vue reutilizables
- Tests unitarios (coverage > 70%)
- Documentación inline (PHPDoc, JSDoc)
- Migrations versionadas

### RNF-006: Disponibilidad
- Uptime: 99.5% (SLA DigitalOcean)
- Backups automáticos diarios
- Health checks configurados
- Rollback strategy definida
- Monitoring con alertas
- Error logging centralizado

### RNF-007: Usabilidad
- Interfaz intuitiva sin necesidad de tutorial
- Máximo 3 clics para llegar a cualquier sección
- Feedback visual en todas las acciones
- Mensajes de error claros y accionables
- Shortcuts de teclado en acciones comunes

### RNF-008: Accesibilidad
- WCAG 2.1 Level AA compliance
- HTML semántico
- ARIA labels donde necesario
- Contraste de colores adecuado
- Navegación por teclado funcional
- Screen reader compatible

---

## Plan de Implementación

### Fase 1: Configuración Base ⏳ Pendiente (Sprint 1-2)
- [ ] Setup repositorio y estructura de carpetas
- [ ] Configuración Docker Compose para desarrollo
- [ ] Setup Laravel 12 con PHP 8.3
- [ ] Setup Vue 3 + TypeScript + Vite
- [ ] Configuración MySQL 8.0 y Redis
- [ ] Configuración CI/CD con GitHub Actions
- [ ] Setup DigitalOcean: Droplets, Managed DB, Spaces

**Estimación:** 2 semanas

### Fase 2: Backend Core ⏳ Pendiente (Sprint 3-5)
- [ ] Implementar autenticación con Sanctum
- [ ] Configurar Spatie Permission (roles y permisos)
- [ ] Crear modelos Eloquent (User, Document, Category, Tag)
- [ ] Implementar migraciones y seeders
- [ ] Crear API Controllers y Resources
- [ ] Implementar Form Requests para validación
- [ ] Configurar almacenamiento (local/S3)
- [ ] Implementar cache con Redis
- [ ] Setup Queue system

**Estimación:** 3 semanas

### Fase 3: Frontend Core ⏳ Pendiente (Sprint 6-8)
- [ ] Estructura de componentes Vue
- [ ] Configuración Pinia stores
- [ ] Setup Vue Router con guards
- [ ] Implementar layout principal (sidebar, header)
- [ ] Crear composables reutilizables
- [ ] Setup Axios con interceptors
- [ ] Implementar VeeValidate + Yup
- [ ] Configurar Vue Query
- [ ] Sistema de notificaciones

**Estimación:** 3 semanas

### Fase 4: Funcionalidades Principales ⏳ Pendiente (Sprint 9-12)
- [ ] Sistema de login/registro (frontend + backend)
- [ ] Dashboard con estadísticas
- [ ] CRUD de documentación completo
- [ ] Editor markdown con preview
- [ ] Gestión de categorías y tags
- [ ] Upload de imágenes/archivos
- [ ] Sistema de búsqueda avanzada
- [ ] Sistema de favoritos
- [ ] Versionamiento de documentos

**Estimación:** 4 semanas

### Fase 5: Panel de Administración ⏳ Pendiente (Sprint 13-14)
- [ ] Gestión de usuarios
- [ ] Gestión de roles y permisos
- [ ] Moderación de contenido
- [ ] Analytics y estadísticas
- [ ] Configuración del sistema
- [ ] Logs de actividad

**Estimación:** 2 semanas

### Fase 6: Testing y QA ⏳ Pendiente (Sprint 15-16)
- [ ] Tests unitarios backend (PHPUnit)
- [ ] Tests unitarios frontend (Vitest)
- [ ] Tests de integración API
- [ ] Tests E2E (Cypress/Playwright)
- [ ] Code coverage > 70%
- [ ] Security audit
- [ ] Performance testing
- [ ] Accessibility audit

**Estimación:** 2 semanas

### Fase 7: Deployment y Optimización ⏳ Pendiente (Sprint 17-18)
- [ ] Setup producción en DigitalOcean
- [ ] Configuración Nginx optimizado
- [ ] SSL certificates (Let's Encrypt)
- [ ] CDN configuration
- [ ] Database optimization e índices
- [ ] Redis cache tuning
- [ ] Monitoring setup (Laravel Pulse, DO Monitoring)
- [ ] Backup strategy
- [ ] Documentation deployment

**Estimación:** 2 semanas

**Total estimado:** 18 semanas (4.5 meses)

## Deployment en DigitalOcean

### Arquitectura de Producción

#### Opción 1: DigitalOcean App Platform (Recomendado para MVP)
```
DigitalOcean App Platform
├─ Frontend (Static Site)
│  └─ Build: npm run build
│  └─ Output: dist/
├─ Backend (Web Service)
│  └─ PHP 8.3 + Nginx
│  └─ Laravel 12
├─ Database
│  └─ Managed MySQL 8.0+ (DO Managed Database)
├─ Cache
│  └─ Managed Redis (DO Managed Database)
└─ Storage
   └─ DigitalOcean Spaces (S3-compatible)
```

#### Opción 2: Droplets (Mayor control)
```
Load Balancer (DO Load Balancer)
    ↓
┌─────────────────────────────────┐
│ App Droplets (2+)               │
│ - Ubuntu 24.04 LTS              │
│ - Nginx                         │
│ - PHP-FPM 8.3                   │
│ - Node.js 20 (build frontend)   │
│ - Supervisor (queue workers)    │
└─────────────────────────────────┘
    ↓
┌─────────────────────────────────┐
│ Managed MySQL 8.0+              │
│ - Automated backups             │
│ - High availability             │
│ - Connection pooling            │
└─────────────────────────────────┘
    ↓
┌─────────────────────────────────┐
│ Managed Redis                   │
│ - Cache                         │
│ - Sessions                      │
│ - Queue                         │
└─────────────────────────────────┘
    ↓
┌─────────────────────────────────┐
│ DigitalOcean Spaces             │
│ - Uploaded files                │
│ - Static assets                 │
│ - CDN enabled                   │
└─────────────────────────────────┘
```

### Setup en Ubuntu 24.04

#### 1. Preparación del Droplet
```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependencias base
sudo apt install -y curl git unzip software-properties-common

# Instalar PHP 8.3
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-redis \
    php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl \
    php8.3-gd php8.3-zip php8.3-intl

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalar Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Instalar Nginx
sudo apt install -y nginx

# Instalar Supervisor (para queue workers)
sudo apt install -y supervisor
```

#### 2. Configuración Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/app/backend/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    
    charset utf-8;
    
    # Frontend SPA
    location / {
        try_files $uri $uri/ /index.html;
    }
    
    # Backend API
    location /api {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### 3. Configuración Supervisor (Queue Workers)
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/app/backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/app/backend/storage/logs/worker.log
stopwaitsecs=3600
```

#### 4. Deploy con GitHub Actions
```yaml
name: Deploy to DigitalOcean

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Deploy to Droplet
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.DO_HOST }}
          username: ${{ secrets.DO_USERNAME }}
          key: ${{ secrets.DO_SSH_KEY }}
          script: |
            cd /var/www/app
            git pull origin main
            
            # Backend
            cd backend
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            
            # Frontend
            cd ../frontend
            npm ci
            npm run build
            
            # Restart services
            sudo supervisorctl restart laravel-worker:*
            sudo systemctl reload php8.3-fpm
            sudo systemctl reload nginx
```

### DigitalOcean Managed Services

#### Managed MySQL
- **Tamaño inicial:** 1 GB RAM, 10 GB disk
- **Escalable:** Hasta 32 GB RAM
- **Features:**
  - Backups automáticos diarios
  - Point-in-time recovery
  - High availability (standby node)
  - Connection pooling
  - Monitoring integrado

#### Managed Redis
- **Tamaño inicial:** 1 GB RAM
- **Uso:**
  - Cache de queries
  - Sesiones de usuario
  - Queue de jobs
- **Features:**
  - Eviction policy configurada
  - Persistencia RDB + AOF
  - Monitoring integrado

#### DigitalOcean Spaces
- **Storage:** S3-compatible
- **CDN:** Integrado (espacios.cdn.digitaloceanspaces.com)
- **Uso:**
  - Uploads de usuarios
  - Assets estáticos
  - Backups de base de datos

### Costos Estimados (DigitalOcean)

#### Opción MVP (App Platform)
- App Platform: $12/mes (básico)
- Managed MySQL: $15/mes (1GB)
- Managed Redis: $15/mes (1GB)
- Spaces: $5/mes (250GB)
- **Total:** ~$47/mes

#### Opción Droplets
- Droplets (2x): $24/mes ($12 cada uno)
- Load Balancer: $12/mes
- Managed MySQL: $15/mes
- Managed Redis: $15/mes
- Spaces: $5/mes
- **Total:** ~$71/mes

### Monitoreo y Alertas

#### DigitalOcean Monitoring
- CPU, Memory, Disk usage
- Network throughput
- Custom metrics
- Alertas por email/Slack

#### Laravel Pulse
- Request rate y duration
- Queue jobs
- Cache hit rate
- Database queries
- Exceptions y slow queries

#### Logs
- Nginx access/error logs
- PHP-FPM logs
- Laravel logs (storage/logs)
- Supervisor logs

### Métricas Técnicas
- ✅ Tiempo de carga < 3 segundos
- ✅ Responsive en todos los breakpoints
- ✅ Sin errores de validación HTML/CSS
- ⏳ 100% de enlaces funcionales
- ⏳ Lighthouse score > 90

### Métricas de Contenido
- ⏳ 100% de secciones documentadas
- ⏳ Ejemplos funcionales en todas las guías
- ⏳ Capturas de pantalla cuando sean útiles

### Métricas de Usabilidad
- ✅ Navegación intuitiva
- ✅ Diseño consistente
- ⏳ Búsqueda funcional (cuando se implemente)

---

## Riesgos y Mitigación

### Riesgo 1: Contenido Desactualizado
**Probabilidad**: Media  
**Impacto**: Alto  
**Mitigación**: 
- Revisar documentación cada 6 meses
- Indicar fecha de última actualización
- Mantener changelog

### Riesgo 2: Incompatibilidad de Navegadores
**Probabilidad**: Baja  
**Impacto**: Medio  
**Mitigación**: 
- Uso de Bootstrap 5 (amplia compatibilidad)
- Testing en navegadores principales
- Uso de CSS estándar

### Riesgo 3: Dependencias CDN No Disponibles
**Probabilidad**: Baja  
**Impacto**: Alto  
**Mitigación**: 
- Usar CDN confiables (jsDelivr, cdnjs)
- Considerar fallback local
- Monitorear disponibilidad

### Riesgo 4: Escalabilidad del Contenido
**Probabilidad**: Media  
**Impacto**: Medio  
**Mitigación**: 
- Organización clara por categorías
- Índices y tabla de contenidos
- Sistema de búsqueda planificado

---

## Mantenimiento y Soporte

### Responsabilidades de Mantenimiento
- Actualización de contenido técnico
- Corrección de errores
- Implementación de nuevas funcionalidades
- Testing y validación
- Control de versiones

### Ciclo de Actualización
- **Revisiones Menores**: Según sea necesario (correcciones, mejoras)
- **Revisiones Mayores**: Cada 6 meses (contenido, tecnologías)
- **Auditoría de Seguridad**: Anual

### Documentación de Cambios
- Commits descriptivos en Git
- Changelog para versiones mayores
- Documentación de decisiones técnicas importantes

---

## Glosario

**VPS**: Virtual Private Server - Servidor privado virtual

**SSH**: Secure Shell - Protocolo de red criptográfico

**Nginx**: Servidor web y proxy inverso de alto rendimiento

**UFW**: Uncomplicated Firewall - Firewall de Ubuntu

**Fail2Ban**: Software de prevención de intrusiones

**SSL/TLS**: Secure Sockets Layer / Transport Layer Security - Protocolos de seguridad

**Let's Encrypt**: Autoridad de certificación gratuita y automatizada

**Responsive Design**: Diseño adaptable a diferentes tamaños de pantalla

**CDN**: Content Delivery Network - Red de distribución de contenido

---

## Referencias

### Documentación Técnica
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/)
- [Bootstrap Icons](https://icons.getbootstrap.com/)
- [MDN Web Docs](https://developer.mozilla.org/)

### Mejores Prácticas
- [Web Content Accessibility Guidelines (WCAG)](https://www.w3.org/WAI/WCAG21/quickref/)
- [HTML5 Specification](https://html.spec.whatwg.org/)
- [CSS Guidelines](https://cssguidelin.es/)

---

## Apéndices

### Apéndice A: Estructura HTML Típica
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal VPS</title>
    <!-- Bootstrap CSS -->
    <!-- Bootstrap Icons -->
    <!-- Custom Styles -->
</head>
<body>
    <!-- Sidebar -->
    <!-- Main Content -->
    <!-- Bootstrap JS -->
    <!-- Custom JavaScript -->
</body>
</html>
```

### Apéndice B: Convenciones de Código
- Indentación: 2 espacios
- Nombres de clases: kebab-case
- Nombres de IDs: camelCase
- Comentarios: En español para contexto, en inglés para código
- Comillas: Dobles para HTML/CSS, simples para JavaScript

---

**Última actualización**: 2026-02-17  
**Versión del documento**: 1.0
