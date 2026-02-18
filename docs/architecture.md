# Arquitectura del Sistema - CMS Gubernamental

## Diagrama de Arquitectura General

```
┌─────────────────────────────────────────────────────────────────────┐
│                         USUARIOS FINALES                             │
├─────────────────────────────────────────────────────────────────────┤
│  Ciudadanos  │  Editores  │  Admins  │  Admins Transp.  │  Auditores│
└─────┬───────────────┬───────────┬──────────────┬─────────────┬──────┘
      │               │           │              │             │
      ▼               ▼           ▼              ▼             ▼
┌──────────────┐  ┌──────────────────────────────────────────────────┐
│   Sitio      │  │           Panel Administrativo                    │
│   Público    │  │           (admin.alcaldia.gov.co)                │
│   (GOV.CO)   │  │                                                   │
│              │  │  ┌────────────────────────────────────────────┐  │
│  Vue 3 +     │  │  │        Vuestic UI Components               │  │
│  Bootstrap 5 │  │  │  Dashboard │ Contenidos │ Usuarios │ etc. │  │
│  WCAG 2.1 AA │  │  └────────────────────────────────────────────┘  │
│              │  │           Vue 3 + TypeScript + Vuestic            │
└──────┬───────┘  └───────────────────────┬──────────────────────────┘
       │                                  │
       │  HTTPS/JSON                      │  HTTPS/JSON (CSRF Protected)
       │                                  │
       └──────────────┬───────────────────┘
                      ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         NGINX REVERSE PROXY                          │
│                       (SSL/TLS Termination)                          │
│                       api.alcaldia.gov.co                            │
├─────────────────────────────────────────────────────────────────────┤
│  • HTTPS con HSTS                                                    │
│  • Rate Limiting                                                     │
│  • Security Headers (X-Frame-Options, CSP, etc.)                    │
│  • Load Balancing (futuro)                                          │
└──────────────────────────────┬──────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    BACKEND API (Laravel 12)                          │
│                         /api/v1/*                                    │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                    MIDDLEWARE STACK                           │  │
│  │  CORS │ Sanctum Auth │ CSRF │ Rate Limit │ Logging           │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                      │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                   CONTROLLERS (API V1)                        │  │
│  │  Auth │ Content │ User │ Transparency │ PQRS │ Media         │  │
│  └────────────┬─────────────────────────────────────────────────┘  │
│               │                                                      │
│               ▼                                                      │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                   FORM REQUESTS                               │  │
│  │              (Validación de entradas)                         │  │
│  └────────────┬─────────────────────────────────────────────────┘  │
│               │                                                      │
│               ▼                                                      │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                     SERVICES                                  │  │
│  │         (Lógica de negocio encapsulada)                      │  │
│  └────────────┬─────────────────────────────────────────────────┘  │
│               │                                                      │
│               ▼                                                      │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                 ELOQUENT MODELS                               │  │
│  │  User │ Content │ Category │ Tag │ Media │ PQRS │ etc.       │  │
│  └────────────┬─────────────────────────────────────────────────┘  │
│               │                                                      │
│               ▼                                                      │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │               SPATIE ACTIVITY LOG                             │  │
│  │              (Auditoría de cambios)                           │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                      │
└───────────┬──────────────────────────────────┬─────────────────────┘
            │                                  │
            ▼                                  ▼
┌───────────────────────┐        ┌────────────────────────────┐
│    MySQL 8.0          │        │       Redis 7.x            │
│  (Base de Datos)      │        │  (Cache + Sessions)        │
├───────────────────────┤        ├────────────────────────────┤
│ • InnoDB              │        │ • Session Store            │
│ • utf8mb4_unicode_ci  │        │ • Query Cache              │
│ • JSON fields         │        │ • Queue Jobs (futuro)      │
│ • FULLTEXT search     │        │ • Rate Limit counters      │
└───────────────────────┘        └────────────────────────────┘
```

## Flujo de Autenticación

