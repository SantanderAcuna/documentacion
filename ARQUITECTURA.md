# Arquitectura del Sistema CRUD - Vue 3 + TypeScript

## Diagrama de Arquitectura

```
┌─────────────────────────────────────────────────────────────────────┐
│                         FRONTEND (Vue 3 + TS)                       │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │                    VIEWS (Páginas)                           │ │
│  │  ┌────────────────┐  ┌────────────────┐  ┌──────────────┐  │ │
│  │  │  ServerList    │  │  ServerForm    │  │   SSHKeys    │  │ │
│  │  │                │  │                │  │              │  │ │
│  │  │ - Tabla        │  │ - Crear        │  │ - Lista      │  │ │
│  │  │ - Paginación   │  │ - Editar       │  │ - Gestión    │  │ │
│  │  │ - Búsqueda     │  │ - Validar      │  │              │  │ │
│  │  │ - Filtros      │  │ - Guardar      │  │              │  │ │
│  │  └────────────────┘  └────────────────┘  └──────────────┘  │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                              │                                     │
│                              │ uses                                │
│                              ▼                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │              COMPOSABLES (Lógica Reutilizable)               │ │
│  │                     - TanStack Query                         │ │
│  │                     - Custom Hooks                           │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                              │                                     │
│                              │ uses                                │
│                              ▼                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │                    SERVICES (API Layer)                      │ │
│  │  ┌────────────────────────────────────────────────────────┐ │ │
│  │  │  vpsServerService                                      │ │ │
│  │  │  - getAll(page, perPage)                               │ │ │
│  │  │  - getById(id)                                         │ │ │
│  │  │  - create(data)                                        │ │ │
│  │  │  - update(id, data)                                    │ │ │
│  │  │  - delete(id)                                          │ │ │
│  │  │  - testConnection(id)                                  │ │ │
│  │  └────────────────────────────────────────────────────────┘ │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                              │                                     │
│                              │ uses                                │
│                              ▼                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │              AXIOS CLIENT (HTTP Layer)                       │ │
│  │  - Base URL: /api/v1                                         │ │
│  │  - Timeout: 30s                                              │ │
│  │  - withCredentials: true (Sanctum)                           │ │
│  │  - Interceptors:                                             │ │
│  │    • Request: CSRF Token                                     │ │
│  │    • Response: Error Handling (401, 403, 404, 422, 500)     │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                              │                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │                   STATE MANAGEMENT                           │ │
│  │  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────┐ │ │
│  │  │ TanStack Query  │  │     Pinia       │  │ ref/reactive│ │ │
│  │  │ - Server data   │  │ - Global state  │  │ - Local     │ │ │
│  │  │ - Cache         │  │ - User info     │  │   state     │ │ │
│  │  │ - Mutations     │  │ - Preferences   │  │             │ │ │
│  │  └─────────────────┘  └─────────────────┘  └─────────────┘ │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │             TYPES & VALIDATIONS (TypeScript)                 │ │
│  │  ┌────────────────────┐  ┌────────────────────────────────┐ │ │
│  │  │ entities.ts        │  │ validations.ts                 │ │ │
│  │  │ - User             │  │ - createVPSServerSchema        │ │ │
│  │  │ - VPSServer        │  │ - updateVPSServerSchema        │ │ │
│  │  │ - SSHKey           │  │ - Yup schemas                  │ │ │
│  │  │ - ApiResponse      │  │ - Regex validators             │ │ │
│  │  └────────────────────┘  └────────────────────────────────┘ │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │                    UI COMPONENTS                             │ │
│  │  - Bootstrap 5 (responsive)                                  │ │
│  │  - FontAwesome 6 Free (icons)                                │ │
│  │  - Vue Toastification (notifications)                        │ │
│  └──────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
                              │
                              │ HTTP/REST API
                              ▼
┌─────────────────────────────────────────────────────────────────────┐
│                       BACKEND (Laravel 12)                          │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │                    API ENDPOINTS                             │ │
│  │  GET    /api/v1/servers                                      │ │
│  │  GET    /api/v1/servers/:id                                  │ │
│  │  POST   /api/v1/servers                                      │ │
│  │  PUT    /api/v1/servers/:id                                  │ │
│  │  DELETE /api/v1/servers/:id                                  │ │
│  │  POST   /api/v1/servers/:id/test-connection                  │ │
│  │  GET    /api/v1/servers/:id/status                           │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                              │                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │                  AUTHENTICATION                              │ │
│  │  - Laravel Sanctum (cookies HTTP-Only)                       │ │
│  │  - CSRF Protection                                           │ │
│  │  - Session Management                                        │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                              │                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │                   MODELS & CONTROLLERS                       │ │
│  │  - VPSServer Model                                           │ │
│  │  - ServerController                                          │ │
│  │  - Validation Rules                                          │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                              │                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │                     DATABASE                                 │ │
│  │  - MySQL 8.0+ (InnoDB, utf8mb4)                              │ │
│  │  - Redis (cache & sessions)                                  │ │
│  └──────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

## Flujo de Datos

### 1. Listar Servidores (Read)

```
[Usuario] → [ServerList.vue]
             │
             ▼
         [useQuery]
             │
             ▼
     [vpsServerService.getAll()]
             │
             ▼
        [Axios GET /api/v1/servers]
             │
             ▼
         [Laravel API]
             │
             ▼
         [MySQL Database]
             │
             ▼
         [JSON Response]
             │
             ▼
     [TanStack Query Cache]
             │
             ▼
         [ServerList.vue]
             │
             ▼
     [Render Table with data]
