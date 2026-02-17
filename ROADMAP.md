# Roadmap del Proyecto

**CMS Gubernamental para Alcaldía**  
**Fecha de inicio:** Febrero 2026  
**Estado:** En desarrollo

---

## 🎯 Visión General

Desarrollar un Sistema de Gestión de Contenidos profesional que cumpla con todas las normativas colombianas de gobierno digital, transparencia y accesibilidad.

## 📅 Fases del Proyecto

### ✅ Fase 1: Constitución del Proyecto (COMPLETADA)
**Duración:** 1 semana  
**Estado:** Completada ✅

**Entregables:**
- [x] Documentación fundacional (constitution.md)
- [x] Estructura de directorios
- [x] Configuración Docker
- [x] ADRs iniciales
- [x] Pipeline CI/CD
- [x] README y guías

---

### 🔄 Fase 2: Backend Base (EN PROGRESO)
**Duración:** 2-3 semanas  
**Estado:** No iniciada

**Objetivos:**
- [ ] Inicializar Laravel 12
- [ ] Configurar base de datos
- [ ] Implementar autenticación con Sanctum
- [ ] Configurar Spatie Permission
- [ ] Crear modelos base
- [ ] Implementar API REST base
- [ ] Tests unitarios y de integración

**Tareas Detalladas:**

#### 2.1 Setup Laravel (Semana 1)
- [ ] `composer create-project laravel/laravel:^12.0 backend-temp`
- [ ] Mover archivos a `backend/`
- [ ] Configurar `.env`
- [ ] Ejecutar `php artisan key:generate`
- [ ] Configurar conexión a MySQL
- [ ] Configurar Redis

#### 2.2 Autenticación (Semana 1-2)
- [ ] Instalar Laravel Sanctum
- [ ] Configurar cookies HTTP-Only
- [ ] Crear endpoints de auth
  - [ ] POST /api/v1/login
  - [ ] POST /api/v1/logout
  - [ ] GET /api/v1/user
  - [ ] POST /api/v1/forgot-password
  - [ ] POST /api/v1/reset-password
- [ ] Tests de autenticación
- [ ] Rate limiting

#### 2.3 Autorización (Semana 2)
- [ ] Instalar Spatie Permission
- [ ] Crear roles:
  - [ ] super-admin
  - [ ] admin-transparencia
  - [ ] editor
  - [ ] ciudadano
  - [ ] auditor
- [ ] Crear permisos base
- [ ] Seeders de roles y permisos
- [ ] Middleware de autorización
- [ ] Tests de autorización

#### 2.4 Modelos Base (Semana 2-3)
- [ ] User (extender default)
- [ ] Content
- [ ] Category
- [ ] Tag
- [ ] Media
- [ ] PQRS
- [ ] TransparencySection
- [ ] AuditLog (spatie/activitylog)

#### 2.5 API REST (Semana 3)
- [ ] Controladores API v1
- [ ] FormRequests para validación
- [ ] API Resources para transformación
- [ ] Paginación
- [ ] Filtros y búsqueda
- [ ] Documentación OpenAPI

---

### ⏳ Fase 3: Frontend Admin (PENDIENTE)
**Duración:** 3-4 semanas  
**Estado:** No iniciada

**Objetivos:**
- [ ] Inicializar Vue 3 + Vuestic
- [ ] Configurar routing
- [ ] Implementar autenticación
- [ ] Crear layout principal
- [ ] Dashboard
- [ ] CRUD de contenidos
- [ ] Gestión de usuarios
- [ ] Gestión de transparencia

**Tareas:**

#### 3.1 Setup Vue 3 (Semana 1)
- [ ] `npm create vite@latest frontend-admin -- --template vue-ts`
- [ ] Instalar Vuestic UI
- [ ] Configurar Vue Router
- [ ] Configurar Pinia
- [ ] Configurar Axios
- [ ] Configurar @tanstack/vue-query

#### 3.2 Autenticación (Semana 1)
- [ ] Login page
- [ ] Store de autenticación
- [ ] Guards de rutas
- [ ] Manejo de tokens CSRF
- [ ] Refresh de sesión

#### 3.3 Layout (Semana 1-2)
- [ ] Sidebar con navegación
- [ ] Header con usuario
- [ ] Footer
- [ ] Breadcrumbs
- [ ] Theme switcher

#### 3.4 Dashboard (Semana 2)
- [ ] Métricas principales
- [ ] Gráficas (Chart.js)
- [ ] Actividad reciente
- [ ] Alertas de transparencia

#### 3.5 Gestión de Contenidos (Semana 2-3)
- [ ] Listado con filtros
- [ ] Crear contenido
- [ ] Editar contenido
- [ ] Editor WYSIWYG
- [ ] Multimedia
- [ ] Programación de publicación

#### 3.6 Gestión de Usuarios (Semana 3)
- [ ] Listado de usuarios
- [ ] Crear usuario
- [ ] Editar usuario
- [ ] Asignar roles
- [ ] Ver actividad

#### 3.7 Transparencia (Semana 4)
- [ ] Secciones obligatorias
- [ ] Formularios de actualización
- [ ] Calendario de actualizaciones
- [ ] Reportes ITA/FURAG

---

### ⏳ Fase 4: Frontend Público (PENDIENTE)
**Duración:** 3-4 semanas  
**Estado:** No iniciada