```
1. Usuario accede al Panel Admin
   │
   ▼
2. Frontend solicita /sanctum/csrf-cookie
   │
   ▼
3. Backend envía cookie XSRF-TOKEN
   │
   ▼
4. Usuario envía credenciales a POST /api/v1/login
   (Header: X-XSRF-TOKEN incluido)
   │
   ▼
5. Backend valida credenciales
   │
   ├─ ✅ Válido
   │  │
   │  ▼
   │  - Crea sesión en Redis
   │  - Envía cookie de sesión (httpOnly, secure, sameSite=strict)
   │  - Retorna datos del usuario
   │
   └─ ❌ Inválido
      │
      ▼
      - Rate limiting incrementa contador
      - Retorna 401 Unauthorized
      - Después de 5 intentos: bloqueo 15 min

6. Requests subsecuentes incluyen cookie automáticamente
   │
   ▼
7. Middleware Sanctum valida sesión
   │
   ├─ ✅ Sesión válida
   │  │
   │  ▼
   │  - Request continúa
   │  - Middleware de autorización verifica permisos
   │  - Controlador procesa request
   │
   └─ ❌ Sesión inválida/expirada
      │
      ▼
      - Retorna 401 Unauthorized
      - Frontend redirige a login
```

## Flujo de Solicitud API Típica

```
GET /api/v1/contents?page=1&category=noticias

   1. Nginx recibe request
      │
      ▼
   2. Verifica rate limit
      │
      ▼
   3. Proxy a backend:9000
      │
      ▼
   4. Laravel: Middleware Stack
      │
      ├─ CORS: Verifica origin
      ├─ Sanctum: Valida sesión
      ├─ CSRF: Valida token (POST/PUT/DELETE)
      ├─ Rate Limit: Verifica límites
      └─ Logging: Registra request-id
      │
      ▼
   5. Route → Controller
      │
      ▼
   6. Controller → Service
      │
      ▼
   7. Service → Model (Eloquent)
      │
      ▼
   8. Eloquent → MySQL (prepared statement)
      │
      ▼
   9. MySQL retorna resultados
      │
      ▼
  10. Model → Service → Controller
      │
      ▼
  11. API Resource transforma datos
      │
      ▼
  12. Response con headers de seguridad
      │
      ▼
  13. Nginx → Cliente (JSON)
      │
      ▼
  14. Frontend procesa response
      - Vue Query cachea resultado
      - Actualiza UI reactivamente
```

## Estructura de Datos

