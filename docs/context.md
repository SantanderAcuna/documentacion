# Contexto del Proyecto CMS Gubernamental

**Última actualización:** 2026-02-17  
**Versión:** 1.0.0  
**Estado:** En desarrollo

---

## 📊 Información General

### Propósito del Proyecto
Sistema de Gestión de Contenidos (CMS) para Alcaldía que cumple con normativas colombianas de gobierno digital, transparencia y accesibilidad.

### Normativas Clave a Cumplir
- Ley 1341/2009 (Gobierno en Línea)
- Ley 1712/2014 (Transparencia)
- Decreto 1078/2015 (Gobierno Digital)
- Resolución 1519/2020 (WCAG 2.1 AA)
- Ley 1581/2012 (Protección de Datos)

---

## 🏗️ Arquitectura Actual

### Estructura del Monorepo
```
cms-gubernamental/
├── backend/              # Laravel 12 + PHP 8.3
├── frontend-admin/       # Vue 3 + Vuestic UI
├── frontend-public/      # Vue 3 + GOV.CO Design
├── docker/              # Contenedores
└── docs/                # Documentación
```

### Stack Tecnológico

**Backend:**
- Laravel 12 sobre PHP 8.3+
- MySQL 8.0 (InnoDB, utf8mb4)
- Redis 7.x (caché y sesiones)
- Laravel Sanctum (autenticación)
- Spatie Permission (RBAC)

**Frontend Admin:**
- Vue 3 (Composition API)
- TypeScript (strict)
- Vuestic UI
- Pinia (estado)
- @tanstack/vue-query (caché)
- VeeValidate 4 + Yup

**Frontend Public:**
- Vue 3 (Composition API)
- TypeScript (strict)
- Bootstrap 5 + SASS
- Diseño GOV.CO (MinTIC)
- FontAwesome 6
- WCAG 2.1 AA

**Infraestructura:**
- Docker + Docker Compose
- Nginx (reverse proxy)
- GitHub Actions (CI/CD)

---

## 🎯 Estado Actual del Desarrollo

### Fase Actual: Fase 1 - Constitución del Proyecto

**Completado:**
- ✅ Documentación fundacional (constitution.md)
- ✅ Estructura de directorios
- ✅ Configuración Docker
- ✅ README principal

**En Progreso:**
- 🔄 Inicialización del backend Laravel
- 🔄 Inicialización de frontends Vue 3

**Pendiente:**
- ⏳ Modelos y migraciones base
- ⏳ Sistema de autenticación
- ⏳ Sistema de roles y permisos
- ⏳ APIs REST base
- ⏳ Interfaz administrativa
- ⏳ Sitio público

---

## 🔑 Decisiones Arquitectónicas Importantes

### ADR-001: Monorepo con Docker
**Decisión:** Usar un monorepo con Docker Compose para desarrollo  
**Razón:** Simplifica el desarrollo local y mantiene todos los componentes versionados juntos  
**Fecha:** 2026-02-17

### ADR-002: Laravel Sanctum para Autenticación
**Decisión:** Usar Laravel Sanctum con cookies HTTP-Only en lugar de JWT  
**Razón:** Mayor seguridad (cookies httpOnly, sameSite), integración nativa con Laravel  
**Fecha:** 2026-02-17

### ADR-003: Dos Frontends Separados
**Decisión:** Frontend admin (Vuestic) y público (GOV.CO) separados  
**Razón:** 
- Admin requiere UI compleja (Vuestic)
- Público requiere diseño gubernamental (GOV.CO)
- Mejor separación de responsabilidades
**Fecha:** 2026-02-17

---

## 🔐 Políticas de Seguridad Aplicadas

### Autenticación y Autorización
- Sanctum con cookies HTTP-Only, Secure, SameSite=Strict
- RBAC con Spatie Permission
- Rate limiting: 5 intentos login / 15 min

### Validación
- Frontend: VeeValidate + Yup
- Backend: FormRequest de Laravel
- Base de datos: Constraints

### Protección contra Ataques
- SQL Injection: Eloquent ORM (prepared statements)
- XSS: Blade `{{ }}` (auto-escape)
- CSRF: Token en todos los formularios
- HTTPS: Obligatorio con HSTS

### Auditoría
- Package: spatie/laravel-activitylog
- Request-ID único
- Logs inmutables (retención 1 año)

---

## 🎨 Diseño y Accesibilidad

### Paleta de Colores GOV.CO
```scss
$azul-institucional: #004884;  // Primario
$amarillo-bandera: #FFD500;     // Secundario
$azul-bandera: #003DA5;         // Acento
$rojo-bandera: #CE1126;         // Alerta
```

### Cumplimiento WCAG 2.1 AA
- Contraste mínimo 4.5:1 para texto normal
- Contraste mínimo 3:1 para texto grande
- Navegación completa por teclado
- Textos alternativos en imágenes
- Formularios con etiquetas asociadas

