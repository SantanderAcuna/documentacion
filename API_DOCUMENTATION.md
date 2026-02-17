# Documentación de API - Portal de Configuración VPS

## Información General

### Base URL
- **Desarrollo:** `http://localhost:8000/api/v1`
- **Producción:** `https://api.vps-portal.com/api/v1`

### Autenticación
Todas las peticiones (excepto login, register, password reset) requieren autenticación mediante Laravel Sanctum con cookies HTTP-Only.

#### Headers Requeridos
```http
Accept: application/json
Content-Type: application/json
X-CSRF-TOKEN: {token}
```

#### Cookies
```
XSRF-TOKEN: {csrf_token}
laravel_session: {session_id}
```

---

## 1. Autenticación

### 1.1 Registro de Usuario

**Endpoint:** `POST /auth/register`

**Descripción:** Registra un nuevo usuario en el sistema

**Request Body:**
```json
{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "SecureP@ss123",
  "password_confirmation": "SecureP@ss123"
}
```

**Validaciones:**
- `name`: requerido, string, mín 3 caracteres, máx 255
- `email`: requerido, email válido, único en la base de datos
- `password`: requerido, mín 8 caracteres, debe contener mayúsculas, minúsculas y números
- `password_confirmation`: requerido, debe coincidir con password

**Response Success (201):**
```json
{
  "success": true,
  "message": "Usuario registrado exitosamente",
  "data": {
    "user": {
      "id": 1,
      "name": "Juan Pérez",
      "email": "juan@example.com",
      "role": "viewer",
      "created_at": "2026-02-17T18:00:00.000000Z"
    },
    "token": null
  }
}
```

**Response Error (422):**
```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "email": ["El email ya ha sido registrado"],
    "password": ["La contraseña debe tener al menos 8 caracteres"]
  }
}
```

**Códigos de Estado:**
- `201`: Usuario creado exitosamente
- `422`: Error de validación
- `500`: Error del servidor

---

### 1.2 Inicio de Sesión

**Endpoint:** `POST /auth/login`

**Descripción:** Autentica un usuario y establece cookies de sesión

**Request Body:**
```json
{
  "email": "juan@example.com",
  "password": "SecureP@ss123",
  "remember": true
}
```

**Validaciones:**
- `email`: requerido, email válido
- `password`: requerido
- `remember`: opcional, boolean (default: false)

**Response Success (200):**
```json
{
  "success": true,
  "message": "Sesión iniciada exitosamente",
  "data": {
    "user": {
      "id": 1,
      "name": "Juan Pérez",
      "email": "juan@example.com",
      "role": "editor",
      "permissions": ["view_documents", "create_documents", "edit_own_documents"]
    }
  }
}
```

**Response Error (401):**
```json
{
  "success": false,
  "message": "Credenciales inválidas",
  "errors": {
    "email": ["Las credenciales proporcionadas son incorrectas"]
  }
}
```

**Rate Limiting:**
- Máximo 5 intentos por minuto por IP
- Response 429 si se excede

---

### 1.3 Cerrar Sesión

**Endpoint:** `POST /auth/logout`

**Descripción:** Cierra la sesión del usuario actual

**Headers:** Requiere autenticación

**Response Success (200):**
```json
{
  "success": true,
  "message": "Sesión cerrada exitosamente"
}
```

---

### 1.4 Usuario Actual

**Endpoint:** `GET /auth/user`

**Descripción:** Obtiene información del usuario autenticado

**Headers:** Requiere autenticación

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "role": "editor",
    "permissions": ["view_documents", "create_documents", "edit_own_documents"],
    "avatar": "https://spaces.digitalocean.com/avatars/juan.jpg",
    "created_at": "2026-01-15T10:00:00.000000Z",
    "updated_at": "2026-02-17T18:00:00.000000Z"
  }
}
```

---

### 1.5 Recuperación de Contraseña

**Endpoint:** `POST /auth/forgot-password`

**Request Body:**
```json
{
  "email": "juan@example.com"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Hemos enviado un enlace de recuperación a tu email"
}
```

**Endpoint:** `POST /auth/reset-password`

**Request Body:**
```json
{
  "email": "juan@example.com",
  "token": "abc123def456",
  "password": "NewSecureP@ss123",
  "password_confirmation": "NewSecureP@ss123"
}
```

---

## 2. Documentación

### 2.1 Listar Documentos

**Endpoint:** `GET /documents`

**Descripción:** Obtiene lista paginada de documentos

**Query Parameters:**
- `page`: número de página (default: 1)
- `per_page`: documentos por página (default: 20, max: 100)
- `category_id`: filtrar por categoría
- `tag`: filtrar por tag
- `search`: búsqueda full-text
- `status`: published | draft
- `sort`: created_at | updated_at | title | views (default: created_at)
- `order`: asc | desc (default: desc)

**Ejemplo Request:**
```
GET /documents?page=1&per_page=20&category_id=2&search=ssh&sort=views&order=desc
```

**Response Success (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Configuración SSH Avanzada",
      "slug": "configuracion-ssh-avanzada",
      "summary": "Guía completa para configurar SSH de manera segura",
      "content_preview": "En esta guía aprenderás a...",
      "category": {
        "id": 2,
        "name": "SSH",
        "slug": "ssh"
      },
      "tags": [
        {"id": 1, "name": "seguridad"},
        {"id": 2, "name": "linux"}
      ],
      "author": {
        "id": 5,
        "name": "María González",
        "avatar": "https://..."
      },
      "status": "published",
      "views": 1523,
      "favorites_count": 45,
      "is_favorited": false,
      "created_at": "2026-01-20T12:00:00.000000Z",
      "updated_at": "2026-02-15T14:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 20,
    "to": 20,
    "total": 95
  },
  "links": {
    "first": "http://api.example.com/documents?page=1",
    "last": "http://api.example.com/documents?page=5",
    "prev": null,
    "next": "http://api.example.com/documents?page=2"
  }
}
```

