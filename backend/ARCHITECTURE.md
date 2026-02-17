# Arquitectura del Backend - CMS Gubernamental

> **Versión:** 1.0  
> **Framework:** Laravel 11.48  
> **Patrón:** MVC + Repository Pattern  
> **Última actualización:** 17 de Febrero, 2026

---

## 📑 Tabla de Contenidos

1. [Visión General](#visión-general)
2. [Arquitectura de Capas](#arquitectura-de-capas)
3. [Patrones de Diseño](#patrones-de-diseño)
4. [Componentes Principales](#componentes-principales)
5. [Flujo de Datos](#flujo-de-datos)
6. [Seguridad](#seguridad)
7. [Base de Datos](#base-de-datos)
8. [APIs y Servicios](#apis-y-servicios)

---

## 1. Visión General

### 1.1 Propósito

El backend del CMS Gubernamental proporciona una API RESTful robusta y segura para gestionar contenidos institucionales, PQRS ciudadanas, y transparencia gubernamental, cumpliendo con las normativas colombianas vigentes.

### 1.2 Principios Arquitectónicos

- **Separación de Responsabilidades:** Cada componente tiene una responsabilidad clara
- **Modularidad:** Componentes independientes y reutilizables
- **Escalabilidad:** Preparado para crecimiento horizontal y vertical
- **Seguridad por Diseño:** Seguridad integrada desde el inicio
- **Mantenibilidad:** Código limpio, documentado y testeable

### 1.3 Stack Tecnológico

```
┌─────────────────────────────────────────┐
│           TECNOLOGÍAS BACKEND           │
├─────────────────────────────────────────┤
│ Framework:      Laravel 11.48           │
│ Lenguaje:       PHP 8.3+                │
│ Base de Datos:  MySQL 8.0 / SQLite      │
│ Caché:          Redis 7.x               │
│ Auth:           Laravel Sanctum 4.3     │
│ Permisos:       Spatie Permission 6.24  │
│ Logs:           Spatie Activity Log 4.11│
│ Testing:        PHPUnit 10.x            │
│ Code Style:     PSR-12                  │
└─────────────────────────────────────────┘
```

---

## 2. Arquitectura de Capas

### 2.1 Diagrama de Capas

```
┌─────────────────────────────────────────────────────┐
│                   CAPA DE CLIENTE                   │
│     (Vue 3 Admin Panel + Vue 3 Public Site)        │
└──────────────────────┬──────────────────────────────┘
                       │ HTTP/JSON
                       ▼
┌─────────────────────────────────────────────────────┐
│              CAPA DE PRESENTACIÓN (API)             │
│  ┌──────────────┐  ┌──────────────┐  ┌───────────┐ │
│  │ Controllers  │  │  Middleware  │  │  Routes   │ │
│  │   (API v1)   │  │ - Auth       │  │  api.php  │ │
│  │              │  │ - CORS       │  │           │ │
│  │              │  │ - Throttle   │  │           │ │
│  └──────────────┘  └──────────────┘  └───────────┘ │
└──────────────────────┬──────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────┐
│               CAPA DE LÓGICA DE NEGOCIO             │
│  ┌──────────────┐  ┌──────────────┐  ┌───────────┐ │
│  │   Services   │  │  Validators  │  │ Policies  │ │
│  │ - Content    │  │ FormRequests │  │ - RBAC    │ │
│  │ - PQRS       │  │              │  │           │ │
│  └──────────────┘  └──────────────┘  └───────────┘ │
└──────────────────────┬──────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────┐
│              CAPA DE ACCESO A DATOS                 │
│  ┌──────────────┐  ┌──────────────┐  ┌───────────┐ │
│  │    Models    │  │ Repositories │  │ Eloquent  │ │
│  │ - User       │  │  (opcional)  │  │    ORM    │ │
│  │ - Content    │  │              │  │           │ │
│  │ - Category   │  │              │  │           │ │
│  │ - Tag        │  │              │  │           │ │
│  │ - Media      │  │              │  │           │ │
│  │ - Pqrs       │  │              │  │           │ │
│  └──────────────┘  └──────────────┘  └───────────┘ │
└──────────────────────┬──────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────┐
│               CAPA DE PERSISTENCIA                  │
│  ┌──────────────┐  ┌──────────────┐  ┌───────────┐ │
│  │    MySQL     │  │    Redis     │  │  Storage  │ │
│  │  Database    │  │    Cache     │  │   Files   │ │
│  └──────────────┘  └──────────────┘  └───────────┘ │
└─────────────────────────────────────────────────────┘
```

### 2.2 Descripción de Capas

#### Capa de Presentación (API)
- **Responsabilidad:** Recibir requests HTTP, enrutar, autenticar
- **Componentes:** Controllers, Routes, Middleware
- **Tecnologías:** Laravel Routing, Sanctum, CORS

#### Capa de Lógica de Negocio
- **Responsabilidad:** Reglas de negocio, validaciones, permisos
- **Componentes:** Services, Validators, Policies
- **Tecnologías:** Spatie Permission, Custom Services

#### Capa de Acceso a Datos
- **Responsabilidad:** Interactuar con la base de datos
- **Componentes:** Eloquent Models, Relationships, Scopes
- **Tecnologías:** Eloquent ORM

#### Capa de Persistencia
- **Responsabilidad:** Almacenar datos de forma persistente
- **Componentes:** MySQL, Redis, File Storage
- **Tecnologías:** InnoDB, Redis, Laravel Storage

---

## 3. Patrones de Diseño

### 3.1 MVC (Model-View-Controller)

```
Request → Route → Controller → Model → Database
                     ↓
                  Response (JSON)
```

- **Model:** Eloquent models con lógica de datos
- **View:** JSON responses (API)
- **Controller:** Lógica de coordinación

### 3.2 Repository Pattern (Opcional)

Preparado para implementar en futuras versiones:

```php
interface ContentRepositoryInterface {
    public function find($id);
    public function all();
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}

class ContentRepository implements ContentRepositoryInterface {
    // Implementation
}
```

### 3.3 Service Layer Pattern

Para lógica de negocio compleja:

```php
class ContentService {
    public function publishContent($contentId) {
        // Business logic
    }
    
    public function schedulePublication($contentId, $date) {
        // Business logic
    }
}
```

### 3.4 Strategy Pattern

Para diferentes tipos de autenticación:

```php
interface AuthenticationStrategy {
    public function authenticate($credentials);
}

class SanctumAuthStrategy implements AuthenticationStrategy {
    // Implementation
}
```

### 3.5 Observer Pattern

Activity Log automático:

```php
class Content extends Model {
    use LogsActivity;
    
    // Los cambios se registran automáticamente
}
```

---

## 4. Componentes Principales

### 4.1 Sistema de Autenticación

```
┌──────────────────────────────────────────┐
│      FLUJO DE AUTENTICACIÓN              │
├──────────────────────────────────────────┤
│                                          │
│  1. POST /api/v1/login                  │
│     ↓                                    │
│  2. AuthController::login()             │
│     ↓                                    │
│  3. Validate credentials                │
│     ↓                                    │
│  4. Generate Sanctum token              │
│     ↓                                    │
│  5. Return token + user data            │
│                                          │
│  Subsequent requests:                   │
│  Header: Authorization: Bearer {token}  │
│     ↓                                    │
│  Middleware: auth:sanctum               │
│     ↓                                    │
│  Access granted to protected routes     │
└──────────────────────────────────────────┘
```

### 4.2 Sistema de Autorización (RBAC)

```
┌─────────────────────────────────────────────┐
│           MODELO RBAC                       │
├─────────────────────────────────────────────┤
│                                             │
│  User ─┬─ hasRole ──→ Role ─┬─ hasPermission ──→ Permission
│        │                    │                               
│        └─ can() ────────────┴─ Direct Permissions          
│                                                              
│  Roles:                                                     
│  - super-admin    (all permissions)                        
│  - editor         (content management)                     
│  - admin-transparencia (transparency)                      
│  - atencion-pqrs  (PQRS management)                       
│  - ciudadano      (public access)                          
│  - auditor        (read-only)                              
│                                                              
│  24 Permissions across 7 categories                        
└─────────────────────────────────────────────┘
```

### 4.3 Sistema de PQRS

```
┌──────────────────────────────────────────┐
│      FLUJO PQRS (Ley 1755/2015)         │
├──────────────────────────────────────────┤
│                                          │
│  1. Ciudadano crea PQRS (público)       │
│     POST /api/v1/pqrs                   │
│     ↓                                    │
│  2. Sistema genera folio único          │
│     Formato: PQRS-YYYYMMDD-XXXX         │
│     ↓                                    │
│  3. Estado inicial: "nuevo"             │
│     ↓                                    │
│  4. Ciudadano puede rastrear            │
│     GET /api/v1/pqrs/{folio}            │
│     ↓                                    │
│  5. Admin cambia estado                 │
│     PUT /api/v1/pqrs/{id}               │
│     Estados: nuevo → en_proceso →       │
│              resuelto → cerrado         │
│     ↓                                    │
│  6. Admin responde                      │
│     POST /api/v1/pqrs/{id}/respond      │
│     - respuesta (text)                  │
│     - respondido_por (user_id)          │
│     - respondido_at (timestamp)         │
│     ↓                                    │
│  7. Activity log registra todo          │
└──────────────────────────────────────────┘
```

### 4.4 Sistema de Contenidos

```
┌──────────────────────────────────────────┐
│      GESTIÓN DE CONTENIDOS              │
├──────────────────────────────────────────┤
│                                          │
│  Content                                 │
│    ├─ belongsTo: Author (User)          │
│    ├─ belongsTo: Category               │
│    ├─ belongsToMany: Tags               │
│    ├─ morphMany: Media                  │
│    ├─ Scopes:                           │
│    │   - published()                    │
│    │   - featured()                     │
│    └─ Methods:                          │
│        - incrementViews()                │
│        - auto-slug generation            │
│                                          │
│  Category (Hierarchical)                │
│    ├─ belongsTo: Parent                 │
│    ├─ hasMany: Children                 │
│    ├─ hasMany: Contents                 │
│    └─ Scopes:                           │
│        - active()                        │
│        - root()                          │
│                                          │
│  Full-text Search:                      │
│    - title, content indexed             │
│    - Fast search queries                │
└──────────────────────────────────────────┘
```

---

## 5. Flujo de Datos

### 5.1 Request Lifecycle

```
┌─────────────────────────────────────────────────────────┐
│                REQUEST LIFECYCLE                        │
└─────────────────────────────────────────────────────────┘

1. HTTP Request
   ↓
2. public/index.php (Entry Point)
   ↓
3. bootstrap/app.php (Application Bootstrap)
   ↓
4. Route Matching (routes/api.php)
   ↓
5. Middleware Pipeline
   ├─ CORS Middleware
   ├─ Throttle Middleware
   ├─ Auth Middleware (auth:sanctum)
   └─ Permission Middleware (permission:xxx)
   ↓
6. Controller Method
   ├─ Validate Request
   ├─ Execute Business Logic
   └─ Query Database
   ↓
7. Response Formation
   ├─ Transform Data (optional Resources)
   └─ JSON Response
   ↓
8. HTTP Response to Client
```

### 5.2 Flujo de Creación de Contenido

```
POST /api/v1/contents
Headers: Authorization: Bearer {token}
Body: {title, content, category_id, tags[], ...}
   ↓
1. auth:sanctum → Verify token
   ↓
2. permission:crear-contenidos → Check permission
   ↓
3. ContentController::store()
   ├─ Validate request data
   ├─ Create slug from title
   ├─ Set author_id = Auth::id()
   ├─ Create Content record
   ├─ Attach tags
   └─ Log activity
   ↓
4. Return 201 Created
   {content: {...}, message: "Created"}
```

### 5.3 Flujo de Autenticación

```
POST /api/v1/login
Body: {email, password}
   ↓
1. AuthController::login()
   ├─ Validate credentials
   ├─ Attempt authentication
   ├─ Generate Sanctum token
   ├─ Load user with roles & permissions
   └─ Return token + user data
   ↓
2. Client stores token
   ↓
3. Subsequent requests include:
   Header: Authorization: Bearer {token}
   ↓
4. auth:sanctum middleware validates
```

---

## 6. Seguridad

### 6.1 Capas de Seguridad

```
┌─────────────────────────────────────────┐
│        CAPAS DE SEGURIDAD               │
├─────────────────────────────────────────┤
│                                         │
│  Nivel 1: Infraestructura              │
│  ├─ HTTPS/TLS                          │
│  ├─ Firewall                           │
│  └─ Rate Limiting                      │
│                                         │
│  Nivel 2: Aplicación                   │
│  ├─ CSRF Protection                    │
│  ├─ XSS Prevention                     │
│  ├─ SQL Injection Prevention           │
│  └─ CORS Policy                        │
│                                         │
│  Nivel 3: Autenticación                │
│  ├─ Sanctum Tokens                     │
│  ├─ Password Hashing (bcrypt)          │
│  └─ Token Expiration                   │
│                                         │
│  Nivel 4: Autorización                 │
│  ├─ RBAC (Spatie Permission)           │
│  ├─ Permission Middleware              │
│  └─ Guard Clauses                      │
│                                         │
│  Nivel 5: Auditoría                    │
│  ├─ Activity Logging                   │
│  ├─ Request Logging                    │
│  └─ Error Logging                      │
└─────────────────────────────────────────┘
```

### 6.2 Prácticas de Seguridad Implementadas

#### Input Validation
```php
// FormRequest validation
public function rules() {
    return [
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
    ];
}
```

#### SQL Injection Prevention
```php
// Always use Eloquent or Query Builder
Content::where('slug', $slug)->first();  // ✅ Safe
DB::table('contents')->where('id', $id)->get();  // ✅ Safe

// Never use raw queries with user input
DB::raw("SELECT * FROM users WHERE email = '$email'");  // ❌ Dangerous
```

#### XSS Prevention
```php
// Blade auto-escapes
{{ $content->title }}  // ✅ Escaped

// JSON responses are safe
return response()->json(['title' => $title]);  // ✅ Escaped
```

#### CSRF Protection
```php
// Sanctum CSRF token in cookies
// Frontend includes X-XSRF-TOKEN header
```

---

## 7. Base de Datos

### 7.1 Diagrama de Relaciones

```
┌──────────────┐
│    users     │
└──────┬───────┘
       │ 1
       │
       │ N
┌──────▼───────┐        ┌──────────────┐
│   contents   ├───N────┤ content_tag  │
└──────┬───────┘        └──────┬───────┘
       │ N                     │ N
       │                       │
       │ 1              ┌──────▼───────┐
┌──────▼───────┐        │     tags     │
│  categories  │        └──────────────┘
└──────┬───────┘
       │ 1
       │
       │ N (self-reference)
       └─────►


┌──────────────┐        ┌──────────────┐
│     pqrs     ├───N────┤    users     │
└──────────────┘   1    │(respondido_por)
                        └──────────────┘


┌──────────────┐
│    media     │ (Polymorphic)
└──────┬───────┘
       │
       ├─ mediable_type = Content
       └─ mediable_type = Other
```

### 7.2 Índices Importantes

```sql
-- Performance Indexes
CREATE INDEX idx_contents_slug ON contents(slug);
CREATE INDEX idx_contents_category ON contents(category_id);
CREATE INDEX idx_contents_author ON contents(author_id);
CREATE INDEX idx_contents_published ON contents(published_at);

-- Full-Text Indexes
CREATE FULLTEXT INDEX idx_contents_search ON contents(title, content);
CREATE FULLTEXT INDEX idx_pqrs_search ON pqrs(asunto, mensaje);

-- Unique Constraints
ALTER TABLE contents ADD UNIQUE(slug);
ALTER TABLE categories ADD UNIQUE(slug);
ALTER TABLE tags ADD UNIQUE(slug);
```

---

## 8. APIs y Servicios

### 8.1 Versionamiento de API

```
/api/v1/*  → Versión actual (estable)
/api/v2/*  → Versión futura (en desarrollo)

Estrategia de versionamiento:
- URL-based versioning
- Backward compatibility garantizada
- Deprecation notices con 6 meses de anticipación
```

### 8.2 Respuestas Estándar

```json
// Success Response
{
    "success": true,
    "data": {...},
    "message": "Operation successful"
}

// Error Response
{
    "success": false,
    "error": "Error message",
    "errors": {...},  // Validation errors
    "code": 400
}

// Paginated Response
{
    "data": [...],
    "links": {...},
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 100
    }
}
```

### 8.3 Rate Limiting

```php
// API Throttling
Route::middleware('throttle:60,1')->group(function () {
    // 60 requests per minute
});

// Login Throttling
Route::middleware('throttle:5,1')->group(function () {
    // 5 login attempts per minute
});
```

---

## 9. Escalabilidad

### 9.1 Estrategias de Escalabilidad

#### Horizontal Scaling
- Load balancer con múltiples instancias
- Session storage en Redis (compartido)
- File storage en S3 (compartido)

#### Vertical Scaling
- Optimización de queries
- Database indexing
- Caching estratégico

#### Caching Strategy
```
┌──────────────────────────────────────┐
│         ESTRATEGIA DE CACHÉ          │
├──────────────────────────────────────┤
│                                      │
│  Level 1: Application Cache (Redis) │
│  - Query results                    │
│  - Computed values                  │
│  - Session data                     │
│                                      │
│  Level 2: HTTP Cache               │
│  - Static responses                 │
│  - ETags                            │
│  - Last-Modified headers            │
│                                      │
│  Level 3: Database Query Cache     │
│  - Eloquent caching                 │
│  - Prepared statements              │
└──────────────────────────────────────┘
```

---

## 10. Monitoreo y Logging

### 10.1 Logs Implementados

```php
// Application Logs
Log::info('User logged in', ['user_id' => $user->id]);
Log::error('Failed to process payment', ['error' => $e]);

// Activity Logs (Spatie)
activity()
    ->causedBy($user)
    ->performedOn($content)
    ->log('Content updated');

// Query Logs (Development)
DB::enableQueryLog();
```

### 10.2 Métricas Clave

- Request count
- Response times
- Error rates
- Database query times
- Cache hit rates
- Active users
- PQRS creation rate

---

## 11. Próximos Pasos

### 11.1 Mejoras Planificadas

1. **API Resources** - Transformación consistente de datos
2. **Form Requests** - Validación más robusta
3. **Service Layer** - Lógica de negocio separada
4. **Repository Pattern** - Abstracción de datos
5. **Event Sourcing** - Historial completo de eventos
6. **CQRS** - Separación lectura/escritura para alto rendimiento

### 11.2 Optimizaciones Futuras

- GraphQL endpoint (adicional a REST)
- WebSocket support para real-time
- Elasticsearch para búsquedas avanzadas
- Queue system para tareas pesadas
- Multi-tenancy para múltiples entidades

---

## 12. Referencias

### 12.1 Documentación Relacionada

- [API Documentation](./API_DOCUMENTATION.md)
- [Setup Guide](./SETUP.md)
- [Testing Guide](./TESTING.md)
- [Backend Compliance](./BACKEND_COMPLIANCE.md)

### 12.2 Recursos Externos

- [Laravel Documentation](https://laravel.com/docs/11.x)
- [Sanctum Documentation](https://laravel.com/docs/11.x/sanctum)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)
- [PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)

---

**Última actualización:** 17 de Febrero, 2026  
**Mantenido por:** Equipo de Desarrollo CMS Gubernamental  
**Contacto:** soporte@alcaldia.gov.co

---

*Arquitectura diseñada para servir a la ciudadanía colombiana con excelencia técnica* 🇨🇴