```
┌─────────────────────────────────────────────────────────────┐
│                      Base de Datos                           │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  users                        roles                          │
│  ├─ id                        ├─ id                          │
│  ├─ name                      ├─ name                        │
│  ├─ email                     └─ guard_name                  │
│  ├─ password (bcrypt)                                        │
│  ├─ email_verified_at         permissions                    │
│  └─ timestamps                ├─ id                          │
│                               ├─ name                        │
│  contents                     └─ guard_name                  │
│  ├─ id                                                       │
│  ├─ title                     model_has_roles                │
│  ├─ slug                      ├─ role_id                     │
│  ├─ content (text)            ├─ model_type                  │
│  ├─ excerpt                   └─ model_id                    │
│  ├─ status (draft/published)                                │
│  ├─ published_at              model_has_permissions          │
│  ├─ author_id (FK users)      ├─ permission_id               │
│  ├─ category_id               ├─ model_type                  │
│  └─ timestamps                └─ model_id                    │
│                                                              │
│  categories                   activity_log (spatie)          │
│  ├─ id                        ├─ id                          │
│  ├─ name                      ├─ log_name                    │
│  ├─ slug                      ├─ description                 │
│  ├─ parent_id                 ├─ subject_type                │
│  └─ timestamps                ├─ subject_id                  │
│                               ├─ causer_type                 │
│  media                        ├─ causer_id                   │
│  ├─ id                        ├─ properties (JSON)           │
│  ├─ filename                  ├─ created_at                  │
│  ├─ path                      └─ updated_at                  │
│  ├─ mime_type                                               │
│  ├─ size                      pqrs                           │
│  └─ timestamps                ├─ id                          │
│                               ├─ tipo (peticion/queja/etc)   │
│  transparency_sections        ├─ nombre                      │
│  ├─ id                        ├─ email                       │
│  ├─ section_name              ├─ documento                   │
│  ├─ content (JSON)            ├─ asunto                      │
│  ├─ last_updated              ├─ mensaje                     │
│  ├─ next_update_due           ├─ estado (nuevo/en proceso)   │
│  ├─ responsible_user_id       ├─ respuesta                   │
│  └─ timestamps                └─ timestamps                  │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

## Capas de Seguridad

```
┌──────────────────────────────────────────────────────────────┐
│                    CAPA 1: INFRAESTRUCTURA                    │
├──────────────────────────────────────────────────────────────┤
│  • Firewall (UFW): Puertos 80, 443, 22                       │
│  • SSL/TLS 1.2+ (Let's Encrypt)                              │
│  • HSTS: max-age=31536000; includeSubDomains; preload        │
│  • Security Headers (X-Frame-Options, CSP, etc.)             │
└──────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────┐
│                    CAPA 2: APLICACIÓN                         │
├──────────────────────────────────────────────────────────────┤
│  • Rate Limiting: 5 login / 15 min, 100 API / min            │
│  • CORS: Strict origin policy                                │
│  • CSRF: Token validation                                    │
│  • Sanctum: HTTP-Only cookies                                │
│  • Input Validation: FormRequest + VeeValidate              │
└──────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────┐
│                    CAPA 3: DATOS                              │
├──────────────────────────────────────────────────────────────┤
│  • SQL Injection: Eloquent ORM (prepared statements)          │
│  • XSS: Blade escaping {{ }}                                 │
│  • Passwords: bcrypt (cost 12)                               │
│  • Sensitive data: Crypt::encryptString()                    │
│  • Database: constraints (NOT NULL, UNIQUE, FK)              │
└──────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────────────────────────────────────────────────┐
│                    CAPA 4: AUDITORÍA                          │
├──────────────────────────────────────────────────────────────┤
│  • Activity Log: Todos los CRUD operations                    │
│  • Request ID: UUID único por request                        │
│  • Logs: IP, user-agent, timestamp, cambios                  │
│  • Retention: 1 año mínimo                                   │
│  • Immutable: Logs no pueden editarse                        │
└──────────────────────────────────────────────────────────────┘
```

## Despliegue en Producción

```
┌────────────────────────────────────────────────────────────────┐
│                DigitalOcean Droplet (Ubuntu 24.04)             │
│                     VPS - 4GB RAM / 2 vCPUs                    │
├────────────────────────────────────────────────────────────────┤
│                                                                │
│  ┌──────────────────────────────────────────────────────────┐ │
│  │                  Docker Compose                           │ │
│  │                                                           │ │
│  │  ┌─────────┐  ┌─────────┐  ┌──────┐  ┌───────┐         │ │
│  │  │ Nginx   │  │ Backend │  │ MySQL│  │ Redis │         │ │
│  │  │ (proxy) │→ │ Laravel │→ │  8.0 │  │  7.x  │         │ │
│  │  └─────────┘  └─────────┘  └──────┘  └───────┘         │ │
│  │                                                           │ │
│  └──────────────────────────────────────────────────────────┘ │
│                                                                │
│  Frontends (builds estáticos):                                │
│  • /var/www/admin/dist/      → admin.alcaldia.gov.co         │
│  • /var/www/public/dist/     → www.alcaldia.gov.co           │
│                                                                │
│  Backups (cron diario 2 AM):                                  │
│  • /var/backups/cms/db_*.sql                                  │
│  • /var/backups/cms/files_*.tar.gz                            │
│                                                                │
└────────────────────────────────────────────────────────────────┘
                              │
                              ▼
                      ┌───────────────┐
                      │  Cloudflare   │
                      │    (CDN)      │
                      └───────────────┘
                              │
                              ▼
                      ┌───────────────┐
                      │   Usuarios    │
                      └───────────────┘
```

---

**Última actualización:** 2026-02-17  
**Versión:** 1.0.0