```

### 2. Crear Servidor (Create)

```
[Usuario] → [Click "Nuevo Servidor"]
             │
             ▼
         [Router → ServerForm.vue]
             │
             ▼
     [Usuario llena formulario]
             │
             ▼
         [Validación Yup (client-side)]
             │
             ▼
         [Click "Crear"]
             │
             ▼
     [useMutation → createMutation]
             │
             ▼
     [vpsServerService.create(data)]
             │
             ▼
     [Axios POST /api/v1/servers]
             │
             ▼
         [Laravel API]
             │
             ▼
     [Validación (server-side)]
             │
             ▼
     [Guardar en MySQL]
             │
             ▼
         [JSON Response]
             │
             ▼
     [onSuccess → invalidate cache]
             │
             ▼
     [Toast: "Servidor creado exitosamente"]
             │
             ▼
     [Router → ServerList.vue]
```

### 3. Editar Servidor (Update)

```
[Usuario] → [Click "Editar" en tabla]
             │
             ▼
     [Router → ServerForm.vue con :id]
             │
             ▼
     [isEditMode = true]
             │
             ▼
     [useQuery → getById(id)]
             │
             ▼
     [vpsServerService.getById(id)]
             │
             ▼
     [Axios GET /api/v1/servers/:id]
             │
             ▼
         [Laravel API]
             │
             ▼
         [MySQL Database]
             │
             ▼
     [JSON Response con datos del servidor]
             │
             ▼
     [watch(serverData) → cargar en formData]
             │
             ▼
     [Formulario poblado con datos]
             │
             ▼
     [Usuario modifica campos]
             │
             ▼
     [Validación Yup en tiempo real]
             │
             ▼
     [Click "Actualizar"]
             │
             ▼
     [useMutation → updateMutation]
             │
             ▼
     [vpsServerService.update(id, data)]
             │
             ▼
     [Axios PUT /api/v1/servers/:id]
             │
             ▼
         [Laravel API]
             │
             ▼
     [Actualizar en MySQL]
             │
             ▼
     [JSON Response]
             │
             ▼
     [onSuccess → invalidate cache]
             │
             ▼
     [Toast: "Servidor actualizado"]
             │
             ▼
     [Router → ServerList.vue]
```

### 4. Eliminar Servidor (Delete)

```
[Usuario] → [Click "Eliminar" en tabla]
             │
             ▼
     [showDeleteConfirm = true]
             │
             ▼
     [Modal: "¿Está seguro?"]
             │
             ▼
     [Usuario click "Confirmar"]
             │
             ▼
     [useMutation → deleteMutation]
             │
             ▼
     [vpsServerService.delete(id)]
             │
             ▼
     [Axios DELETE /api/v1/servers/:id]
             │
             ▼
         [Laravel API]
             │
             ▼
     [Soft/Hard delete en MySQL]
             │
             ▼
     [JSON Response]
             │
             ▼
     [onSuccess → invalidate cache]
             │
             ▼
     [Toast: "Servidor eliminado"]
             │
             ▼
     [Cerrar modal]
             │
             ▼
     [Re-render tabla sin el servidor]
