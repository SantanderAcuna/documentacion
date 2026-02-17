# Esquema de Base de Datos - Portal de Configuración VPS

## Información General

### Motor de Base de Datos
- **Sistema:** MySQL 8.0+
- **Motor de Almacenamiento:** InnoDB
- **Charset:** utf8mb4
- **Collation:** utf8mb4_unicode_ci
- **Zona Horaria:** UTC

### Convenciones de Nomenclatura

- **Tablas:** snake_case, plural (ej: `users`, `documents`)
- **Columnas:** snake_case
- **Índices:** `idx_{tabla}_{columnas}`
- **Foreign Keys:** `fk_{tabla}_{columna}`
- **Primary Keys:** `id` (BIGINT UNSIGNED AUTO_INCREMENT)
- **Timestamps:** `created_at`, `updated_at`, `deleted_at` (soft deletes)

---

## Diagrama Entidad-Relación (ERD)

```
┌─────────────────┐         ┌──────────────────┐
│     users       │         │      roles       │
├─────────────────┤         ├──────────────────┤
│ id PK           │◄───────┤│ id PK            │
│ name            │         ││ name             │
│ email (unique)  │         ││ guard_name       │
│ password        │         ││ created_at       │
│ avatar          │         ││ updated_at       │
│ status          │         │└──────────────────┘
│ last_login_at   │                 │
│ created_at      │                 │
│ updated_at      │         ┌───────▼──────────┐
│ deleted_at      │         │ model_has_roles  │
└─────────────────┘         ├──────────────────┤
        │                   │ role_id FK       │
        │                   │ model_id FK      │
        │                   │ model_type       │
        │                   └──────────────────┘
        │                           │
        │                   ┌───────▼──────────┐
        │                   │   permissions    │
        │                   ├──────────────────┤
        │                   │ id PK            │
        │                   │ name             │
        │                   │ guard_name       │
        │                   └──────────────────┘
        │
        ├───────────────────────────────────────┐
        │                                       │
        ▼                                       ▼
┌─────────────────┐                  ┌──────────────────┐
│   documents     │                  │    favorites     │
├─────────────────┤                  ├──────────────────┤
│ id PK           │                  │ id PK            │
│ user_id FK      │──────────────────┤│ user_id FK       │
│ category_id FK  │                  ││ document_id FK   │
│ title (unique)  │                  ││ created_at       │
│ slug (unique)   │                  │└──────────────────┘
│ summary         │
│ content         │                  ┌──────────────────┐
│ status          │                  │  document_views  │
│ views_count     │                  ├──────────────────┤
│ favorites_count │◄─────────────────┤│ id PK            │
│ version         │                  ││ document_id FK   │
│ published_at    │                  ││ user_id FK (null)│
│ created_at      │                  ││ ip_address       │
│ updated_at      │                  ││ user_agent       │
│ deleted_at      │                  ││ created_at       │
└─────────────────┘                  │└──────────────────┘
        │
        ├──────────────────┐
        │                  │
        ▼                  ▼
┌─────────────────┐  ┌──────────────────┐
│   categories    │  │ document_tag     │
├─────────────────┤  ├──────────────────┤
│ id PK           │  │ document_id FK   │
│ name (unique)   │  │ tag_id FK        │
│ slug (unique)   │  └──────────────────┘
│ description     │           │
│ icon            │           ▼
│ color           │  ┌──────────────────┐
│ order           │  │      tags        │
│ created_at      │  ├──────────────────┤
│ updated_at      │  │ id PK            │
└─────────────────┘  │ name (unique)    │
                     │ slug (unique)    │
                     │ created_at       │
                     │ updated_at       │
                     └──────────────────┘

┌──────────────────────┐
│  document_versions   │
├──────────────────────┤
│ id PK                │
│ document_id FK       │
│ user_id FK           │
│ title                │
│ content              │
│ version              │
│ created_at           │
└──────────────────────┘

┌──────────────────────┐
│    activity_logs     │
├──────────────────────┤
│ id PK                │
│ user_id FK           │
│ action               │
│ description          │
│ subject_type         │
│ subject_id           │
│ ip_address           │
│ user_agent           │
│ created_at           │
└──────────────────────┘

┌──────────────────────┐
│        files         │
├──────────────────────┤
│ id PK                │
│ user_id FK           │
│ uuid (unique)        │
│ filename             │
│ path                 │
│ url                  │
│ mime_type            │
│ size                 │
│ disk                 │
│ created_at           │
│ updated_at           │
│ deleted_at           │
└──────────────────────┘
```

