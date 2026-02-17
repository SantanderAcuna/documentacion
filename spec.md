# Especificación Detallada - CMS Gubernamental

## Documento de Requisitos Funcionales
**Proyecto:** CMS Alcaldía  
**Versión:** 1.0.0  
**Fecha:** Febrero 2026  
**Basado en:** constitution.md v1.0.0

---

## 1. Visión General

Este documento especifica los requisitos funcionales del CMS Gubernamental, organizados en módulos temáticos. Cada requisito incluye historias de usuario, criterios de aceptación y restricciones legales asociadas.

---

## 2. Módulo de Autenticación y Usuarios (AU)

### AU-01: Registro de Usuarios

**Historia de Usuario:**  
Como nuevo funcionario de la Alcaldía, quiero registrarme en el CMS para comenzar a gestionar contenidos.

**Criterios de Aceptación:**
- [ ] Formulario de registro solicita: nombre completo, email institucional, contraseña
- [ ] Email debe ser del dominio `@alcaldia.gov.co` (validación server-side)
- [ ] Contraseña cumple requisitos: mínimo 8 caracteres, 1 mayúscula, 1 minúscula, 1 número, 1 símbolo
- [ ] Se envía email de confirmación con link válido por 24 horas
- [ ] Usuario queda en estado `pendiente` hasta confirmar email
- [ ] Usuario confirmado queda en estado `inactivo` hasta que admin lo active
- [ ] Rate limiting: máximo 3 registros por hora por IP

**Validaciones:**
```php
// app/Http/Requests/RegisterRequest.php
public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users|ends_with:@alcaldia.gov.co',
        'password' => [
            'required',
            'confirmed',
            'min:8',
            Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
        ]
    ];
}
```

**Restricciones Legales:**
- Ley 1581/2012: Consentimiento explícito para tratamiento de datos
- Debe mostrar política de privacidad antes de registro
- Usuario debe aceptar términos y condiciones

**Archivos a Crear:**
- `app/Http/Controllers/Auth/RegisterController.php`
- `app/Http/Requests/RegisterRequest.php`
- `resources/js/pages/Auth/Register.vue`
- `database/migrations/xxxx_create_users_table.php`

---

### AU-02: Inicio de Sesión

**Historia de Usuario:**  
Como usuario registrado, quiero iniciar sesión con mi email y contraseña para acceder al sistema.

**Criterios de Aceptación:**
- [ ] Formulario solicita email y contraseña
- [ ] Protección contra fuerza bruta: máximo 5 intentos en 15 minutos
- [ ] Después de 5 intentos fallidos, cuenta se bloquea por 15 minutos
- [ ] Login exitoso genera cookie HTTP-Only con token Sanctum
- [ ] Cookie tiene SameSite=Strict, Secure=true
- [ ] Se registra el login en tabla `activity_log`
- [ ] Se actualiza campo `last_login_at` del usuario
- [ ] Redirección a dashboard después de login exitoso

**Validaciones:**
```php
// app/Http/Requests/LoginRequest.php
public function rules(): array
{
    return [
        'email' => 'required|email',
        'password' => 'required|string'
    ];
}

// Rate limiting en AuthController
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->email);
});
```

**Flujo de Autenticación:**
```
1. Usuario envía POST /api/auth/login
2. Backend valida credenciales
3. Si es válido:
   - Genera token Sanctum
   - Guarda en cookie HTTP-Only
   - Registra en auditoría
   - Retorna 200 con datos del usuario
4. Si es inválido:
   - Incrementa contador de intentos fallidos
   - Si ≥ 5 intentos: bloquea por 15 min
   - Retorna 401 con mensaje genérico
```

**Restricciones de Seguridad:**
- Mensaje de error genérico: "Credenciales incorrectas" (no especificar si email existe)
- No mostrar contraseña en ningún momento
- Logging de intentos fallidos (IP, user-agent, timestamp)

---

### AU-03: Gestión de Roles y Permisos (RBAC)

**Historia de Usuario:**  
Como administrador del sistema, quiero asignar roles y permisos específicos a los usuarios para controlar su acceso.

**Roles Predefinidos:**

| Rol | Permisos | Descripción |
|-----|----------|-------------|
| **super_admin** | `*` (todos) | Control total del sistema |
| **admin_transparencia** | `manage_transparency`, `publish_data` | Gestiona información de transparencia |
| **editor** | `create_content`, `edit_content`, `publish_content` | Crea y publica contenidos |
| **revisor** | `review_content`, `approve_content` | Revisa y aprueba contenidos |
| **ciudadano** | `read_public`, `create_pqrs` | Acceso público |

**Permisos Específicos:**
```php
// Contenidos
'create_content'
'edit_content'
'delete_content'
'publish_content'
'unpublish_content'

// Transparencia
'manage_transparency'
'publish_budget'
'publish_contracts'
'manage_org_structure'

// Usuarios
'manage_users'
'assign_roles'
'view_audit_logs'

// PQRS
'create_pqrs'
'view_pqrs'
'respond_pqrs'
'close_pqrs'

// Configuración
'manage_settings'
'manage_categories'
'manage_tags'
```

**Criterios de Aceptación:**
- [ ] Se usa paquete `spatie/laravel-permission`
- [ ] Roles y permisos se gestionan desde panel administrativo
- [ ] Se puede asignar múltiples roles a un usuario
- [ ] Se puede asignar permisos directos (fuera de roles)
- [ ] Middleware `role:admin` y `permission:edit_content` funcionales
- [ ] Interfaz muestra solo acciones permitidas para el rol