```

## Manejo de Errores

```
┌────────────────────┐
│  Error Ocurre      │
└────────────────────┘
          │
          ▼
┌────────────────────────────────────┐
│  Axios Interceptor detecta error   │
└────────────────────────────────────┘
          │
          ├──[401]──→ No autorizado → Redirect a login
          │
          ├──[403]──→ Sin permisos → Toast error
          │
          ├──[404]──→ No encontrado → Toast error
          │
          ├──[422]──→ Validación → Mostrar errores en campos
          │
          └──[500]──→ Error servidor → Toast error
          │
          ▼
┌────────────────────────────────────┐
│  Promise.reject(error)             │
└────────────────────────────────────┘
          │
          ▼
┌────────────────────────────────────┐
│  Mutation/Query onError callback   │
└────────────────────────────────────┘
          │
          ▼
┌────────────────────────────────────┐
│  Toast notification                │
│  "Error: [mensaje]"                │
└────────────────────────────────────┘
```

## Validación en Capas

```
┌─────────────────────────────────────────────┐
│         CAPA 1: Client-Side (Yup)           │
│                                             │
│  ✅ Validación en tiempo real               │
│  ✅ Mensajes de error inmediatos            │
│  ✅ Previene envío de datos inválidos       │
│  ✅ Mejor UX (feedback instantáneo)         │
└─────────────────────────────────────────────┘
                    │
                    │ Si pasa validación
                    ▼
┌─────────────────────────────────────────────┐
│       CAPA 2: Server-Side (Laravel)         │
│                                             │
│  ✅ Validación con Form Request             │
│  ✅ Reglas de negocio complejas             │
│  ✅ Validación de permisos                  │
│  ✅ Protección contra manipulación          │
└─────────────────────────────────────────────┘
                    │
                    │ Si pasa validación
                    ▼
┌─────────────────────────────────────────────┐
│       CAPA 3: Database (MySQL)              │
│                                             │
│  ✅ Constraints (UNIQUE, NOT NULL)          │
│  ✅ Foreign Keys                            │
│  ✅ Triggers                                │
│  ✅ Última línea de defensa                 │
└─────────────────────────────────────────────┘
```

## Cache Strategy (TanStack Query)

```
┌──────────────────────────────────────────────┐
│         Primera Petición (Miss)              │
│                                              │
│  [Component] → [useQuery]                    │
│                    │                         │
│                    ▼                         │
│              [No hay cache]                  │
│                    │                         │
│                    ▼                         │
│            [Fetch desde API]                 │
│                    │                         │
│                    ▼                         │
│          [Guardar en cache]                  │
│                    │                         │
│                    ▼                         │
│            [Retornar datos]                  │
└──────────────────────────────────────────────┘

┌──────────────────────────────────────────────┐
│      Peticiones Subsiguientes (Hit)          │
│                                              │
│  [Component] → [useQuery]                    │
│                    │                         │
│                    ▼                         │
│           [Cache existe y es fresh]          │
│                    │                         │
│                    ▼                         │
│      [Retornar desde cache (instant)]        │
│                    │                         │
│                    ▼                         │
│    [Background refetch si es stale]          │
└──────────────────────────────────────────────┘

┌──────────────────────────────────────────────┐
│         Después de Mutation                  │
│                                              │
│  [Create/Update/Delete] → [Mutation]         │
│                    │                         │
│                    ▼                         │
│            [Ejecutar mutación]               │
│                    │                         │
│                    ▼                         │
│              [onSuccess]                     │
│                    │                         │
│                    ▼                         │
│    [invalidateQueries(['servers'])]          │
│                    │                         │
│                    ▼                         │
│           [Cache invalidado]                 │
│                    │                         │
│                    ▼                         │
│      [Próxima query hace refetch]            │
└──────────────────────────────────────────────┘
```

---

**Nota**: Esta arquitectura sigue las mejores prácticas de Vue 3, TypeScript, y desarrollo moderno de frontend, garantizando un código mantenible, escalable y profesional.
