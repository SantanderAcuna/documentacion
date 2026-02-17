# Portal de Configuración VPS - Documentación del Proyecto

Portal web full-stack de documentación y gestión centralizada para administradores de sistemas que trabajan con servidores VPS.

## 🏗️ Stack Tecnológico

### Backend
- **Framework:** Laravel 12 (PHP 8.3.1+)
- **Base de Datos:** MySQL 8.0+ (InnoDB, utf8mb4)
- **Autenticación:** Laravel Sanctum (cookies HTTP-Only)
- **Autorización:** Spatie Permission (RBAC dinámico)
- **Almacenamiento:** DigitalOcean Spaces (S3-compatible)
- **Caché:** Redis (sesiones, queries, queue)

### Frontend
- **Framework:** Vue.js 3 (Composition API)
- **Lenguaje:** TypeScript (strict mode)
- **Cliente HTTP:** Axios (withCredentials)
- **Estado:** Pinia (stores modulares)
- **Queries:** Vue Query (@tanstack/vue-query)
- **Validación:** VeeValidate 4 + Yup
- **Enrutamiento:** Vue Router 4 (mode history)
- **UI:** Bootstrap 5 + SASS
- **Iconos:** FontAwesome 6 (FREE)
- **Notificaciones:** Vue Toastification

### Infraestructura
- **Cloud:** DigitalOcean
- **OS:** Ubuntu 24.04 LTS
- **Contenedores:** Docker + Docker Compose
- **Servidor Web:** Nginx (reverse proxy)
- **CI/CD:** GitHub Actions
- **Monitoreo:** Laravel Pulse + DigitalOcean Monitoring

## 📋 Descripción

Aplicación web full-stack SPA que centraliza la documentación técnica de configuración VPS. Plataforma dinámica con gestión de contenido, sistema de autenticación, roles de usuario y búsqueda avanzada.

### Características Principales

- ✅ **Autenticación Completa:** Registro, login, recuperación de contraseña
- ✅ **Sistema RBAC:** 4 roles (SuperAdmin, Admin, Editor, Viewer)
- ✅ **CRUD de Documentación:** Editor markdown con preview en tiempo real
- ✅ **Búsqueda Avanzada:** Full-text search con filtros y autocompletado
- ✅ **Sistema de Favoritos:** Marcar documentos con sincronización backend
- ✅ **Upload de Archivos:** Gestión de imágenes y archivos en S3
- ✅ **Dashboard Personalizado:** Estadísticas según rol de usuario
- ✅ **Versionamiento:** Historial completo de cambios en documentos
- ✅ **Panel de Administración:** Gestión de usuarios, roles y contenido
- ✅ **Responsive Design:** Optimizado para móvil, tablet y desktop
- ✅ **API RESTful:** Endpoints documentados y versionados

## 📁 Estructura del Proyecto

```
documentacion/
├── backend/                    # Laravel 12 API
│   ├── app/
│   │   ├── Http/              # Controllers, Requests, Resources
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
│   └── tests/                # PHPUnit tests
│
├── frontend/                  # Vue.js 3 + TypeScript
│   ├── src/
│   │   ├── components/       # Vue components
│   │   ├── composables/      # Vue composables
│   │   ├── router/           # Vue Router config
│   │   ├── stores/           # Pinia stores
│   │   ├── services/         # API services
│   │   ├── types/            # TypeScript types
│   │   └── views/            # Page views
│   └── tests/                # Vitest tests
│
├── docker/                    # Docker configuration
│   ├── nginx/
│   ├── php/
│   └── mysql/
│
├── .github/workflows/         # CI/CD pipelines
│   ├── test.yml
│   ├── build.yml
│   └── deploy.yml
│
├── deployment/                # Deployment configs
│   └── digitalocean/
│       ├── app.yaml          # DO App Platform
│       └── setup.sh          # Droplet setup script
│
├── docs/                      # Documentación del proyecto
│   ├── README.md             # Este archivo
│   ├── user-stories.md       # 20 historias de usuario
│   ├── tasks.md              # 40 tareas del proyecto
│   ├── business-rules.md     # 30 reglas de negocio
│   ├── project-specs.md      # Especificaciones técnicas
│   ├── DOCUMENTATION_SUMMARY.md
│   └── DOCUMENTATION_INDEX.md
│
├── docker-compose.yml         # Desarrollo local
└── README.md                 # Este archivo
```

## 📚 Documentación del Proyecto

### [Historias de Usuario](user-stories.md)
20 historias de usuario completas con criterios de aceptación:
- **Alta prioridad (12):** Autenticación, CRUD, búsqueda, navegación
- **Media prioridad (6):** Perfil, favoritos, uploads, recovery
- **Baja prioridad (4):** Versionamiento, analytics, logs

**Estimación total:** 238 horas

### [Tareas del Proyecto](tasks.md)
40 tareas técnicas organizadas en 7 fases (18 sprints):
1. **Configuración del Entorno** (5 tareas)
2. **Backend Core** (7 tareas)
3. **Frontend Core** (7 tareas)
4. **Funcionalidades Principales** (6 tareas)
5. **Panel de Administración** (4 tareas)
6. **Testing y QA** (5 tareas)
7. **Deployment** (6 tareas)

**Estimación total:** 482 horas (~12 semanas)