**Implementación:**
```php
// Middleware
Route::middleware(['auth:sanctum', 'role:editor'])
    ->group(function () {
        Route::post('/contents', [ContentController::class, 'store']);
    });

// En Controlador
public function store(Request $request)
{
    if (!$request->user()->can('create_content')) {
        abort(403, 'No tiene permiso para crear contenidos');
    }
    
    // Lógica de creación
}

// En Vue
<template>
  <button v-if="can('publish_content')" @click="publish">
    Publicar
  </button>
</template>

<script setup>
import { useCan } from '@/composables/usePermissions';
const can = useCan();
</script>
```

---

### AU-04: Auditoría de Acciones

**Historia de Usuario:**  
Como auditor, quiero ver un registro completo de todas las acciones realizadas en el sistema para supervisión y cumplimiento.

**Criterios de Aceptación:**
- [ ] Se registra automáticamente: creación, actualización, eliminación de recursos
- [ ] Cada entrada incluye: usuario, acción, recurso, timestamp, IP, user-agent
- [ ] Se usa paquete `spatie/laravel-activitylog`
- [ ] Logs son inmutables (no se pueden editar ni eliminar)
- [ ] Panel de auditoría con filtros por: usuario, fecha, acción, recurso
- [ ] Exportación de logs en formato CSV
- [ ] Retención mínima: 1 año

**Eventos Auditados:**
- Login/Logout de usuarios
- Creación/edición/eliminación de contenidos
- Publicación/despublicación de contenidos
- Cambios en transparencia
- Respuestas a PQRS
- Cambios de roles/permisos
- Modificaciones de configuración

**Implementación:**
```php
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Content extends Model
{
    use LogsActivity;
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'body', 'status', 'published_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
    protected static $recordEvents = ['created', 'updated', 'deleted'];
}
```

**Restricciones Legales:**
- Cumple con ITA (componente de control)
- Cumple con FURAG (trazabilidad de gestión)

---

### AU-05: Restablecimiento de Contraseña

**Historia de Usuario:**  
Como usuario que olvidó su contraseña, quiero recibir un link para restablecerla.

**Criterios de Aceptación:**
- [ ] Link "¿Olvidó su contraseña?" en página de login
- [ ] Formulario solicita email
- [ ] Si email existe, envía correo con token
- [ ] Token válido por 60 minutos
- [ ] Link de reseteo lleva a formulario de nueva contraseña
- [ ] Nueva contraseña no puede ser igual a las últimas 3 usadas
- [ ] Después de cambio exitoso, invalida tokens anteriores
- [ ] Envía email de confirmación de cambio

**Flujo:**
```
1. Usuario ingresa email en formulario
2. Sistema valida que email existe
3. Genera token seguro (hash SHA-256)
4. Guarda en tabla password_resets con expiración
5. Envía email con link: /reset-password?token=xxx&email=xxx
6. Usuario accede al link
7. Muestra formulario de nueva contraseña
8. Usuario envía nueva contraseña
9. Sistema valida token, email, expiración
10. Actualiza contraseña
11. Invalida token
12. Envía email de confirmación
13. Redirige a login
```

**Validaciones:**
```php
public function rules(): array
{
    return [
        'token' => 'required',
        'email' => 'required|email',
        'password' => [
            'required',
            'confirmed',
            Password::min(8)->mixedCase()->numbers()->symbols(),
            new NotInLastThree($this->email)
        ]
    ];
}
```

---

## 3. Módulo de Gestión de Contenidos (GC)

### GC-01: Crear Contenido

**Historia de Usuario:**  
Como editor, quiero crear diferentes tipos de contenidos (noticias, páginas, documentos) para publicar en el sitio.

**Tipos de Contenido:**

| Tipo | Campos Específicos | Uso |
|------|-------------------|-----|
| **noticia** | fecha_evento, autor, fuente | Noticias institucionales |
| **pagina** | plantilla, orden_menu | Páginas institucionales estáticas |
| **documento** | numero_acto, fecha_expedicion, vigencia, tipo_documento | Decretos, resoluciones, circulares |
| **evento** | fecha_inicio, fecha_fin, ubicacion, inscripcion_url | Eventos y convocatorias |
| **dato_abierto** | dataset_url, licencia, formato | Publicaciones de datos abiertos |

**Campos Comunes:**
```php
Schema::create('contents', function (Blueprint $table) {
    $table->id();
    $table->string('type'); // noticia, pagina, documento, evento, dato_abierto
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('summary')->nullable();
    $table->longText('body');
    $table->string('status')->default('draft'); // draft, review, scheduled, published
    $table->foreignId('featured_image_id')->nullable();
    $table->timestamp('published_at')->nullable();
    $table->timestamp('scheduled_at')->nullable();
    
    // SEO
    $table->string('meta_title')->nullable();
    $table->text('meta_description')->nullable();
    $table->string('canonical_url')->nullable();
    $table->json('schema_data')->nullable(); // JSON-LD
    
    // Auditoría
    $table->foreignId('created_by');
    $table->foreignId('updated_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

**Criterios de Aceptación:**
- [ ] Formulario dinámico según tipo de contenido
- [ ] Slug se genera automáticamente desde el título (personalizable)
- [ ] Editor WYSIWYG para campo `body` (Tiptap o similar)
- [ ] Carga de imagen destacada con texto alternativo obligatorio
- [ ] Metadatos SEO generados automáticamente (editables)
- [ ] Validación de campos según tipo
- [ ] Guardar como borrador sin publicar
- [ ] Asignar categorías y etiquetas
- [ ] Asociar a dependencias

**Editor WYSIWYG - Requisitos:**
```typescript
// Funcionalidades del editor
- Formateo de texto (negrita, cursiva, subrayado)
- Encabezados (H2, H3, H4) - H1 reservado para título
- Listas (ordenadas, desordenadas)
- Enlaces (con opción de abrir en nueva pestaña)
- Imágenes (con alt text obligatorio)
- Tablas accesibles (con headers)
- Citas (blockquote)
- Código (inline y bloques)
- Alineación de texto
- Vista previa en tiempo real
- Accesibilidad: navegación por teclado
```

**Validación de Accesibilidad:**
```php
// Validar que todas las imágenes tengan alt text
public function validateAccessibility(string $html): array
{
    $errors = [];
    
    // Buscar imágenes sin alt
    preg_match_all('/<img[^>]*>/i', $html, $images);
    foreach ($images[0] as $img) {
        if (!preg_match('/alt=["\']([^"\']*)["\']/', $img, $matches) || empty($matches[1])) {
            $errors[] = 'Imagen sin texto alternativo';
        }
    }
    
    // Validar encabezados jerárquicos
    // Validar contraste de colores inline
    
    return $errors;
}
```

---

### GC-02: Editar Contenido

**Historia de Usuario:**  
Como editor, quiero modificar contenidos existentes sin perder el historial de versiones.

**Criterios de Aceptación:**
- [ ] Formulario pre-cargado con datos actuales
- [ ] Bloqueo optimista para evitar ediciones concurrentes
- [ ] Sistema de versiones (guardado automático cada 5 minutos)
- [ ] Posibilidad de comparar versiones
- [ ] Restaurar versión anterior
- [ ] Indicador visual si otra persona está editando
- [ ] Actualiza campo `updated_at` y `updated_by`

**Bloqueo Optimista:**
```php
// Agregar columna version a tabla contents
$table->integer('version')->default(1);