---

### 2.2 Obtener Documento

**Endpoint:** `GET /documents/{id}`

**Descripción:** Obtiene un documento específico por ID

**Path Parameters:**
- `id`: ID del documento

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Configuración SSH Avanzada",
    "slug": "configuracion-ssh-avanzada",
    "summary": "Guía completa para configurar SSH de manera segura",
    "content": "# Configuración SSH\n\n## Introducción\n\nSSH (Secure Shell)...",
    "content_html": "<h1>Configuración SSH</h1><h2>Introducción</h2>...",
    "category": {
      "id": 2,
      "name": "SSH",
      "slug": "ssh",
      "description": "Configuración y seguridad SSH"
    },
    "tags": [
      {"id": 1, "name": "seguridad", "slug": "seguridad"},
      {"id": 2, "name": "linux", "slug": "linux"}
    ],
    "author": {
      "id": 5,
      "name": "María González",
      "email": "maria@example.com",
      "avatar": "https://..."
    },
    "status": "published",
    "views": 1523,
    "favorites_count": 45,
    "is_favorited": false,
    "version": 3,
    "created_at": "2026-01-20T12:00:00.000000Z",
    "updated_at": "2026-02-15T14:30:00.000000Z",
    "published_at": "2026-01-20T15:00:00.000000Z"
  }
}
```

**Response Error (404):**
```json
{
  "success": false,
  "message": "Documento no encontrado"
}
```

---

### 2.3 Crear Documento

**Endpoint:** `POST /documents`

**Descripción:** Crea un nuevo documento

**Permisos Requeridos:** `create_documents` (Editor, Admin, SuperAdmin)

**Request Body:**
```json
{
  "title": "Instalación de Nginx",
  "summary": "Guía para instalar y configurar Nginx en Ubuntu 24.04",
  "content": "# Instalación de Nginx\n\n## Requisitos\n...",
  "category_id": 3,
  "tags": ["nginx", "web-server", "ubuntu"],
  "status": "draft",
  "images": ["uuid-1.jpg", "uuid-2.png"]
}
```

**Validaciones:**
- `title`: requerido, único, string, mín 10 caracteres, máx 255
- `summary`: requerido, string, mín 20 caracteres, máx 500
- `content`: requerido, string, mín 50 caracteres
- `category_id`: requerido, existe en categorías
- `tags`: opcional, array, máx 10 tags
- `status`: requerido, enum: draft | published
- `images`: opcional, array de UUIDs de imágenes previamente subidas

**Response Success (201):**
```json
{
  "success": true,
  "message": "Documento creado exitosamente",
  "data": {
    "id": 25,
    "title": "Instalación de Nginx",
    "slug": "instalacion-de-nginx",
    "status": "draft",
    "created_at": "2026-02-17T18:30:00.000000Z"
  }
}
```

---

### 2.4 Actualizar Documento

**Endpoint:** `PUT /documents/{id}`

**Descripción:** Actualiza un documento existente

**Permisos Requeridos:** 
- Editor: solo sus propios documentos
- Admin/SuperAdmin: todos los documentos

**Request Body:** (igual que crear, todos los campos opcionales)
```json
{
  "title": "Instalación y Configuración de Nginx",
  "content": "# Instalación de Nginx\n\n## Actualizado...",
  "status": "published"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Documento actualizado exitosamente",
  "data": {
    "id": 25,
    "version": 2,
    "updated_at": "2026-02-17T18:45:00.000000Z"
  }
}
```

**Response Error (403):**
```json
{
  "success": false,
  "message": "No tienes permiso para editar este documento"
}
```

---

### 2.5 Eliminar Documento

**Endpoint:** `DELETE /documents/{id}`

**Descripción:** Elimina un documento

**Permisos Requeridos:** Admin, SuperAdmin

**Response Success (200):**
```json
{
  "success": true,
  "message": "Documento eliminado exitosamente"
}
```

---

## 3. Categorías

### 3.1 Listar Categorías

**Endpoint:** `GET /categories`

**Response Success (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "SSH",
      "slug": "ssh",
      "description": "Configuración y seguridad SSH",
      "icon": "fa-terminal",
      "color": "#2b6cb0",
      "documents_count": 15,
      "order": 1
    }
  ]
}
```

