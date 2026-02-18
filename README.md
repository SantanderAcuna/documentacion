# CMS Gubernamental - Sistema de Gestión de Contenidos para Alcaldía

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)
[![Vue](https://img.shields.io/badge/Vue-3-green.svg)](https://vuejs.org)
[![PHP](https://img.shields.io/badge/PHP-8.3+-purple.svg)](https://php.net)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-blue.svg)](https://www.typescriptlang.org)

## 📋 Descripción

Sistema de Gestión de Contenidos (CMS) profesional desarrollado para la Alcaldía que permite crear, gestionar y publicar información institucional, normativa y de transparencia, cumpliendo integralmente con las normativas colombianas vigentes.

### Cumplimiento Normativo

Este sistema cumple con:
- ✅ **Ley 1341/2009** - Gobierno en Línea
- ✅ **Ley 1712/2014** - Transparencia y Acceso a la Información
- ✅ **Decreto 1078/2015** - Gobierno Digital
- ✅ **Resolución 1519/2020** - Accesibilidad Web WCAG 2.1 AA
- ✅ **Ley 1581/2012** - Protección de Datos Personales
- ✅ **ITA** - Índice de Transparencia y Acceso a la Información
- ✅ **FURAG** - Formulario Único de Reporte y Avance de Gestión (MIPG)

## 🏗️ Arquitectura

```
cms-gubernamental/
├── backend/              # Laravel 12 API
├── frontend-admin/       # Panel administrativo (Vue 3 + Vuestic)
├── frontend-public/      # Sitio público (Vue 3 + GOV.CO Design)
├── docker/              # Contenedores y configuración
├── docs/                # Documentación del proyecto
│   ├── adr/            # Architecture Decision Records
│   └── context.md      # Contexto compartido
└── .github/            # CI/CD y workflows
```

## 🚀 Stack Tecnológico

### Backend
- **Framework:** Laravel 12
- **Lenguaje:** PHP 8.3+
- **Base de Datos:** MySQL 8.0+
- **Autenticación:** Laravel Sanctum (cookies HTTP-Only)
- **Autorización:** Spatie Laravel Permission (RBAC)
- **Caché:** Redis 7.x
- **Validación:** FormRequest
- **API:** RESTful versionada (`/api/v1/`)

### Frontend Admin
- **Framework:** Vue 3 (Composition API + `<script setup>`)
- **Lenguaje:** TypeScript (strict mode)
- **UI:** Vuestic UI
- **Estado:** Pinia
- **HTTP Client:** Axios (withCredentials)
- **Cache/Query:** @tanstack/vue-query
- **Validación:** VeeValidate 4 + Yup
- **Routing:** Vue Router 4

### Frontend Public
- **Framework:** Vue 3 (Composition API + `<script setup>`)
- **Lenguaje:** TypeScript (strict mode)
- **Diseño:** GOV.CO Design System (MinTIC)
- **UI:** Bootstrap 5 + SASS personalizado
- **Iconos:** FontAwesome 6 (FREE)
- **Notificaciones:** Vue Toastification

### Infraestructura
- **Contenedores:** Docker + Docker Compose
- **Servidor Web:** Nginx (reverse proxy)
- **CI/CD:** GitHub Actions
- **Control de Versiones:** Git (trunk-based development)
- **Análisis Estático:** PHPStan (Level 8), ESLint

## 📦 Requisitos Previos

- **Docker:** 20.10+
- **Docker Compose:** v2.0+
- **Node.js:** 18.x LTS (para desarrollo local)
- **PHP:** 8.3+ (para desarrollo local)
- **Composer:** 2.x (para desarrollo local)

## 🔧 Instalación

### 1. Clonar el Repositorio

```bash
git clone https://github.com/SantanderAcuna/documentacion.git cms-gubernamental
cd cms-gubernamental
```

### 2. Configuración con Docker (Recomendado)

```bash
# Copiar archivos de entorno
cp backend/.env.example backend/.env
cp frontend-admin/.env.example frontend-admin/.env
cp frontend-public/.env.example frontend-public/.env

# Iniciar contenedores
docker-compose up -d

# Instalar dependencias del backend
docker-compose exec backend composer install

# Generar clave de aplicación
docker-compose exec backend php artisan key:generate

# Ejecutar migraciones
docker-compose exec backend php artisan migrate --seed

# Instalar permisos y roles
docker-compose exec backend php artisan db:seed --class=RolePermissionSeeder

# Instalar dependencias del frontend admin
docker-compose exec frontend-admin npm install

# Instalar dependencias del frontend público
docker-compose exec frontend-public npm install
```

### 3. Acceder a las Aplicaciones

- **API Backend:** http://localhost:8000
- **Panel Admin:** http://localhost:3000
- **Sitio Público:** http://localhost:3001
- **PhpMyAdmin:** http://localhost:8080
- **Redis Commander:** http://localhost:8081

## 🔐 Seguridad

Este proyecto implementa las siguientes medidas de seguridad **inmutables**:

### SEC-01: HTTPS Obligatorio
- HTTPS en todos los entornos
- Header HSTS: `max-age=31536000; includeSubDomains; preload`
- Certificados Let's Encrypt en producción

### SEC-02: Validación Exhaustiva
- Validación en frontend (VeeValidate + Yup)
- Validación en backend (FormRequest)
- Constraints en base de datos

### SEC-03: Prevención de Inyecciones
- SQL Injection: Prepared statements (Eloquent ORM)
- XSS: Codificación automática (Blade `{{ }}`)
- CSRF: Token en todos los formularios

### SEC-04: Rate Limiting
- Login: 5 intentos / 15 minutos
- API pública: 100 requests / minuto
- API autenticada: 300 requests / minuto

### SEC-05: Auditoría Completa
- Registro de todas las operaciones CRUD
- Request-ID único por petición
- Package: `spatie/laravel-activitylog`

## 🎨 Paleta de Colores GOV.CO

Conforme al Manual de Identidad Visual del Gobierno de Colombia (Resolución 2345/2023):

```scss
// Colores Principales
$azul-institucional: #004884;  // Azul principal GOV.CO
$amarillo-bandera: #FFD500;     // Amarillo bandera
$azul-bandera: #003DA5;         // Azul bandera
$rojo-bandera: #CE1126;         // Rojo bandera
```

Todos los colores cumplen con el contraste mínimo **WCAG 2.1 AA (4.5:1)**.

## 🧪 Pruebas

### Backend (Laravel)

```bash
# Ejecutar todas las pruebas
docker-compose exec backend php artisan test

# Pruebas con cobertura
docker-compose exec backend php artisan test --coverage

# Análisis estático con PHPStan
docker-compose exec backend vendor/bin/phpstan analyse
```

### Frontend

```bash
# Admin Panel
docker-compose exec frontend-admin npm run test
docker-compose exec frontend-admin npm run test:unit
docker-compose exec frontend-admin npm run lint

# Sitio Público
docker-compose exec frontend-public npm run test
docker-compose exec frontend-public npm run test:unit
docker-compose exec frontend-public npm run lint
```

## 🚀 Estado del Proyecto

**Progreso General: 60%**

```
Fase 1: Constitución        [████████████████████] 100% ✅
Fase 2: Backend Base        [██████████████████░░]  90% 🔄
Fase 3: Frontend Admin      [░░░░░░░░░░░░░░░░░░░░]   0% ⏳
Fase 4: Frontend Público    [░░░░░░░░░░░░░░░░░░░░]   0% ⏳
Fase 5: Características     [░░░░░░░░░░░░░░░░░░░░]   0% ⏳
Fase 6: Testing y QA        [░░░░░░░░░░░░░░░░░░░░]   0% ⏳
Fase 7: Producción          [░░░░░░░░░░░░░░░░░░░░]   0% ⏳
```

### ✨ Recién Completado: Fase 2 Backend

**API REST Completamente Funcional:**
- ✅ 6 modelos con relaciones completas (User, Content, Category, Tag, Media, Pqrs)
- ✅ 6 controladores API RESTful con validación
- ✅ Autenticación con Laravel Sanctum
- ✅ RBAC: 6 roles y 24 permisos
- ✅ Migraciones y seeders listos
- ✅ Documentación API completa

**Recursos disponibles:**
- 📖 [API Documentation](backend/API_DOCUMENTATION.md)
- 🛠️ [Backend Setup Guide](backend/SETUP.md)
- 📊 [Estado Detallado](STATUS.md)

**Próximo:** Fase 3 - Desarrollo del Frontend Administrativo con Vue 3 + Vuestic UI

---

## 📚 Documentación

- **[Constitución del Proyecto](constitution.md)** - Principios rectores y fundamentos
- **[Especificaciones](project-specs.md)** - Requisitos funcionales y técnicos
- **[Historias de Usuario](user-stories.md)** - User stories y casos de uso
- **[Reglas de Negocio](business-rules.md)** - Lógica de negocio y validaciones
- **[Tareas](tasks.md)** - Backlog y plan de trabajo
- **[ADRs](docs/adr/)** - Decisiones arquitectónicas
- **[Context.md](docs/context.md)** - Contexto compartido para continuidad

## 🚢 Despliegue

### Producción en DigitalOcean

El sistema está diseñado para desplegarse en un Droplet de Ubuntu 24.04.

```bash
# Conectar al servidor
ssh root@your-server-ip

# Clonar repositorio
git clone https://github.com/SantanderAcuna/documentacion.git /var/www/cms-gubernamental

# Configurar producción
cd /var/www/cms-gubernamental
./deploy/production-setup.sh
```

Consulta la [Guía de Despliegue](docs/deployment.md) para instrucciones detalladas.

## 👥 Perfiles de Usuario

1. **Ciudadanos/Visitantes** - Consultan información y envían PQRS
2. **Editores** - Crean y publican contenidos
3. **Administradores de Transparencia** - Gestionan información obligatoria
4. **Administradores del Sistema** - Gestionan usuarios y configuraciones
5. **Auditores** - Acceso de solo lectura para supervisión

## 🤝 Contribución

Este proyecto sigue los siguientes estándares:

- **PHP:** PSR-12
- **Vue:** Guía de Estilos Oficial de Vue 3
- **Commits:** Conventional Commits
- **Branching:** Trunk-based Development
- **Code Review:** Obligatorio antes de merge

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo [LICENSE](LICENSE) para más detalles.

## 📧 Contacto

Para soporte técnico o consultas, contactar a:
- **Email:** soporte@alcaldia.gov.co
- **Documentación:** https://docs.alcaldia.gov.co

---

**Desarrollado con ❤️ para servir a la ciudadanía**

*Última actualización: Febrero 2026*