// En el controlador
public function update(Request $request, Content $content)
{
    if ($content->version != $request->input('version')) {
        return response()->json([
            'message' => 'El contenido ha sido modificado por otro usuario. Por favor, recargue la página.',
            'latest_version' => $content->fresh()
        ], 409); // Conflict
    }
    
    $content->update($request->validated());
    $content->increment('version');
    
    return response()->json($content);
}
```

**Versionado:**
```php
// Tabla de revisiones
Schema::create('content_revisions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('content_id');
    $table->integer('version');
    $table->text('title');
    $table->longText('body');
    $table->json('metadata');
    $table->foreignId('created_by');
    $table->timestamp('created_at');
});
```

---

### GC-03: Programar Publicación

**Historia de Usuario:**  
Como editor, quiero programar la publicación de un contenido para una fecha futura.

**Criterios de Aceptación:**
- [ ] Campo `scheduled_at` en formulario (date + time picker)
- [ ] Contenido queda en estado `scheduled`
- [ ] Laravel Scheduler ejecuta comando cada 5 minutos
- [ ] Comando busca contenidos con `scheduled_at <= now()`
- [ ] Cambia estado a `published` y establece `published_at`
- [ ] Envía notificación al editor que lo creó
- [ ] Se puede desprogramar antes de la fecha

**Implementación:**
```php
// app/Console/Commands/PublishScheduledContent.php
class PublishScheduledContent extends Command
{
    protected $signature = 'content:publish-scheduled';
    
    public function handle()
    {
        Content::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->each(function ($content) {
                $content->update([
                    'status' => 'published',
                    'published_at' => $content->scheduled_at
                ]);
                
                // Notificar
                $content->creator->notify(new ContentPublishedNotification($content));
                
                // Auditar
                activity()
                    ->performedOn($content)
                    ->log('Contenido publicado automáticamente');
            });
    }
}

// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('content:publish-scheduled')
        ->everyFiveMinutes()
        ->withoutOverlapping();
}
```

---

### GC-04: Buscar Contenidos

**Historia de Usuario:**  
Como ciudadano, quiero buscar información en el sitio usando palabras clave.

**Criterios de Aceptación:**
- [ ] Búsqueda full-text en título, resumen y cuerpo
- [ ] Filtros: tipo de contenido, categoría, fecha, dependencia
- [ ] Orden por relevancia (default) o fecha
- [ ] Paginación de 20 resultados por página
- [ ] Solo muestra contenidos con `status=published` y `published_at <= now()`
- [ ] Resaltado de términos de búsqueda en resultados
- [ ] URLs amigables: `/buscar?q=presupuesto&tipo=documento`

**Implementación con MySQL FULLTEXT:**
```php
// Migración: agregar índice FULLTEXT
Schema::table('contents', function (Blueprint $table) {
    $table->fullText(['title', 'summary', 'body']);
});

