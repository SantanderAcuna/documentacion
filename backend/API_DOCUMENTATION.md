# API Documentation - CMS Gubernamental v1

**Base URL:** `http://localhost:8000/api/v1`  
**Authentication:** Bearer Token (Laravel Sanctum)

---

## 🔐 Authentication

### Register
```http
POST /v1/register
Content-Type: application/json

{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@example.com"
  },
  "token": "1|abc123...",
  "roles": ["ciudadano"],
  "permissions": ["ver-contenidos"]
}
```

### Login
```http
POST /v1/login
Content-Type: application/json

{
  "email": "juan@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "user": { ... },
  "token": "2|xyz789...",
  "roles": ["editor"],
  "permissions": ["ver-contenidos", "crear-contenidos", ...]
}
```

### Get Current User
```http
GET /v1/me
Authorization: Bearer {token}
```

### Logout
```http
POST /v1/logout
Authorization: Bearer {token}
```

---

## 📝 Contents

### List Contents (Public)
```http
GET /v1/contents?page=1&per_page=15
GET /v1/contents?category_id=1
GET /v1/contents?featured=1
GET /v1/contents?search=transparencia
```

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Título del contenido",
      "slug": "titulo-del-contenido",
      "excerpt": "Resumen...",
      "content": "Contenido completo...",
      "status": "published",
      "published_at": "2026-02-17T10:00:00Z",
      "author": { "id": 1, "name": "Admin" },
      "category": { "id": 1, "name": "Noticias" },
      "tags": [
        { "id": 1, "name": "Transparencia", "slug": "transparencia" }
      ],
      "views": 42,
      "is_featured": true
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

### Get Content by Slug (Public)
```http
GET /v1/contents/{slug}
```

### Create Content (Auth + Permission)
```http
POST /v1/contents
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Nuevo contenido",
  "content": "Texto completo del contenido...",
  "excerpt": "Resumen breve",
  "category_id": 1,
  "status": "draft",
  "published_at": "2026-02-20T10:00:00Z",
  "meta_title": "SEO Title",
  "meta_description": "SEO Description",
  "meta_keywords": ["palabra1", "palabra2"],
  "is_featured": false,
  "allow_comments": true,
  "tags": [1, 2, 3]
}
```

**Required Permission:** `crear-contenidos`

### Update Content (Auth + Permission)
```http
PUT /v1/contents/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Título actualizado",
  "status": "published"
}
```

**Required Permission:** `editar-contenidos`

### Delete Content (Auth + Permission)
```http
DELETE /v1/contents/{id}
Authorization: Bearer {token}
```

**Required Permission:** `eliminar-contenidos`

---

## 📁 Categories

### List Categories (Public)
```http
GET /v1/categories
GET /v1/categories?root_only=1
```

**Response (200):**
```json
[
  {
    "id": 1,
    "name": "Noticias",
    "slug": "noticias",
    "description": "Sección de noticias",
    "parent_id": null,
    "children": [
      {
        "id": 2,
        "name": "Noticias Locales",
        "slug": "noticias-locales",
        "parent_id": 1
      }
    ],
    "order": 0,
    "is_active": true
  }
]
```

### Get Category by Slug (Public)
```http
GET /v1/categories/{slug}
```

### Create Category (Auth + Permission)
```http
POST /v1/categories
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Nueva Categoría",
  "description": "Descripción...",
  "parent_id": null,
  "order": 0,
  "is_active": true
}
```

**Required Permission:** `crear-categorias`

### Update Category (Auth + Permission)
```http
PUT /v1/categories/{id}
Authorization: Bearer {token}
```

**Required Permission:** `editar-categorias`

### Delete Category (Auth + Permission)
```http
DELETE /v1/categories/{id}
Authorization: Bearer {token}
```

**Required Permission:** `eliminar-categorias`

---

## 🏷️ Tags

### List Tags (Public)
```http
GET /v1/tags
```

### Create Tag (Auth)
```http
POST /v1/tags
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Nueva Etiqueta"
}
```

### Update Tag (Auth)
```http
PUT /v1/tags/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Etiqueta Actualizada"
}
```

### Delete Tag (Auth)
```http
DELETE /v1/tags/{id}
Authorization: Bearer {token}
```

---

## 📎 Media

### Upload File (Auth)
```http
POST /v1/media
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "file": (binary),
  "alt_text": "Descripción de la imagen",
  "caption": "Pie de foto",
  "mediable_type": "App\\Models\\Content",
  "mediable_id": 1
}
```