---

## Tablas Detalladas

### 1. users

Almacena información de usuarios del sistema.

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(500) NULL,
    bio TEXT NULL,
    status ENUM('active', 'suspended', 'inactive') DEFAULT 'active',
    last_login_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_users_email (email),
    INDEX idx_users_status (status),
    INDEX idx_users_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columnas:**
- `id`: Identificador único
- `name`: Nombre completo del usuario
- `email`: Email único (usado para login)
- `email_verified_at`: Fecha de verificación del email
- `password`: Hash de contraseña (bcrypt)
- `avatar`: URL del avatar del usuario
- `bio`: Biografía corta del usuario
- `status`: Estado del usuario (active, suspended, inactive)
- `last_login_at`: Fecha del último login
- `remember_token`: Token para "recordarme"
- `created_at`, `updated_at`: Timestamps automáticos
- `deleted_at`: Soft delete timestamp

**Índices:**
- Primary key en `id`
- Unique index en `email`
- Index en `status` para filtros
- Index en `created_at` para ordenamiento

**Restricciones:**
- Email debe ser único
- Password debe estar hasheado (nunca en texto plano)

---

### 2. roles

Roles del sistema RBAC (Spatie Permission).

```sql
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY unique_roles (name, guard_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Roles Predefinidos:**
- `superadmin`: Acceso total al sistema
- `admin`: Gestión de usuarios y contenido
- `editor`: Creación y edición de contenido
- `viewer`: Solo lectura

---

### 3. permissions

Permisos granulares (Spatie Permission).

```sql
CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY unique_permissions (name, guard_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Permisos Principales:**
- `view_documents`: Ver documentos
- `create_documents`: Crear documentos
- `edit_own_documents`: Editar documentos propios
- `edit_all_documents`: Editar todos los documentos
- `delete_documents`: Eliminar documentos
- `manage_users`: Gestionar usuarios
- `manage_roles`: Gestionar roles y permisos
- `view_analytics`: Ver estadísticas
- `view_logs`: Ver logs de actividad

---

### 4. model_has_roles

Tabla pivot para relación usuarios-roles.

```sql
CREATE TABLE model_has_roles (
    role_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (role_id, model_id, model_type),
    INDEX idx_model_has_roles_model_id_type (model_id, model_type),
    
    CONSTRAINT fk_model_has_roles_role_id 
        FOREIGN KEY (role_id) 
        REFERENCES roles(id) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 5. model_has_permissions

Tabla pivot para permisos directos a modelos.

```sql
CREATE TABLE model_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (permission_id, model_id, model_type),
    INDEX idx_model_has_permissions_model_id_type (model_id, model_type),
    
    CONSTRAINT fk_model_has_permissions_permission_id 
        FOREIGN KEY (permission_id) 
        REFERENCES permissions(id) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 6. role_has_permissions

Tabla pivot para permisos asignados a roles.

```sql
CREATE TABLE role_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (permission_id, role_id),
    
    CONSTRAINT fk_role_has_permissions_permission_id 
        FOREIGN KEY (permission_id) 
        REFERENCES permissions(id) 
        ON DELETE CASCADE,
    CONSTRAINT fk_role_has_permissions_role_id 
        FOREIGN KEY (role_id) 
        REFERENCES roles(id) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 7. documents

Tabla principal de documentación.

```sql
CREATE TABLE documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL UNIQUE,
    slug VARCHAR(255) NOT NULL UNIQUE,
    summary VARCHAR(500) NOT NULL,
    content LONGTEXT NOT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    views_count INT UNSIGNED DEFAULT 0,
    favorites_count INT UNSIGNED DEFAULT 0,
    version INT UNSIGNED DEFAULT 1,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_documents_user_id (user_id),
    INDEX idx_documents_category_id (category_id),
    INDEX idx_documents_slug (slug),
    INDEX idx_documents_status (status),
    INDEX idx_documents_published_at (published_at),
    FULLTEXT INDEX idx_documents_search (title, content),
    
    CONSTRAINT fk_documents_user_id 
        FOREIGN KEY (user_id) 
        REFERENCES users(id) 
        ON DELETE CASCADE,
    CONSTRAINT fk_documents_category_id 
        FOREIGN KEY (category_id) 
        REFERENCES categories(id) 
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columnas:**
- `id`: Identificador único
- `user_id`: ID del autor (FK a users)
- `category_id`: ID de categoría (FK a categories)
- `title`: Título único del documento
- `slug`: URL-friendly slug único
- `summary`: Resumen corto (max 500 chars)
- `content`: Contenido completo en Markdown
- `status`: Estado (draft, published)
- `views_count`: Contador de visualizaciones
- `favorites_count`: Contador de favoritos
- `version`: Número de versión actual
- `published_at`: Fecha de publicación
- `created_at`, `updated_at`, `deleted_at`: Timestamps

**Índices:**
- FULLTEXT en `title, content` para búsqueda
- Index en columnas de filtro frecuente

**Restricciones:**
- No se puede eliminar categoría con documentos (ON DELETE RESTRICT)
- Al eliminar usuario, sus documentos se eliminan (ON DELETE CASCADE)

---

### 8. categories

Categorías de documentación.

```sql
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    icon VARCHAR(50) NULL,
    color VARCHAR(7) NULL,
    order INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_categories_slug (slug),
    INDEX idx_categories_order (order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columnas:**
- `id`: Identificador único
- `name`: Nombre de la categoría (único)
- `slug`: URL-friendly slug (único)
- `description`: Descripción de la categoría
- `icon`: Clase de ícono FontAwesome (ej: fa-terminal)
- `color`: Color en hexadecimal (ej: #2b6cb0)
- `order`: Orden de visualización

**Categorías Iniciales:**
```sql
INSERT INTO categories (name, slug, description, icon, color, order) VALUES
('SSH', 'ssh', 'Configuración y seguridad SSH', 'fa-terminal', '#2b6cb0', 1),
('Seguridad', 'seguridad', 'Firewall, Fail2Ban, hardening', 'fa-shield', '#ed8936', 2),
('Web Server', 'web-server', 'Nginx, Apache, configuración', 'fa-server', '#48bb78', 3),
('Base de Datos', 'base-de-datos', 'MySQL, PostgreSQL, MongoDB', 'fa-database', '#9f7aea', 4);
```

---

### 9. tags

Etiquetas para documentos.

```sql
CREATE TABLE tags (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_tags_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 10. document_tag

Tabla pivot para relación documentos-tags (many-to-many).

```sql
CREATE TABLE document_tag (
    document_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (document_id, tag_id),
    INDEX idx_document_tag_tag_id (tag_id),
    
    CONSTRAINT fk_document_tag_document_id 
        FOREIGN KEY (document_id) 
        REFERENCES documents(id) 
        ON DELETE CASCADE,
    CONSTRAINT fk_document_tag_tag_id 
        FOREIGN KEY (tag_id) 
        REFERENCES tags(id) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 11. favorites

Favoritos de usuarios.

```sql
CREATE TABLE favorites (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    document_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    
    UNIQUE KEY unique_favorites (user_id, document_id),
    INDEX idx_favorites_document_id (document_id),
    
    CONSTRAINT fk_favorites_user_id 
        FOREIGN KEY (user_id) 
        REFERENCES users(id) 
        ON DELETE CASCADE,
    CONSTRAINT fk_favorites_document_id 
        FOREIGN KEY (document_id) 
        REFERENCES documents(id) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Restricciones:**
- Un usuario no puede marcar el mismo documento dos veces (UNIQUE KEY)

---

### 12. document_views

Registro de visualizaciones de documentos.

```sql
CREATE TABLE document_views (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(500) NULL,
    created_at TIMESTAMP NULL,
    
    INDEX idx_document_views_document_id (document_id),
    INDEX idx_document_views_user_id (user_id),
    INDEX idx_document_views_created_at (created_at),
    
    CONSTRAINT fk_document_views_document_id 
        FOREIGN KEY (document_id) 
        REFERENCES documents(id) 
        ON DELETE CASCADE,
    CONSTRAINT fk_document_views_user_id 
        FOREIGN KEY (user_id) 
        REFERENCES users(id) 
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Notas:**
- `user_id` es NULL para usuarios anónimos
- Se registra IP y user agent para analytics

---

### 13. document_versions

Historial de versiones de documentos.

```sql
CREATE TABLE document_versions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    version INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    
    INDEX idx_document_versions_document_id (document_id),
    INDEX idx_document_versions_version (document_id, version),
    
    CONSTRAINT fk_document_versions_document_id 
        FOREIGN KEY (document_id) 
        REFERENCES documents(id) 
        ON DELETE CASCADE,
    CONSTRAINT fk_document_versions_user_id 
        FOREIGN KEY (user_id) 
        REFERENCES users(id) 
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Política de Retención:**
- Se mantienen las últimas 50 versiones por documento
- Versiones más antiguas se archivan o eliminan

---

### 14. files

Archivos subidos al sistema.

```sql
CREATE TABLE files (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    uuid CHAR(36) NOT NULL UNIQUE,
    filename VARCHAR(255) NOT NULL,
    path VARCHAR(500) NOT NULL,
    url VARCHAR(1000) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    size BIGINT UNSIGNED NOT NULL,
    disk VARCHAR(50) DEFAULT 'local',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_files_user_id (user_id),
    INDEX idx_files_uuid (uuid),
    INDEX idx_files_created_at (created_at),
    
    CONSTRAINT fk_files_user_id 
        FOREIGN KEY (user_id) 
        REFERENCES users(id) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columnas:**
- `uuid`: Identificador único público
- `filename`: Nombre original del archivo
- `path`: Ruta en el disco
- `url`: URL pública del archivo
- `mime_type`: Tipo MIME (image/png, etc.)
- `size`: Tamaño en bytes
- `disk`: Disco de almacenamiento (local, s3, spaces)

---

### 15. activity_logs

Logs de actividad del sistema.

```sql
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT NULL,
    subject_type VARCHAR(255) NULL,
    subject_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(500) NULL,
    created_at TIMESTAMP NULL,
    
    INDEX idx_activity_logs_user_id (user_id),
    INDEX idx_activity_logs_action (action),
    INDEX idx_activity_logs_subject (subject_type, subject_id),
    INDEX idx_activity_logs_created_at (created_at),
    
    CONSTRAINT fk_activity_logs_user_id 
        FOREIGN KEY (user_id) 
        REFERENCES users(id) 
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Acciones Registradas:**
- `auth.login`: Usuario inició sesión
- `auth.logout`: Usuario cerró sesión
- `auth.failed`: Intento fallido de login
- `document.created`: Documento creado
- `document.updated`: Documento actualizado
- `document.deleted`: Documento eliminado
- `user.created`: Usuario creado
- `user.updated`: Usuario actualizado
- `user.suspended`: Usuario suspendido
- `role.changed`: Rol de usuario cambiado

**Política de Retención:**
- Se mantienen los logs durante 90 días
- Logs más antiguos se archivan o eliminan

---

## Seeders

### UserSeeder

Crea usuarios iniciales para testing:

```php
User::create([
    'name' => 'Super Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'status' => 'active',
])->assignRole('superadmin');

User::create([
    'name' => 'Editor',
    'email' => 'editor@example.com',
    'password' => Hash::make('password'),
    'status' => 'active',
])->assignRole('editor');

User::create([
    'name' => 'Viewer',
    'email' => 'viewer@example.com',
    'password' => Hash::make('password'),
    'status' => 'active',
])->assignRole('viewer');
```

---

### RolePermissionSeeder

Crea roles y permisos:

```php
// Permisos
$permissions = [
    'view_documents',
    'create_documents',
    'edit_own_documents',
    'edit_all_documents',
    'delete_documents',
    'manage_users',
    'manage_roles',
    'view_analytics',
    'view_logs',
];

foreach ($permissions as $permission) {
    Permission::create(['name' => $permission]);
}

// Roles
$superadmin = Role::create(['name' => 'superadmin']);
$superadmin->givePermissionTo(Permission::all());

$admin = Role::create(['name' => 'admin']);
$admin->givePermissionTo([
    'view_documents', 'create_documents', 
    'edit_all_documents', 'delete_documents',
    'manage_users', 'view_analytics', 'view_logs'
]);

$editor = Role::create(['name' => 'editor']);
$editor->givePermissionTo([
    'view_documents', 'create_documents', 'edit_own_documents'
]);

$viewer = Role::create(['name' => 'viewer']);
$viewer->givePermissionTo(['view_documents']);
```

---

## Optimizaciones de Performance

### 1. Índices

Todos los índices críticos están definidos arriba. Adicionales:

```sql
-- Búsqueda rápida de documentos publicados recientes
CREATE INDEX idx_documents_published_recent 
ON documents(status, published_at DESC);

-- Búsqueda de documentos por autor y estado
CREATE INDEX idx_documents_user_status 
ON documents(user_id, status);
```

### 2. Particionamiento (Opcional para tablas grandes)

```sql
-- Particionar activity_logs por mes
ALTER TABLE activity_logs PARTITION BY RANGE (TO_DAYS(created_at)) (
    PARTITION p202601 VALUES LESS THAN (TO_DAYS('2026-02-01')),
    PARTITION p202602 VALUES LESS THAN (TO_DAYS('2026-03-01')),
    -- ...
);
```

### 3. Caché de Queries

Queries que deben cachearse en Redis:
- Listado de categorías (1 hora)
- Documento individual (15 minutos)
- Listado de documentos (5 minutos)
- Tags populares (30 minutos)

---

## Backups

### Estrategia de Backup

1. **Backup Completo Diario:**
   ```bash
   mysqldump -u root -p vps_portal > backup_$(date +%Y%m%d).sql
   ```

2. **Backup Incremental cada 6 horas** (usando binlogs)

3. **Retención:**
   - Diarios: 7 días
   - Semanales: 4 semanas
   - Mensuales: 12 meses

4. **Almacenamiento:**
   - Local: últimos 3 días
   - DigitalOcean Spaces: todos los backups

---

## Migraciones Ejemplo

### CreateDocumentsTable

```php
Schema::create('documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('category_id')->constrained()->onDelete('restrict');
    $table->string('title')->unique();
    $table->string('slug')->unique();
    $table->string('summary', 500);
    $table->longText('content');
    $table->enum('status', ['draft', 'published'])->default('draft');
    $table->unsignedInteger('views_count')->default(0);
    $table->unsignedInteger('favorites_count')->default(0);
    $table->unsignedInteger('version')->default(1);
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->index(['status', 'published_at']);
    $table->fullText(['title', 'content']);
});
```

---

## Queries Comunes Optimizadas

### 1. Listar documentos con autor y categoría

```sql
SELECT 
    d.id, d.title, d.slug, d.summary, d.status,
    d.views_count, d.created_at,
    u.id AS author_id, u.name AS author_name,
    c.id AS category_id, c.name AS category_name
FROM documents d
INNER JOIN users u ON d.user_id = u.id
INNER JOIN categories c ON d.category_id = c.id
WHERE d.status = 'published'
AND d.deleted_at IS NULL
ORDER BY d.published_at DESC
LIMIT 20 OFFSET 0;
```

### 2. Búsqueda full-text

```sql
SELECT 
    id, title, slug, summary,
    MATCH(title, content) AGAINST('configurar ssh' IN NATURAL LANGUAGE MODE) AS relevance
FROM documents
WHERE MATCH(title, content) AGAINST('configurar ssh' IN NATURAL LANGUAGE MODE)
AND status = 'published'
AND deleted_at IS NULL
ORDER BY relevance DESC
LIMIT 20;
```

### 3. Documentos más vistos

```sql
SELECT d.*, COUNT(dv.id) as total_views
FROM documents d
LEFT JOIN document_views dv ON d.id = dv.document_id
WHERE d.status = 'published'
AND d.deleted_at IS NULL
AND dv.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY d.id
ORDER BY total_views DESC
LIMIT 10;
```

---

**Última actualización:** 2026-02-17  
**Versión:** 1.0