// Búsqueda
public function search(Request $request)
{
    $query = Content::query()
        ->where('status', 'published')
        ->where('published_at', '<=', now());
    
    // Full-text search
    if ($search = $request->input('q')) {
        $query->whereRaw(
            'MATCH(title, summary, body) AGAINST(? IN NATURAL LANGUAGE MODE)',
            [$search]
        );
    }
    
    // Filtros
    if ($type = $request->input('tipo')) {
        $query->where('type', $type);
    }
    
    if ($category = $request->input('categoria')) {
        $query->whereHas('categories', fn($q) => $q->where('slug', $category));
    }
    
    // Ordenamiento
    $query->orderByDesc('published_at');
    
    return $query->paginate(20);
}
```

**Accesibilidad:**
- Formulario de búsqueda con label accesible
- Anuncio de resultados para lectores de pantalla: "Se encontraron 15 resultados para presupuesto"
- Navegación por teclado en resultados

---

### GC-05: Gestionar Documentos Oficiales

**Historia de Usuario:**  
Como administrador de transparencia, quiero gestionar documentos oficiales (decretos, resoluciones, actas) con sus metadatos específicos.

**Tipos de Documentos:**
- Decreto
- Resolución
- Acuerdo
- Circular
- Acta
- Concepto
- Directiva
- Otro

**Campos Específicos:**
```php
Schema::create('documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('content_id'); // Relación con contents
    $table->string('document_type'); // decreto, resolucion, etc.
    $table->string('document_number');
    $table->date('expedition_date');
    $table->date('validity_from')->nullable();
    $table->date('validity_until')->nullable();
    $table->string('issuing_entity');
    $table->string('file_path'); // PDF del documento
    $table->string('file_hash'); // Integridad
    $table->timestamps();
});
```

**Criterios de Aceptación:**
- [ ] Subida obligatoria de PDF del documento oficial
- [ ] Generación de hash SHA-256 para verificar integridad
- [ ] Búsqueda por número de documento
- [ ] Filtro por tipo y fecha de expedición
- [ ] Agrupación en expedientes (relación many-to-many)
- [ ] Exportación de catálogo en formato DCAT-AP
- [ ] Visualizador de PDF integrado con accesibilidad

**Validación de Integridad:**
```php
public function verifyIntegrity(Document $document): bool
{
    $currentHash = hash_file('sha256', storage_path('app/' . $document->file_path));
    return $currentHash === $document->file_hash;
}
```

---

### GC-06: Asociar Contenido a Dependencias

**Historia de Usuario:**  
Como editor, quiero asociar contenidos a una o más dependencias de la estructura orgánica.

**Criterios de Aceptación:**
- [ ] Relación many-to-many entre contenidos y dependencias
- [ ] Selector de dependencias en formulario de contenido
- [ ] Posibilidad de asociar múltiples dependencias
- [ ] Filtrado de contenidos por dependencia
- [ ] Vista de contenidos agrupados por dependencia

**Implementación:**
```php
// Tabla pivote
Schema::create('content_dependency', function (Blueprint $table) {
    $table->foreignId('content_id');
    $table->foreignId('dependency_id');
    $table->timestamps();
    
    $table->primary(['content_id', 'dependency_id']);
});

// Modelo Content
public function dependencies()
{
    return $this->belongsToMany(Dependency::class)
        ->withTimestamps();
}

// Controlador
public function store(StoreContentRequest $request)
{
    $content = Content::create($request->validated());
    
    // Asociar dependencias
    $content->dependencies()->sync($request->input('dependency_ids', []));
    
    return response()->json($content->load('dependencies'), 201);
}
```

---

## 4. Módulo de Transparencia (TR)

### TR-01: Publicar Estructura Orgánica

**Historia de Usuario:**  
Como administrador de transparencia, quiero publicar y mantener actualizada la estructura orgánica de la Alcaldía.

**Normativa:** Ley 1712/2014 Art. 9, literal a)

**Criterios de Aceptación:**
- [ ] Gestión jerárquica de dependencias (árbol)
- [ ] Campos: nombre, sigla, descripción, funciones, ubicación, horario, teléfono, email
- [ ] Soporte para dependencias padre-hijo (anidamiento)
- [ ] Publicación automática en `/transparencia/estructura-organica`
- [ ] Exportación en JSON, CSV
- [ ] Actualización mensual obligatoria

**Modelo de Datos:**
```php
Schema::create('dependencies', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('acronym')->nullable();
    $table->text('description')->nullable();
    $table->text('functions')->nullable();
    $table->string('location')->nullable();
    $table->string('schedule')->nullable();
    $table->string('phone')->nullable();
    $table->string('email')->nullable();
    $table->foreignId('parent_id')->nullable();
    $table->integer('order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamp('last_updated_at')->useCurrent();
    $table->timestamps();
});

// Modelo
class Dependency extends Model
{
    public function parent()
    {
        return $this->belongsTo(Dependency::class, 'parent_id');
    }
    
    public function children()
    {
        return $this->hasMany(Dependency::class, 'parent_id')
            ->orderBy('order');
    }
}
```

**API Pública:**
```php
// GET /api/v1/transparencia/estructura-organica
// Accept: application/json
{
  "data": [
    {
      "id": 1,
      "nombre": "Despacho del Alcalde",
      "sigla": "DA",
      "funciones": "...",
      "ubicacion": "Palacio Municipal, Piso 3",
      "horario": "Lunes a Viernes 8:00 AM - 5:00 PM",
      "telefono": "+57 1 234 5678",
      "email": "despacho@alcaldia.gov.co",
      "dependencias_hijas": [
        {
          "id": 2,
          "nombre": "Oficina de Prensa",
          "sigla": "OP",
          "..."
        }
      ],
      "ultima_actualizacion": "2026-02-01T10:00:00Z"
    }
  ],
  "meta": {
    "fecha_publicacion": "2026-02-17",
    "proxima_actualizacion": "2026-03-17"
  }
}
```

**Componente Vue:**
```vue
<template>
  <div class="estructura-organica">
    <h1>Estructura Orgánica</h1>
    <TreeView :nodes="dependencies" />
    
    <div class="acciones">
      <button @click="downloadJSON" aria-label="Descargar en formato JSON">
        <i class="fa fa-download"></i> Descargar JSON
      </button>
      <button @click="downloadCSV" aria-label="Descargar en formato CSV">
        <i class="fa fa-download"></i> Descargar CSV
      </button>
    </div>
    
    <p class="ultima-actualizacion">
      Última actualización: {{ formatDate(lastUpdate) }}
    </p>
  </div>
</template>
```

**Recordatorio Automático:**
```php
// Notificar 5 días antes de cumplir mes
$schedule->command('transparency:remind-update structure')
    ->monthly()
    ->at('09:00');