---

### 3.2 Crear Categoría

**Endpoint:** `POST /categories`

**Permisos Requeridos:** Admin, SuperAdmin

**Request Body:**
```json
{
  "name": "Bases de Datos",
  "description": "MySQL, PostgreSQL, MongoDB",
  "icon": "fa-database",
  "color": "#48bb78"
}
```

---

## 4. Búsqueda

### 4.1 Búsqueda Global

**Endpoint:** `GET /search`

**Query Parameters:**
- `q`: término de búsqueda (requerido, mín 3 caracteres)
- `category_id`: filtrar por categoría
- `tags`: array de tags
- `page`: número de página
- `per_page`: resultados por página

**Ejemplo:**
```
GET /search?q=configurar%20firewall&category_id=2&page=1
```

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "query": "configurar firewall",
    "results": [
      {
        "id": 10,
        "title": "Configuración de Firewall UFW",
        "summary": "...",
        "relevance": 0.95,
        "highlights": {
          "content": "...cómo <mark>configurar</mark> el <mark>firewall</mark>..."
        }
      }
    ],
    "total": 12,
    "page": 1,
    "per_page": 20
  }
}
```

---

### 4.2 Sugerencias de Búsqueda

**Endpoint:** `GET /search/suggestions`

**Query Parameters:**
- `q`: término parcial (mín 2 caracteres)

**Response Success (200):**
```json
{
  "success": true,
  "data": [
    "configurar ssh",
    "configurar firewall",
    "configurar nginx"
  ]
}
```

---

## 5. Favoritos

### 5.1 Listar Favoritos del Usuario

**Endpoint:** `GET /favorites`

**Headers:** Requiere autenticación

**Response Success (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 15,
      "document": {
        "id": 1,
        "title": "Configuración SSH",
        "slug": "configuracion-ssh"
      },
      "created_at": "2026-02-10T12:00:00.000000Z"
    }
  ]
}
```

---

### 5.2 Agregar a Favoritos

**Endpoint:** `POST /documents/{id}/favorite`

**Response Success (201):**
```json
{
  "success": true,
  "message": "Documento agregado a favoritos"
}
```

---

### 5.3 Quitar de Favoritos

**Endpoint:** `DELETE /documents/{id}/favorite`

**Response Success (200):**
```json
{
  "success": true,
  "message": "Documento removido de favoritos"
}
```

---

## 6. Usuarios (Admin)

### 6.1 Listar Usuarios

**Endpoint:** `GET /admin/users`

**Permisos Requeridos:** Admin, SuperAdmin

**Query Parameters:**
- `page`, `per_page`: paginación
- `role`: filtrar por rol
- `search`: buscar por nombre o email
- `status`: active | suspended