**Objetivos:**
- [ ] Inicializar Vue 3 + Bootstrap 5
- [ ] Implementar diseño GOV.CO
- [ ] Página principal
- [ ] Sección de noticias
- [ ] Transparencia activa
- [ ] PQRS
- [ ] Buscador
- [ ] Datos abiertos
- [ ] Cumplir WCAG 2.1 AA

**Tareas:**

#### 4.1 Setup Vue 3 (Semana 1)
- [ ] `npm create vite@latest frontend-public -- --template vue-ts`
- [ ] Instalar Bootstrap 5
- [ ] Configurar SASS
- [ ] Implementar paleta GOV.CO
- [ ] Configurar Vue Router
- [ ] SEO (vue-meta)

#### 4.2 Diseño GOV.CO (Semana 1-2)
- [ ] Header oficial
- [ ] Footer oficial
- [ ] Navegación
- [ ] Componentes base accesibles
- [ ] Tipografía Work Sans
- [ ] Sistema de grid responsive

#### 4.3 Página Principal (Semana 2)
- [ ] Hero section
- [ ] Noticias destacadas
- [ ] Accesos rápidos
- [ ] Transparencia destacada
- [ ] Redes sociales

#### 4.4 Noticias (Semana 2)
- [ ] Listado de noticias
- [ ] Detalle de noticia
- [ ] Filtros por categoría
- [ ] Búsqueda

#### 4.5 Transparencia (Semana 3)
- [ ] Información mínima obligatoria
- [ ] Navegación por secciones
- [ ] Descarga de documentos
- [ ] Datasets abiertos (JSON/CSV/XML)

#### 4.6 PQRS (Semana 3)
- [ ] Formulario accesible
- [ ] Validación completa
- [ ] Captcha
- [ ] Confirmación
- [ ] Seguimiento

#### 4.7 Accesibilidad (Semana 4)
- [ ] Textos alternativos
- [ ] Navegación por teclado
- [ ] Contraste WCAG 2.1 AA
- [ ] ARIA labels
- [ ] Tests con Axe
- [ ] Auditoría Lighthouse

---

### ⏳ Fase 5: Características Avanzadas (PENDIENTE)
**Duración:** 2-3 semanas  
**Estado:** No iniciada

**Objetivos:**
- [ ] Sistema de notificaciones
- [ ] Exportación de datos
- [ ] Reportes avanzados
- [ ] Multilenguaje
- [ ] Versiones de contenido
- [ ] Workflow de aprobación

---

### ⏳ Fase 6: Testing y QA (PENDIENTE)
**Duración:** 2 semanas  
**Estado:** No iniciada

**Objetivos:**
- [ ] Tests E2E completos
- [ ] Tests de carga
- [ ] Tests de seguridad
- [ ] Auditoría de accesibilidad
- [ ] Auditoría de cumplimiento normativo
- [ ] Corrección de bugs

---

### ⏳ Fase 7: Despliegue a Producción (PENDIENTE)
**Duración:** 1 semana  
**Estado:** No iniciada

**Objetivos:**
- [ ] Configurar servidor DigitalOcean
- [ ] Configurar DNS
- [ ] Certificados SSL
- [ ] Deploy backend
- [ ] Deploy frontend admin
- [ ] Deploy frontend público
- [ ] Configurar backups
- [ ] Monitoreo
- [ ] Capacitación a usuarios

---

## 📊 Métricas de Éxito

### Técnicas
- ✅ Cobertura de tests: >80%
- ✅ PHPStan: Level 8
- ✅ TypeScript: Strict mode
- ✅ Lighthouse: >90
- ✅ WCAG 2.1: AA

### Negocio
- ✅ Cumplimiento 100% normativas
- ✅ ITA: Índice >90
- ✅ FURAG: Reportes automáticos
- ✅ PQRS: Tiempo respuesta <15 días

## 🎯 Hitos Principales

| Fecha | Hito |
|-------|------|
| 2026-02-17 | ✅ Fase 1 completada |
| 2026-03-10 | Backend base |
| 2026-04-07 | Frontend admin |
| 2026-05-05 | Frontend público |
| 2026-05-26 | Features avanzadas |
| 2026-06-09 | Testing y QA |
| 2026-06-16 | **Producción** 🚀 |

## 🔄 Metodología

- **Framework:** Scrum adaptado
- **Sprints:** 2 semanas
- **Revisiones:** Cada sprint
- **Retrospectivas:** Cada sprint
- **Daily standups:** No (proyecto individual/equipo pequeño)

## 📈 Progreso Actual

```
[████████░░░░░░░░░░░░░░░░░░░░] 25% - Fase 1 completada

Fase 1: Constitución     [████████████████████] 100%
Fase 2: Backend Base     [░░░░░░░░░░░░░░░░░░░░]   0%
Fase 3: Frontend Admin   [░░░░░░░░░░░░░░░░░░░░]   0%
Fase 4: Frontend Público [░░░░░░░░░░░░░░░░░░░░]   0%
Fase 5: Avanzadas        [░░░░░░░░░░░░░░░░░░░░]   0%
Fase 6: Testing          [░░░░░░░░░░░░░░░░░░░░]   0%
Fase 7: Producción       [░░░░░░░░░░░░░░░░░░░░]   0%
```

## 🚀 Próximos Pasos Inmediatos

1. Inicializar Laravel 12 en `backend/`
2. Configurar base de datos y migraciones
3. Implementar autenticación con Sanctum
4. Crear primeros endpoints API
5. Tests básicos

---

**Última actualización:** 2026-02-17  
**Versión:** 1.0.0