```

---

### TR-02: Directorio de Servidores Públicos

**Historia de Usuario:**  
Como ciudadano, quiero consultar el directorio de funcionarios con sus cargos y contactos.

**Normativa:** Ley 1712/2014 Art. 9, literal b)

**Criterios de Aceptación:**
- [ ] Tabla con: nombre completo, cargo, email, teléfono, escala salarial
- [ ] Integración con tabla `users` donde `is_public_servant = true`
- [ ] Exportación CSV/JSON
- [ ] Email ofuscado en exportación pública (`j***@alcaldia.gov.co`)
- [ ] Actualización mensual
- [ ] Orden alfabético por apellido

**Implementación:**
```php
// Agregar campos a users
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_public_servant')->default(false);
    $table->string('position')->nullable(); // Cargo
    $table->string('phone')->nullable();
    $table->string('salary_scale')->nullable(); // Ej: "Grado 20"
    $table->boolean('show_in_directory')->default(true);
});

// Controlador
public function directory()
{
    $servants = User::where('is_public_servant', true)
        ->where('show_in_directory', true)
        ->orderBy('last_name')
        ->get(['name', 'last_name', 'position', 'email', 'phone', 'salary_scale']);
    
    return response()->json($servants);
}

// Ofuscar email en exportación
public function obfuscateEmail(string $email): string
{
    [$local, $domain] = explode('@', $email);
    $obfuscated = substr($local, 0, 1) . '***';
    return $obfuscated . '@' . $domain;
}
```

---

### TR-03: Información Presupuestal

**Historia de Usuario:**  
Como ciudadano, quiero consultar el presupuesto de la Alcaldía y su ejecución.

**Normativa:** Ley 1712/2014 Art. 9, literal e)

**Criterios de Aceptación:**
- [ ] Carga de archivos PDF/Excel de presupuesto
- [ ] Visualización en tablas (parseo opcional)
- [ ] Descarga de archivos originales
- [ ] Histórico de al menos 3 años
- [ ] API con datos estructurados (si están disponibles)
- [ ] Actualización mensual
- [ ] Formatos: PDF, Excel, JSON

**Modelo:**
```php
Schema::create('budgets', function (Blueprint $table) {
    $table->id();
    $table->integer('year');
    $table->enum('type', ['aprobado', 'modificado', 'ejecutado']);
    $table->string('file_path'); // Archivo original
    $table->json('structured_data')->nullable(); // Datos parseados (opcional)
    $table->text('description')->nullable();
    $table->date('published_at');
    $table->timestamps();
});
```

**Vista:**
```vue
<template>
  <div class="presupuesto">
    <h1>Información Presupuestal</h1>
    
    <div class="filtros">
      <label for="year">Año:</label>
      <select id="year" v-model="selectedYear">
        <option v-for="year in years" :key="year" :value="year">
          {{ year }}
        </option>
      </select>
      
      <label for="type">Tipo:</label>
      <select id="type" v-model="selectedType">
        <option value="aprobado">Presupuesto Aprobado</option>
        <option value="modificado">Modificaciones</option>
        <option value="ejecutado">Ejecución</option>
      </select>
    </div>
    
    <div class="resultados">
      <BudgetTable :data="budgetData" />
      
      <div class="descargas">
        <a :href="downloadUrl" download>
          <i class="fa fa-file-pdf"></i> Descargar PDF
        </a>
        <a :href="excelUrl" download>
          <i class="fa fa-file-excel"></i> Descargar Excel
        </a>
      </div>
    </div>
  </div>
</template>
```

---

### TR-04: Gestionar Contratos y Licitaciones

**Historia de Usuario:**  
Como administrador de transparencia, quiero publicar información de contratos y licitaciones, integrándola con SECOP.

**Normativa:** Ley 1712/2014 Art. 9, literal d)

**Criterios de Aceptación:**
- [ ] Campos: número de contrato, objeto, contratista, valor, plazo, fechas, estado
- [ ] Integración con SECOP (API o carga manual)
- [ ] Búsqueda por contratista, valor, fecha
- [ ] Exportación JSON/XML/CSV
- [ ] Solo muestra contratos no confidenciales
- [ ] Actualización inmediata al registrar nuevo contrato

**Modelo:**
```php
Schema::create('contracts', function (Blueprint $table) {
    $table->id();
    $table->string('contract_number')->unique();
    $table->text('object');
    $table->string('contractor_name');
    $table->string('contractor_id'); // NIT
    $table->decimal('amount', 15, 2);
    $table->date('start_date');
    $table->date('end_date');
    $table->enum('status', ['borrador', 'adjudicado', 'en_ejecucion', 'finalizado', 'terminado']);
    $table->boolean('is_confidential')->default(false);
    $table->string('secop_url')->nullable();
    $table->json('additional_data')->nullable();
    $table->timestamps();
});
```

**API:**
```php
// GET /api/v1/transparencia/contratos
public function index(Request $request)
{
    $query = Contract::where('is_confidential', false);
    
    if ($search = $request->input('contratista')) {
        $query->where('contractor_name', 'like', "%{$search}%");
    }
    
    if ($minAmount = $request->input('valor_minimo')) {
        $query->where('amount', '>=', $minAmount);
    }
    
    return $query->paginate(20);
}

// Formato de exportación
// Accept: application/xml
<?xml version="1.0" encoding="UTF-8"?>
<contratos>
  <contrato>
    <numero>2026-001</numero>
    <objeto>Contratación de servicios de aseo</objeto>
    <contratista>Empresa ABC S.A.S.</contratista>
    <valor>50000000</valor>
    <fecha_inicio>2026-01-15</fecha_inicio>
    <fecha_fin>2026-12-31</fecha_fin>
    <estado>en_ejecucion</estado>
    <url_secop>https://www.contratos.gov.co/...</url_secop>
  </contrato>