**Response Success (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 5,
      "name": "María González",
      "email": "maria@example.com",
      "role": "editor",
      "status": "active",
      "documents_count": 12,
      "last_login": "2026-02-17T10:30:00.000000Z",
      "created_at": "2026-01-10T08:00:00.000000Z"
    }
  ],
  "meta": {
    "total": 45,
    "current_page": 1,
    "last_page": 3
  }
}
```

---

### 6.2 Actualizar Rol de Usuario

**Endpoint:** `PUT /admin/users/{id}/role`

**Permisos Requeridos:** SuperAdmin

**Request Body:**
```json
{
  "role": "admin"
}
```

**Validaciones:**
- `role`: requerido, enum: viewer | editor | admin | superadmin

---

### 6.3 Suspender Usuario

**Endpoint:** `POST /admin/users/{id}/suspend`

**Permisos Requeridos:** Admin, SuperAdmin

**Response Success (200):**
```json
{
  "success": true,
  "message": "Usuario suspendido exitosamente"
}
```

---

## 7. Upload de Archivos

### 7.1 Subir Imagen

**Endpoint:** `POST /upload/image`

**Permisos Requeridos:** Editor, Admin, SuperAdmin

**Request:** `multipart/form-data`
```
Content-Type: multipart/form-data
```

**Form Data:**
- `image`: archivo de imagen (jpg, png, gif, webp)

**Validaciones:**
- Tipos permitidos: jpg, jpeg, png, gif, webp
- Tamaño máximo: 5MB
- Dimensiones recomendadas: hasta 2048x2048px

**Response Success (201):**
```json
{
  "success": true,
  "message": "Imagen subida exitosamente",
  "data": {
    "uuid": "9f8e7d6c-5b4a-3210-1234-56789abcdef0",
    "filename": "nginx-config.png",
    "url": "https://spaces.digitalocean.com/vps-portal/images/9f8e7d6c.png",
    "size": 245678,
    "mime_type": "image/png",
    "dimensions": {
      "width": 1200,
      "height": 800
    }
  }
}
```

---

## 8. Analytics (Admin)

### 8.1 Dashboard de Estadísticas

**Endpoint:** `GET /admin/analytics/dashboard`

**Permisos Requeridos:** Admin, SuperAdmin

**Query Parameters:**
- `period`: today | week | month | year (default: month)

**Response Success (200):**
```json
{
  "success": true,
  "data": {
    "users": {
      "total": 156,
      "new_this_period": 12,
      "active": 89
    },
    "documents": {
      "total": 95,
      "published": 87,
      "draft": 8,
      "new_this_period": 5
    },
    "views": {
      "total": 45678,
      "this_period": 3456
    },
    "top_documents": [
      {
        "id": 1,
        "title": "Configuración SSH",
        "views": 1523
      }
    ],
    "top_categories": [
      {
        "id": 2,
        "name": "SSH",
        "documents": 15,
        "views": 8945
      }
    ]
  }
}
```

---

## 9. Logs de Actividad (Admin)

### 9.1 Listar Logs

**Endpoint:** `GET /admin/logs`

**Permisos Requeridos:** Admin, SuperAdmin

**Query Parameters:**
- `user_id`: filtrar por usuario
- `action`: filtrar por tipo de acción
- `date_from`: fecha inicio
- `date_to`: fecha fin
- `page`, `per_page`: paginación

**Response Success (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1523,
      "user": {
        "id": 5,
        "name": "María González"
      },
      "action": "document.created",
      "description": "Creó el documento 'Instalación de Nginx'",
      "ip_address": "192.168.1.100",
      "user_agent": "Mozilla/5.0...",
      "created_at": "2026-02-17T18:30:00.000000Z"
    }
  ]
}
```

---

## Códigos de Estado HTTP

| Código | Significado |
|--------|-------------|
| 200 | OK - Solicitud exitosa |
| 201 | Created - Recurso creado exitosamente |
| 204 | No Content - Solicitud exitosa sin contenido |
| 400 | Bad Request - Solicitud mal formada |
| 401 | Unauthorized - No autenticado |
| 403 | Forbidden - No autorizado (sin permisos) |
| 404 | Not Found - Recurso no encontrado |
| 422 | Unprocessable Entity - Error de validación |
| 429 | Too Many Requests - Rate limit excedido |
| 500 | Internal Server Error - Error del servidor |

---

## Rate Limiting

| Endpoint | Límite |
|----------|--------|
| /auth/login | 5 por minuto |
| /auth/register | 3 por hora |
| /search | 30 por minuto |
| /upload/* | 10 por hora |
| General API | 60 por minuto |

**Response cuando se excede:**
```json
{
  "success": false,
  "message": "Demasiadas solicitudes. Intenta nuevamente en 45 segundos",
  "retry_after": 45
}
```

---

## Versionamiento

La API utiliza versionamiento en la URL:
- **v1** (actual): `/api/v1/*`

Cambios breaking se comunican con 3 meses de anticipación.

---

## Webhooks (Futuro)

Planeado para v2.0:
- `document.created`
- `document.updated`
- `document.deleted`
- `user.registered`

---

## Paginación

Todas las respuestas paginadas incluyen:

```json
{
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 20,
    "to": 20,
    "total": 95
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

---

## Manejo de Errores

### Estructura de Error Estándar

```json
{
  "success": false,
  "message": "Mensaje descriptivo del error",
  "errors": {
    "campo": ["Error específico del campo"]
  },
  "code": "ERROR_CODE"
}
```

### Códigos de Error Comunes

| Código | Descripción |
|--------|-------------|
| VALIDATION_ERROR | Error de validación de datos |
| UNAUTHORIZED | Usuario no autenticado |
| FORBIDDEN | Usuario sin permisos |
| NOT_FOUND | Recurso no encontrado |
| RATE_LIMIT_EXCEEDED | Límite de requests excedido |
| SERVER_ERROR | Error interno del servidor |

---

**Última actualización:** 2026-02-17  
**Versión de API:** v1.0