### [Reglas de Negocio](business-rules.md)
30 reglas de negocio en 10 categorías:
- Autenticación y Sesiones
- Autorización y Permisos
- Contenido y Documentación
- Almacenamiento y Uploads
- Búsqueda y Filtrado
- Performance y Cache
- Seguridad
- API y Comunicación
- Frontend
- Testing

**Impacto:** 7 Críticas, 15 Altas, 7 Medias, 1 Baja

### [Especificaciones del Proyecto](project-specs.md)
Documento técnico completo con:
- Arquitectura Laravel + Vue.js detallada
- Estructura de carpetas
- Tecnologías y dependencias
- Diseño y UX (paleta de colores, componentes)
- 10 Requisitos funcionales
- 8 Requisitos no funcionales
- Plan de implementación en 7 fases
- Guía de deployment en DigitalOcean
- Setup Ubuntu 24.04 paso a paso
- Costos estimados

## 🚀 Instalación y Configuración

### Prerrequisitos
- Docker Desktop instalado
- Git instalado
- Node.js 20+ (para desarrollo frontend)
- PHP 8.3+ (para desarrollo backend)
- Composer 2+

### 1. Clonar el Repositorio
```bash
git clone https://github.com/SantanderAcuna/documentacion.git
cd documentacion
```

### 2. Setup con Docker Compose
```bash
# Copiar archivos de configuración
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env

# Iniciar contenedores
docker-compose up -d

# Instalar dependencias backend
docker-compose exec php composer install

# Generar key de Laravel
docker-compose exec php php artisan key:generate

# Ejecutar migraciones
docker-compose exec php php artisan migrate --seed

# Instalar dependencias frontend
docker-compose exec node npm install

# Iniciar dev server frontend
docker-compose exec node npm run dev
```

### 3. Acceder a la Aplicación
- **Frontend:** http://localhost:5173
- **Backend API:** http://localhost:8000/api
- **phpMyAdmin:** http://localhost:8080

### Credenciales por Defecto
- **SuperAdmin:** admin@example.com / password
- **Editor:** editor@example.com / password
- **Viewer:** viewer@example.com / password

## 🧪 Testing

### Backend Tests (PHPUnit)
```bash
docker-compose exec php php artisan test
docker-compose exec php php artisan test --coverage
```

### Frontend Tests (Vitest)
```bash
docker-compose exec node npm run test
docker-compose exec node npm run test:coverage
```

### E2E Tests (Cypress)
```bash
docker-compose exec node npm run test:e2e
```

## 📦 Deployment en DigitalOcean

### Opción 1: App Platform (Recomendado)
```bash
# Deploy automático desde GitHub
doctl apps create --spec deployment/digitalocean/app.yaml
```

### Opción 2: Droplets Ubuntu 24.04
```bash
# Configurar droplet
ssh root@your-droplet-ip
bash deployment/digitalocean/setup.sh

# Deploy con GitHub Actions (automático)
git push origin main
```

Ver [project-specs.md - Deployment](project-specs.md#deployment-en-digitalocean) para guía completa.

## 🎨 Paleta de Colores

```css
--primary-dark: #1a365d      /* Azul oscuro principal */
--primary-blue: #2b6cb0      /* Azul principal */
--primary-light-blue: #4299e1 /* Azul claro */
--accent-orange: #ed8936     /* Color de acento */
--sidebar-bg: #1a202c        /* Fondo del sidebar */
```

## 📱 Responsive Breakpoints

- **Desktop:** > 992px (sidebar visible)
- **Tablet:** 768px - 992px (sidebar colapsable)
- **Mobile:** < 768px (sidebar oculto, menú hamburguesa)
- **Small Mobile:** < 480px

## 🔐 Seguridad

- ✅ HTTPS obligatorio en producción
- ✅ CSRF protection habilitado
- ✅ XSS prevention con sanitización
- ✅ SQL Injection prevention (Eloquent ORM)
- ✅ Rate limiting en API
- ✅ Cookies HTTP-Only y Secure
- ✅ Headers de seguridad (HSTS, CSP)
- ✅ Validación dual frontend/backend
- ✅ Tests de seguridad en CI

## 📊 Estado del Proyecto

### Fase Actual: Planificación y Documentación ✅
- [x] Arquitectura definida
- [x] Stack tecnológico seleccionado
- [x] Documentación completa generada
- [ ] Setup inicial del proyecto
- [ ] Desarrollo backend core
- [ ] Desarrollo frontend core
- [ ] Testing
- [ ] Deployment

### Próximos Pasos
1. Setup repositorio y Docker Compose
2. Inicializar Laravel 12 backend
3. Inicializar Vue 3 + TypeScript frontend
4. Implementar autenticación con Sanctum
5. Desarrollar API CRUD documentación
6. Desarrollar interfaces Vue.js

## 🤝 Contribuciones

### Workflow
1. Fork del repositorio
2. Crear branch: `feature/nombre-feature`
3. Commits con mensajes descriptivos
4. Push a tu fork
5. Crear Pull Request

### Code Standards
- **Backend:** PSR-12 coding standards
- **Frontend:** ESLint + Prettier
- **Tests:** Coverage mínimo 70%
- **Commits:** Conventional Commits

## 📄 Licencia

Todos los derechos reservados © 2023

## 📞 Contacto y Soporte

Para preguntas o soporte, consulta la documentación o abre un issue en GitHub.

---

**Versión:** 2.0.0  
**Última actualización:** 2026-02-17  
**Stack:** Laravel 12 + Vue.js 3 + TypeScript + DigitalOcean Ubuntu 24.04