</contratos>
```

---

### TR-05: Plan Anticorrupción

**Historia de Usuario:**  
Como administrador de transparencia, quiero publicar el Plan Anticorrupción y sus seguimientos.

**Normativa:** Ley 1474/2011

**Criterios de Aceptación:**
- [ ] Carga del plan vigente (PDF)
- [ ] Seguimientos trimestrales
- [ ] Publicación en ruta `/transparencia/plan-anticorrupcion`
- [ ] Descarga de documentos
- [ ] Actualización anual

**Implementación:**
```php
Schema::create('anticorruption_plans', function (Blueprint $table) {
    $table->id();
    $table->integer('year');
    $table->string('file_path'); // Plan PDF
    $table->date('published_at');
    $table->timestamps();
});

Schema::create('anticorruption_followups', function (Blueprint $table) {
    $table->id();
    $table->foreignId('plan_id');
    $table->integer('quarter'); // 1, 2, 3, 4
    $table->string('file_path'); // Seguimiento PDF
    $table->date('published_at');
    $table->timestamps();
});
```

---

## 5. Módulo de Medios (MM)

### MM-01: Subir Archivos

**Historia de Usuario:**  
Como editor, quiero subir imágenes y documentos para usar en mis contenidos.

**Criterios de Aceptación:**
- [ ] Drag & drop o selector de archivos
- [ ] Validación de tipo: imágenes (jpg, png, gif, webp), documentos (pdf, docx, xlsx)
- [ ] Tamaño máximo: 50 MB
- [ ] Generación de texto alternativo sugerido (IA o manual)
- [ ] Texto alternativo obligatorio para imágenes (WCAG 1.1.1)
- [ ] Asociación a múltiples contenidos (relación many-to-many)

**Modelo:**
```php
Schema::create('media', function (Blueprint $table) {
    $table->id();
    $table->string('filename');
    $table->string('original_filename');
    $table->string('path');
    $table->string('mime_type');
    $table->unsignedBigInteger('size'); // bytes
    $table->string('alt_text')->nullable();
    $table->text('description')->nullable();
    $table->json('metadata')->nullable(); // width, height, etc.
    $table->foreignId('uploaded_by');
    $table->timestamps();
});
```

**Validación:**
```php
public function rules(): array
{
    return [
        'file' => [
            'required',
            'file',
            'max:51200', // 50MB
            'mimes:jpeg,png,gif,webp,pdf,docx,xlsx'
        ],
        'alt_text' => [
            'required_if:mime_type,image/*',
            'string',
            'max:255'
        ]
    ];
}
```

**Componente Vue:**
```vue
<template>
  <div
    class="dropzone"
    @drop.prevent="handleDrop"
    @dragover.prevent
    @dragleave.prevent
  >
    <input
      type="file"
      ref="fileInput"
      @change="handleFileSelect"
      multiple
      accept="image/*,.pdf,.docx,.xlsx"
      aria-label="Seleccionar archivos"
    />
    
    <p>Arrastra archivos aquí o haz clic para seleccionar</p>
    <p class="hint">Tamaño máximo: 50 MB. Formatos: JPG, PNG, PDF, DOCX, XLSX</p>
    
    <div v-if="files.length" class="preview">
      <div v-for="file in files" :key="file.id" class="file-item">
        <img v-if="file.type.startsWith('image/')" :src="file.preview" :alt="file.alt_text || 'Vista previa'" />
        
        <label :for="`alt-${file.id}`">Texto alternativo (obligatorio):</label>
        <input
          :id="`alt-${file.id}`"
          v-model="file.alt_text"
          type="text"
          required
          placeholder="Describe la imagen"
        />
        
        <button @click="upload(file)" :disabled="!file.alt_text">
          Subir
        </button>
      </div>
    </div>
  </div>
</template>
```

---

### MM-02: Almacenamiento Estructurado

**Historia de Usuario:**  
Como desarrollador, quiero que los archivos se almacenen de manera organizada y segura.

**Estructura de Carpetas:**
```
storage/app/public/
├── contents/
│   ├── noticias/
│   │   ├── 2026/
│   │   │   ├── 01/
│   │   │   │   └── imagen-alcalde-xyz.jpg
│   ├── documentos/
│   │   ├── 2026/
│   │   │   └── decreto-001-2026.pdf
├── dependencies/
│   ├── despacho-alcalde/
│   │   └── organigrama.png
├── users/
│   └── avatars/
│       └── user-123.jpg
```

**Generación de Ruta:**
```php
public function generatePath(UploadedFile $file, string $type, ?int $year = null): string
{
    $year = $year ?? now()->year;
    $month = now()->format('m');
    $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
    $extension = $file->getClientOriginalExtension();
    
    // Agregar hash único para evitar colisiones
    $uniqueFilename = $filename . '-' . Str::random(8) . '.' . $extension;
    
    return "{$type}/{$year}/{$month}/{$uniqueFilename}";
}
```

---

### MM-03: Biblioteca Reutilizable

**Historia de Usuario:**  
Como editor, quiero buscar y reutilizar medios existentes en nuevos contenidos.

**Criterios de Aceptación:**
- [ ] Modal de biblioteca de medios
- [ ] Búsqueda por nombre de archivo, alt text
- [ ] Filtros por tipo (imagen, documento), fecha
- [ ] Vista de miniaturas (grid)
- [ ] Selección múltiple
- [ ] Insertar en editor WYSIWYG
- [ ] Deduplicación automática (mismo archivo ya subido no se duplica)

**Deduplicación:**
```php
public function checkDuplicate(UploadedFile $file): ?Media
{
    $hash = hash_file('sha256', $file->getRealPath());
    
    return Media::where('hash', $hash)->first();
}