**Response (201):**
```json
{
  "id": 1,
  "filename": "uuid-filename.jpg",
  "original_filename": "foto.jpg",
  "path": "media/uuid-filename.jpg",
  "mime_type": "image/jpeg",
  "size": 102400,
  "url": "http://localhost:8000/storage/media/uuid-filename.jpg",
  "alt_text": "Descripción...",
  "uploaded_by": 1
}
```

### Delete Media (Auth)
```http
DELETE /v1/media/{id}
Authorization: Bearer {token}
```

---

## 📨 PQRS (Peticiones, Quejas, Reclamos, Sugerencias)

### Create PQRS (Public)
```http
POST /v1/pqrs
Content-Type: application/json

{
  "tipo": "peticion",
  "nombre": "María González",
  "email": "maria@example.com",
  "telefono": "3001234567",
  "documento": "12345678",
  "asunto": "Solicitud de información",
  "mensaje": "Requiero información sobre..."
}
```

**Response (201):**
```json
{
  "message": "PQRS creado exitosamente",
  "pqrs": {
    "id": 1,
    "folio": "PQRS-2026-000001",
    "tipo": "peticion",
    "nombre": "María González",
    "email": "maria@example.com",
    "asunto": "Solicitud de información",
    "estado": "nuevo",
    "created_at": "2026-02-17T22:00:00Z"
  },
  "folio": "PQRS-2026-000001"
}
```

### Track PQRS by Folio (Public)
```http
GET /v1/pqrs/{folio}
```

**Example:** `GET /v1/pqrs/PQRS-2026-000001`

**Response (200):**
```json
{
  "id": 1,
  "folio": "PQRS-2026-000001",
  "tipo": "peticion",
  "nombre": "María González",
  "asunto": "Solicitud de información",
  "estado": "resuelto",
  "respuesta": "Su solicitud ha sido atendida...",
  "respondido_at": "2026-02-18T10:00:00Z",
  "created_at": "2026-02-17T22:00:00Z"
}
```

### List PQRS (Auth + Permission)
```http
GET /v1/pqrs?page=1&per_page=15
GET /v1/pqrs?tipo=queja
GET /v1/pqrs?estado=nuevo
GET /v1/pqrs?search=información
```

**Required Permission:** `ver-pqrs`

### Update PQRS Status (Auth + Permission)
```http
PUT /v1/pqrs/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "estado": "en_proceso"
}
```

**Required Permission:** `responder-pqrs`

### Respond to PQRS (Auth + Permission)
```http
POST /v1/pqrs/{id}/respond
Authorization: Bearer {token}
Content-Type: application/json

{
  "respuesta": "Estimado usuario, en respuesta a su solicitud..."
}
```

**Required Permission:** `responder-pqrs`

---

## 🔑 Roles & Permissions

### Roles
1. **super-admin** - All permissions
2. **editor** - Content management
3. **admin-transparencia** - Transparency section
4. **atencion-pqrs** - PQRS management
5. **ciudadano** - Read-only (default for registered users)
6. **auditor** - Read-only oversight

### Permissions
- **Contenidos:** ver, crear, editar, eliminar, publicar
- **Categorías:** ver, crear, editar, eliminar
- **Usuarios:** ver, crear, editar, eliminar
- **Transparencia:** ver, editar, publicar
- **PQRS:** ver, responder, cerrar
- **Configuración:** ver, editar

---

## 📊 Response Codes

- **200** - OK
- **201** - Created
- **204** - No Content (successful deletion)
- **400** - Bad Request (validation error)
- **401** - Unauthorized (not authenticated)
- **403** - Forbidden (no permission)
- **404** - Not Found
- **422** - Unprocessable Entity (validation failed)
- **500** - Internal Server Error

---

## 🔧 Error Response Format

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "El campo email es obligatorio."
    ],
    "password": [
      "El campo password debe tener al menos 8 caracteres."
    ]
  }
}
```

---

## 🚀 Quick Start

### 1. Run Migrations
```bash
cd backend
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
```

### 2. Create Admin User
```bash
php artisan tinker
```

```php
$user = App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@alcaldia.gov.co',
    'password' => bcrypt('admin123')
]);
$user->assignRole('super-admin');
```

### 3. Test with cURL

**Register:**
```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Login:**
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@alcaldia.gov.co",
    "password": "admin123"
  }'
```

**Get Contents:**
```bash
curl -X GET http://localhost:8000/api/v1/contents
```

**Create Content (with token):**
```bash
curl -X POST http://localhost:8000/api/v1/contents \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "title": "Mi Primer Contenido",
    "content": "Este es el contenido...",
    "status": "published"
  }'
```

---

**Note:** Remember to set up CORS properly for frontend integration.