### Tipografía
- Primaria: Work Sans
- Secundaria: Montserrat
- Tamaño base: 16px
- Line-height: 1.5 (mínimo WCAG)

---

## 📝 Convenciones de Código

### PHP (Backend)
- Estándar: PSR-12
- Análisis estático: PHPStan Level 8
- Documentación: PHPDoc
- Nombres de clases: PascalCase
- Nombres de métodos: camelCase
- Nombres de variables: snake_case (BD) / camelCase (código)

### TypeScript/Vue (Frontend)
- Guía de estilos: Vue 3 Official
- Composition API con `<script setup>`
- TypeScript strict mode
- Documentación: TSDoc
- Nombres de componentes: PascalCase
- Nombres de composables: use{Nombre}
- ESLint + Prettier

### Git
- Commits: Conventional Commits
- Formato: `type(scope): mensaje`
- Tipos: feat, fix, docs, style, refactor, test, chore
- Branching: Trunk-based development

---

## 🚀 Flujo de Trabajo

### Desarrollo Local
1. `docker-compose up -d` - Iniciar contenedores
2. Desarrollar en rama feature
3. Ejecutar tests localmente
4. Commit con mensaje convencional
5. Push y crear PR

### CI/CD
1. GitHub Actions ejecuta tests
2. PHPStan + ESLint verifican código
3. Build de producción
4. Aprobación manual
5. Deploy automático

---

## 🧪 Testing

### Backend
- Framework: PHPUnit
- Cobertura mínima: 80%
- Tests: Feature, Unit, Integration
- Comando: `php artisan test`

### Frontend
- Framework: Vitest
- Cobertura mínima: 80%
- Tests: Unit, Component, E2E (Cypress)
- Comando: `npm run test`

---

## 📦 Dependencias Clave

### Backend (composer.json)
```json
{
  "laravel/framework": "^12.0",
  "laravel/sanctum": "^4.0",
  "spatie/laravel-permission": "^6.0",
  "spatie/laravel-activitylog": "^4.0"
}
```

### Frontend Admin (package.json)
```json
{
  "vue": "^3.4.0",
  "vuestic-ui": "^1.9.0",
  "pinia": "^2.1.0",
  "@tanstack/vue-query": "^5.0.0",
  "vee-validate": "^4.12.0",
  "yup": "^1.3.0"
}
```

### Frontend Public (package.json)
```json
{
  "vue": "^3.4.0",
  "bootstrap": "^5.3.0",
  "@fortawesome/fontawesome-free": "^6.5.0",
  "vue-toastification": "^2.0.0"
}
```

---

## 🔄 Patrones y Prácticas

### Backend (Laravel)
- **Repository Pattern:** Para lógica de datos complejas
- **Service Pattern:** Para lógica de negocio
- **FormRequest:** Para validación
- **API Resources:** Para transformación de respuestas
- **Jobs:** Para tareas asíncronas
- **Events:** Para desacoplar funcionalidades

### Frontend (Vue)
- **Composables:** Lógica reutilizable
- **Pinia Stores:** Estado global
- **Vue Query:** Caché de servidor
- **Props down, Events up:** Comunicación componentes
- **Teleport:** Para modales/toasts
- **Suspense:** Para carga asíncrona

---

## 🐛 Problemas Conocidos y Soluciones

### Problema: [Ninguno aún]
**Síntoma:** -  
**Causa:** -  
**Solución:** -  
**Fecha:** -

---

## 📚 Recursos y Referencias

### Documentación Oficial
- Laravel 12: https://laravel.com/docs/12.x
- Vue 3: https://vuejs.org/guide/
- Vuestic: https://vuestic.dev/
- MinTIC GOV.CO: https://www.gov.co/

### Normativas
- Ley 1712/2014: Transparencia
- Resolución 1519/2020: Accesibilidad WCAG 2.1 AA
- Manual GOV.CO: Identidad visual

### Herramientas
- Axe DevTools: Validación accesibilidad
- PHPStan: Análisis estático PHP
- ESLint: Linting JavaScript/TypeScript

---

## 👥 Equipo y Contacto

### Roles
- **Tech Lead:** [Pendiente]
- **Backend Developer:** [Pendiente]
- **Frontend Developer:** [Pendiente]
- **DevOps:** [Pendiente]
- **QA:** [Pendiente]

### Comunicación
- **GitHub Issues:** Bugs y features
- **GitHub Discussions:** Preguntas generales
- **PRs:** Code review

---

## 🔄 Historial de Cambios

### 2026-02-17 - v1.0.0
- ✅ Creación del documento inicial
- ✅ Definición de arquitectura
- ✅ Configuración Docker
- ✅ Estructura de directorios

---

**Nota para IA:** Este documento debe actualizarse en cada iteración importante. Mantener las decisiones arquitectónicas, problemas resueltos y patrones aplicados para asegurar continuidad entre sesiones.