public function store(UploadedFile $file, Request $request)
{
    // Verificar si ya existe
    if ($existing = $this->checkDuplicate($file)) {
        return response()->json([
            'message' => 'Este archivo ya existe en la biblioteca',
            'media' => $existing
        ], 200);
    }
    
    // Subir nuevo archivo
    // ...
}
```

---

### MM-04: Videos Accesibles

**Historia de Usuario:**  
Como administrador, quiero que los videos cumplan con requisitos de accesibilidad.

**Normativa:** Resolución 1519/2020 (WCAG 2.1 Criterio 1.2.2, 1.2.6)

**Criterios de Aceptación:**
- [ ] Soporte para subtítulos en formato SRT/VTT
- [ ] Carga de archivo de subtítulos al subir video
- [ ] Lengua de Señas Colombiana (LSC) en videos oficiales
- [ ] Reproductor accesible (controles navegables por teclado)
- [ ] Transcripción de audio (opcional pero recomendado)

**Implementación:**
```php
Schema::create('videos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('media_id'); // Archivo de video
    $table->string('subtitles_path')->nullable(); // SRT/VTT
    $table->string('sign_language_path')->nullable(); // Video LSC
    $table->text('transcript')->nullable();
    $table->timestamps();
});
```

**Reproductor Vue:**
```vue
<template>
  <video
    ref="videoPlayer"
    controls
    :aria-label="videoTitle"
    :aria-describedby="`transcript-${videoId}`"
  >
    <source :src="videoUrl" :type="videoMimeType" />
    <track
      v-if="subtitlesUrl"
      kind="subtitles"
      :src="subtitlesUrl"
      srclang="es"
      label="Español"
      default
    />
    <p>Tu navegador no soporta el elemento video.</p>
  </video>
  
  <button @click="toggleSignLanguage" v-if="signLanguageUrl">
    {{ showSignLanguage ? 'Ocultar' : 'Mostrar' }} Lengua de Señas
  </button>
  
  <div v-if="showSignLanguage && signLanguageUrl" class="sign-language-overlay">
    <video :src="signLanguageUrl" autoplay muted></video>
  </div>
  
  <details>
    <summary>Ver transcripción</summary>
    <div :id="`transcript-${videoId}`">
      {{ transcript }}
    </div>
  </details>
</template>
```

---

## 6. Módulo de PQRS (PQ)

### PQ-01: Sistema PQRS Completo

**Historia de Usuario:**  
Como ciudadano, quiero enviar una petición, queja, reclamo o sugerencia y hacer seguimiento de su estado.

**Normativa:** Ley 1712/2014 Art. 11, Ley 1581/2012 Art. 13

**Criterios de Aceptación:**
- [ ] Formulario público (sin autenticación)
- [ ] Tipos: Petición, Queja, Reclamo, Sugerencia
- [ ] Campos: tipo, nombre, email, teléfono, descripción, adjuntos
- [ ] Validación de email
- [ ] Máximo 5 archivos adjuntos (10 MB total)
- [ ] Generación de radicado único: `PQRS-YYYY-XXXXXX`
- [ ] Email de confirmación con link de seguimiento
- [ ] Página pública de seguimiento (radicado + email)
- [ ] Plazo automático: 20 días hábiles
- [ ] Notificación a área responsable
- [ ] Respuesta por administrador
- [ ] Estadísticas públicas: cantidad recibidas, respondidas, tiempo promedio

**Modelo:**
```php
Schema::create('petitions', function (Blueprint $table) {
    $table->id();
    $table->string('radicado')->unique(); // PQRS-2026-000123
    $table->enum('type', ['peticion', 'queja', 'reclamo', 'sugerencia']);
    $table->string('citizen_name');
    $table->string('citizen_email');
    $table->string('citizen_phone')->nullable();
    $table->text('description');
    $table->enum('status', ['nuevo', 'en_revision', 'respondido', 'cerrado'])->default('nuevo');
    $table->text('response')->nullable();
    $table->foreignId('responded_by')->nullable();
    $table->timestamp('responded_at')->nullable();
    $table->date('due_date'); // 20 días hábiles
    $table->boolean('is_overdue')->default(false);
    $table->timestamps();
});

Schema::create('petition_attachments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('petition_id');
    $table->string('filename');
    $table->string('path');
    $table->unsignedBigInteger('size');
    $table->timestamps();
});
```

**Generación de Radicado:**
```php
public function generateRadicado(): string
{
    $year = now()->year;
    $lastRadicado = Petition::whereYear('created_at', $year)
        ->orderBy('id', 'desc')
        ->value('radicado');
    
    if ($lastRadicado) {
        $lastNumber = (int) substr($lastRadicado, -6);
        $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    } else {
        $newNumber = '000001';
    }
    
    return "PQRS-{$year}-{$newNumber}";
}
```

**Cálculo de Días Hábiles:**
```php
use Carbon\Carbon;

public function calculateDueDate(Carbon $startDate, int $businessDays = 20): Carbon
{
    $dueDate = $startDate->copy();
    $daysAdded = 0;
    
    while ($daysAdded < $businessDays) {
        $dueDate->addDay();
        
        // Saltar fines de semana
        if ($dueDate->isWeekend()) {
            continue;
        }
        
        // Saltar festivos (implementar calendario de festivos)
        if ($this->isHoliday($dueDate)) {
            continue;
        }
        
        $daysAdded++;
    }
    
    return $dueDate;
}
```

**Formulario Vue:**
```vue
<template>
  <form @submit.prevent="submitPQRS" class="pqrs-form">
    <h1>Peticiones, Quejas, Reclamos y Sugerencias</h1>
    
    <div class="form-group">
      <label for="type">Tipo de solicitud *</label>
      <select id="type" v-model="form.type" required>
        <option value="">Seleccione...</option>
        <option value="peticion">Petición</option>
        <option value="queja">Queja</option>
        <option value="reclamo">Reclamo</option>
        <option value="sugerencia">Sugerencia</option>
      </select>
    </div>
    
    <div class="form-group">
      <label for="name">Nombre completo *</label>
      <input id="name" v-model="form.name" type="text" required />
    </div>
    
    <div class="form-group">
      <label for="email">Correo electrónico *</label>
      <input id="email" v-model="form.email" type="email" required />
    </div>
    
    <div class="form-group">
      <label for="phone">Teléfono</label>
      <input id="phone" v-model="form.phone" type="tel" />
    </div>
    
    <div class="form-group">
      <label for="description">Descripción *</label>
      <textarea
        id="description"
        v-model="form.description"
        rows="5"
        minlength="20"
        required
      ></textarea>
      <p class="hint">Mínimo 20 caracteres. {{ form.description.length }}/20</p>
    </div>
    
    <div class="form-group">
      <label for="attachments">Archivos adjuntos (opcional)</label>
      <input
        id="attachments"
        type="file"
        @change="handleFiles"
        multiple
        accept=".pdf,.jpg,.png,.docx"
      />
      <p class="hint">Máximo 5 archivos. Tamaño total: 10 MB</p>
    </div>
    
    <div class="form-group">
      <label>
        <input type="checkbox" v-model="form.acceptTerms" required />
        Acepto la <a href="/politica-privacidad" target="_blank">Política de Privacidad</a> y autorizo el tratamiento de mis datos personales conforme a la Ley 1581 de 2012 *
      </label>
    </div>
    
    <button type="submit" :disabled="!canSubmit">
      Enviar solicitud
    </button>
  </form>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePQRS } from '@/composables/usePQRS';

const { submitPQRS, isLoading } = usePQRS();

const form = ref({
  type: '',
  name: '',
  email: '',
  phone: '',
  description: '',
  attachments: [],
  acceptTerms: false
});

const canSubmit = computed(() => {
  return form.value.type &&
         form.value.name &&
         form.value.email &&
         form.value.description.length >= 20 &&
         form.value.acceptTerms;
});

function handleFiles(event: Event) {
  const files = Array.from((event.target as HTMLInputElement).files || []);
  
  if (files.length > 5) {
    alert('Máximo 5 archivos');
    return;
  }
  
  const totalSize = files.reduce((sum, file) => sum + file.size, 0);
  if (totalSize > 10 * 1024 * 1024) {
    alert('El tamaño total no puede exceder 10 MB');
    return;
  }
  
  form.value.attachments = files;
}
</script>
```

**Página de Seguimiento:**
```vue
<template>
  <div class="seguimiento-pqrs">
    <h1>Seguimiento de PQRS</h1>
    
    <form @submit.prevent="trackPQRS">
      <div class="form-group">
        <label for="radicado">Número de radicado</label>
        <input
          id="radicado"
          v-model="radicado"
          type="text"
          placeholder="PQRS-2026-000123"
          required
        />
      </div>
      
      <div class="form-group">
        <label for="email">Correo electrónico</label>
        <input
          id="email"
          v-model="email"
          type="email"
          required
        />
      </div>
      
      <button type="submit">Consultar</button>
    </form>
    
    <div v-if="petition" class="resultado">
      <h2>Estado de su solicitud</h2>
      
      <dl>
        <dt>Radicado:</dt>
        <dd>{{ petition.radicado }}</dd>
        
        <dt>Tipo:</dt>
        <dd>{{ petition.type }}</dd>
        
        <dt>Fecha de radicación:</dt>
        <dd>{{ formatDate(petition.created_at) }}</dd>
        
        <dt>Estado:</dt>
        <dd>
          <span :class="`badge badge-${statusClass(petition.status)}`">
            {{ statusLabel(petition.status) }}
          </span>
        </dd>
        
        <dt>Fecha límite de respuesta:</dt>
        <dd>{{ formatDate(petition.due_date) }}</dd>
        
        <template v-if="petition.status === 'respondido'">
          <dt>Fecha de respuesta:</dt>
          <dd>{{ formatDate(petition.responded_at) }}</dd>
          
          <dt>Respuesta:</dt>
          <dd>{{ petition.response }}</dd>
        </template>
      </dl>
    </div>
  </div>
</template>
```

**Estadísticas Públicas:**
```php
// GET /api/v1/pqrs/estadisticas
public function statistics()
{
    $currentYear = now()->year;
    
    return response()->json([
        'total_recibidas' => Petition::whereYear('created_at', $currentYear)->count(),
        'respondidas' => Petition::whereYear('created_at', $currentYear)
            ->where('status', 'respondido')
            ->count(),
        'pendientes' => Petition::whereYear('created_at', $currentYear)
            ->whereIn('status', ['nuevo', 'en_revision'])
            ->count(),
        'vencidas' => Petition::where('is_overdue', true)->count(),
        'tiempo_promedio_respuesta' => $this->calculateAverageResponseTime(),
        'por_tipo' => Petition::whereYear('created_at', $currentYear)
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get()
    ]);
}

private function calculateAverageResponseTime(): float
{
    $responded = Petition::whereNotNull('responded_at')
        ->whereYear('created_at', now()->year)
        ->get();
    
    if ($responded->isEmpty()) {
        return 0;
    }
    
    $totalDays = $responded->sum(function ($petition) {
        return $petition->created_at->diffInDays($petition->responded_at);
    });
    
    return round($totalDays / $responded->count(), 1);
}
```

---

**Continuará...**

*Este documento continúa con módulos de Datos Abiertos, Reportes ITA/FURAG, Flujos de Negocio y Casos Límite. Para mantener la legibilidad, se ha dividido en múltiples secciones.*

---

**Versión:** 1.0.0 (Parcial)  
**Próxima sección:** Datos Abiertos, Catálogos DCAT-AP, Reportes de Compliance

