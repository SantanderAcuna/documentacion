# Guía de Desarrollo - CMS Gubernamental Backend

> **Versión:** 1.0  
> **Para:** Desarrolladores Backend  
> **Framework:** Laravel 11.48  
> **Última actualización:** 17 de Febrero, 2026

---

## 📑 Tabla de Contenidos

1. [Configuración del Entorno](#configuración-del-entorno)
2. [Estructura del Proyecto](#estructura-del-proyecto)
3. [Convenciones de Código](#convenciones-de-código)
4. [Workflow de Desarrollo](#workflow-de-desarrollo)
5. [Mejores Prácticas](#mejores-prácticas)
6. [Herramientas de Desarrollo](#herramientas-de-desarrollo)
7. [Debugging](#debugging)
8. [Testing](#testing)

---

## 1. Configuración del Entorno

### 1.1 Requisitos del Sistema

```bash
# Versiones mínimas requeridas
PHP >= 8.3
Composer >= 2.6
MySQL >= 8.0 (o SQLite para desarrollo)
Redis >= 7.0 (opcional, recomendado)
Git >= 2.40
Node.js >= 18.0 (para asset compilation)
```

### 1.2 Setup Inicial

```bash
# 1. Clonar el repositorio
git clone https://github.com/SantanderAcuna/documentacion.git
cd documentacion/backend

# 2. Instalar dependencias
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos
# Editar .env con tus credenciales de BD

# 5. Ejecutar migraciones
php artisan migrate

# 6. Seed inicial de datos
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=AdminUserSeeder

# 7. Crear link de storage
php artisan storage:link

# 8. Verificar instalación
php artisan --version
```

### 1.3 Configuración de IDE

#### VS Code (Recomendado)

Extensiones esenciales:
```json
{
    "recommendations": [
        "bmewburn.vscode-intelephense-client",
        "amiralizadeh9480.laravel-extra-intellisense",
        "onecentlin.laravel-blade",
        "ryannaddy.laravel-artisan",
        "mikestead.dotenv",
        "editorconfig.editorconfig"
    ]
}
```

Configuración de workspace (`.vscode/settings.json`):
```json
{
    "php.suggest.basic": false,
    "intelephense.files.exclude": [
        "**/.git/**",
        "**/.svn/**",
        "**/.hg/**",
        "**/CVS/**",
        "**/.DS_Store/**",
        "**/node_modules/**",
        "**/bower_components/**",
        "**/vendor/**/{Tests,tests}/**"
    ],
    "files.associations": {
        "*.blade.php": "blade"
    }
}
```

#### PHPStorm

1. Configurar Laravel Plugin
2. Habilitar Symfony Plugin
3. Configurar PHP Interpreter a 8.3+
4. Indexar vendor/ para autocompletado

---

## 2. Estructura del Proyecto

### 2.1 Estructura de Directorios

```
backend/
├── app/
│   ├── Console/              # Artisan commands
│   ├── Exceptions/           # Exception handlers
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/V1/      # API Controllers v1
│   │   ├── Middleware/       # Custom middleware
│   │   └── Requests/         # Form Requests (futuro)
│   ├── Models/               # Eloquent Models
│   ├── Policies/             # Authorization Policies (futuro)
│   ├── Providers/            # Service Providers
│   └── Services/             # Business Logic Services (futuro)
│
├── bootstrap/                # Application bootstrap
│
├── config/                   # Configuration files
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── permission.php
│   └── sanctum.php
│
├── database/
│   ├── factories/            # Model Factories
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
│
├── public/                   # Web root
│   └── index.php            # Entry point
│
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│
├── routes/
│   ├── api.php              # API routes
│   ├── console.php          # Artisan commands
│   └── web.php              # Web routes
│
├── storage/
│   ├── app/
│   ├── framework/
│   └── logs/
│
├── tests/
│   ├── Feature/             # Feature tests
│   └── Unit/                # Unit tests
│
├── .env.example             # Environment template
├── artisan                  # Artisan CLI
├── composer.json            # PHP dependencies
└── phpunit.xml             # PHPUnit configuration
```

### 2.2 Organización de Código

#### Controllers
```
app/Http/Controllers/Api/V1/
├── AuthController.php        # Authentication
├── CategoryController.php    # Categories CRUD
├── ContentController.php     # Contents CRUD
├── MediaController.php       # File uploads
├── PqrsController.php        # PQRS management
└── TagController.php         # Tags CRUD
```

#### Models
```
app/Models/
├── User.php                  # Users & Auth
├── Category.php              # Categories (hierarchical)
├── Content.php               # Main content
├── Tag.php                   # Tags
├── Media.php                 # File attachments
└── Pqrs.php                  # PQRS system
```

#### Migrations
```
database/migrations/
├── 2026_02_17_*_create_permission_tables.php
├── 2026_02_17_*_create_activity_log_table.php
├── 2026_02_17_*_create_categories_table.php
├── 2026_02_17_*_create_contents_table.php
├── 2026_02_17_*_create_tags_table.php
├── 2026_02_17_*_create_media_table.php
└── 2026_02_17_*_create_pqrs_table.php
```

---

## 3. Convenciones de Código

### 3.1 Estándar PSR-12

Seguir estrictamente PSR-12:

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $contents = Content::query()
            ->with(['author', 'category'])
            ->published()
            ->paginate(15);

        return response()->json($contents);
    }
}
```

### 3.2 Nomenclatura

#### Clases
```php
// PascalCase para clases
class ContentController {}
class UserService {}
class CreateContentRequest {}
```

#### Métodos
```php
// camelCase para métodos
public function storeContent() {}
public function getUserById() {}
protected function validateInput() {}
```

#### Variables
```php
// camelCase para variables
$userId = 1;
$contentList = [];
$isPublished = true;
```

#### Constantes
```php
// UPPER_SNAKE_CASE para constantes
const MAX_UPLOAD_SIZE = 10485760;
const DEFAULT_LOCALE = 'es';
```

#### Rutas
```php
// kebab-case para URLs
/api/v1/user-profile
/api/v1/content-categories
```

#### Base de Datos
```php
// snake_case para tablas y columnas
Schema::create('content_tags', function (Blueprint $table) {
    $table->id();
    $table->foreignId('content_id');
    $table->foreignId('tag_id');
    $table->timestamps();
});
```

### 3.3 Comentarios y Documentación

```php
/**
 * Store a newly created content in storage.
 *
 * @param Request $request
 * @return JsonResponse
 *
 * @throws \Illuminate\Validation\ValidationException
 */
public function store(Request $request): JsonResponse
{
    // Validate the request
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
    ]);

    // Create the content
    $content = Content::create($validated);

    // Return response
    return response()->json([
        'content' => $content,
        'message' => 'Content created successfully'
    ], 201);
}
```

---

## 4. Workflow de Desarrollo

### 4.1 Git Workflow

#### Branching Strategy (Trunk-based)

```bash
main              # Production branch
  └─ develop      # Development branch (no usamos)
      └─ feature/nombre-feature  # Feature branches
```

#### Flujo de Trabajo

```bash
# 1. Crear feature branch
git checkout -b feature/add-notifications

# 2. Desarrollar feature
# ... hacer cambios ...

# 3. Commit frecuentes (Conventional Commits)
git add .
git commit -m "feat: add email notifications for PQRS"

# 4. Push a remote
git push origin feature/add-notifications

# 5. Crear Pull Request en GitHub

# 6. Code Review

# 7. Merge a main (después de aprobar)
git checkout main
git merge feature/add-notifications
git push origin main

# 8. Eliminar feature branch
git branch -d feature/add-notifications
```

### 4.2 Conventional Commits

Formato: `<type>(<scope>): <subject>`

Tipos permitidos:
```
feat:     Nueva característica
fix:      Corrección de bug
docs:     Cambios en documentación
style:    Formato, sin cambios en código
refactor: Refactorización de código
test:     Agregar o modificar tests
chore:    Tareas de mantenimiento
```

Ejemplos:
```bash
feat(api): add pagination to contents endpoint
fix(auth): resolve token expiration issue
docs(readme): update installation instructions
test(pqrs): add unit tests for folio generation
refactor(models): optimize query performance
```

### 4.3 Pull Request Process

#### Template de PR

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Checklist
- [ ] Code follows PSR-12 style guide
- [ ] Tests added/updated
- [ ] Documentation updated
- [ ] No new warnings or errors

## Related Issues
Closes #123
```

---

## 5. Mejores Prácticas

### 5.1 Eloquent Best Practices

#### ✅ DO: Use Eager Loading
```php
// Good - N+1 avoided
$contents = Content::with(['author', 'category', 'tags'])->get();

// Bad - N+1 problem
$contents = Content::all();
foreach ($contents as $content) {
    echo $content->author->name; // N+1 query
}
```

#### ✅ DO: Use Query Scopes
```php
// Model
public function scopePublished($query)
{
    return $query->where('status', 'published')
                 ->whereNotNull('published_at');
}

// Usage
$contents = Content::published()->get();
```

#### ✅ DO: Use Mutators and Accessors
```php
// Model
protected function title(): Attribute
{
    return Attribute::make(
        get: fn ($value) => ucfirst($value),
        set: fn ($value) => strtolower($value),
    );
}
```

#### ❌ DON'T: Use Raw Queries with User Input
```php
// Bad - SQL Injection risk
DB::select("SELECT * FROM users WHERE email = '$email'");

// Good - Use bindings
DB::select('SELECT * FROM users WHERE email = ?', [$email]);

// Better - Use Eloquent
User::where('email', $email)->first();
```

### 5.2 Controller Best Practices

#### ✅ DO: Keep Controllers Thin
```php
// Good - Controller delegates to Service
public function store(Request $request)
{
    $content = $this->contentService->create($request->validated());
    return response()->json($content, 201);
}

// Bad - Too much logic in controller
public function store(Request $request)
{
    $validated = $request->validate([...]);
    $slug = Str::slug($validated['title']);
    $content = Content::create([...]);
    $content->tags()->attach($validated['tags']);
    activity()->performedOn($content)->log('created');
    return response()->json($content, 201);
}
```

#### ✅ DO: Use Form Requests (futuro)
```php
// Good
public function store(StoreContentRequest $request)
{
    // Validation already done
    $content = Content::create($request->validated());
}

// Current (acceptable)
public function store(Request $request)
{
    $validated = $request->validate([...]);
    $content = Content::create($validated);
}
```

### 5.3 Seguridad Best Practices

#### ✅ DO: Validate All Input
```php
$validated = $request->validate([
    'email' => 'required|email|unique:users',
    'password' => 'required|min:8|confirmed',
    'name' => 'required|string|max:255',
]);
```

#### ✅ DO: Use Mass Assignment Protection
```php
// Model
protected $fillable = ['title', 'content', 'category_id'];
// or
protected $guarded = ['id', 'created_at', 'updated_at'];
```

#### ✅ DO: Hash Passwords
```php
use Illuminate\Support\Facades\Hash;

// Creating user
User::create([
    'password' => Hash::make($request->password),
]);

// Verifying password
if (Hash::check($password, $user->password)) {
    // Correct
}
```

#### ❌ DON'T: Return Sensitive Data
```php
// Bad - exposes password
return User::find($id);

// Good - hide sensitive fields
return User::find($id)->makeHidden(['password', 'remember_token']);

// Better - use API Resources
return new UserResource(User::find($id));
```

### 5.4 Performance Best Practices

#### ✅ DO: Use Indexes
```php
Schema::table('contents', function (Blueprint $table) {
    $table->index('slug');
    $table->index(['category_id', 'published_at']);
    $table->fullText(['title', 'content']);
});
```

#### ✅ DO: Cache Expensive Queries
```php
$categories = Cache::remember('categories.all', 3600, function () {
    return Category::with('children')->root()->get();
});
```

#### ✅ DO: Use Pagination
```php
// Good
$contents = Content::paginate(15);

// Bad - load all records
$contents = Content::all();
```

---

## 6. Herramientas de Desarrollo

### 6.1 Artisan Commands Útiles

```bash
# Crear nuevos archivos
php artisan make:controller Api/V1/ExampleController --api
php artisan make:model Example -m  # con migración
php artisan make:migration create_examples_table
php artisan make:seeder ExampleSeeder
php artisan make:request StoreExampleRequest

# Base de datos
php artisan migrate                # Ejecutar migraciones
php artisan migrate:fresh --seed   # Reset + seed
php artisan db:seed               # Ejecutar seeders

# Cache
php artisan config:cache          # Cache config
php artisan route:cache           # Cache routes
php artisan view:cache            # Cache views
php artisan cache:clear           # Clear app cache

# Debugging
php artisan route:list            # Ver todas las rutas
php artisan tinker                # REPL interactivo

# Testing
php artisan test                  # Ejecutar tests
php artisan test --filter=ContentTest  # Test específico
```

### 6.2 Tinker para Debugging

```php
php artisan tinker

// Consultar datos
>>> User::count()
=> 4

>>> Content::published()->count()
=> 0

// Crear datos de prueba
>>> $user = User::factory()->create()

>>> $content = Content::create([
    'title' => 'Test',
    'slug' => 'test',
    'content' => 'Content',
    'author_id' => 1,
])

// Probar relaciones
>>> $content->author->name
=> "Admin User"

// Probar scopes
>>> Content::published()->get()
```

### 6.3 Laravel Telescope (Recomendado para desarrollo)

```bash
# Instalar
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate

# Acceder
http://localhost:8000/telescope
```

Características:
- Request inspector
- Query inspector
- Exception tracking
- Log viewer
- Cache monitoring

---

## 7. Debugging

### 7.1 Debug Methods

```php
// dd() - Dump and Die
dd($variable);
dd($request->all());

// dump() - Dump and Continue
dump($variable);

// Log to file
Log::info('User created', ['user_id' => $user->id]);
Log::error('Error occurred', ['error' => $e->getMessage()]);

// Query debugging
DB::enableQueryLog();
// ... queries ...
dd(DB::getQueryLog());

// Debug bar (recomendado)
composer require barryvdh/laravel-debugbar --dev
```

### 7.2 Common Issues

#### Issue: "Class not found"
```bash
# Solution
composer dump-autoload
php artisan clear-compiled
```

#### Issue: "Too few arguments to function"
```bash
# Check method signature
# Verify route parameters match controller
```

#### Issue: "CSRF token mismatch"
```bash
# For API, exclude from CSRF verification
# In app/Http/Middleware/VerifyCsrfToken.php
protected $except = [
    'api/*'
];
```

---

## 8. Testing

### 8.1 Escribir Tests

```php
// Feature Test Example
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_content_with_permission()
    {
        // Arrange
        $user = User::factory()->create();
        $user->givePermissionTo('crear-contenidos');
        
        // Act
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/contents', [
                'title' => 'Test Content',
                'content' => 'Test body',
            ]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('contents', [
            'title' => 'Test Content',
        ]);
    }
}
```

### 8.2 Running Tests

```bash
# Todos los tests
php artisan test

# Test específico
php artisan test --filter=ContentTest

# Con coverage
php artisan test --coverage

# Test groups
php artisan test --group=feature

# Parallel execution
php artisan test --parallel
```

---

## 9. Recursos Adicionales

### 9.1 Documentación Oficial

- [Laravel 11.x Docs](https://laravel.com/docs/11.x)
- [Sanctum Docs](https://laravel.com/docs/11.x/sanctum)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)

### 9.2 Tools & Packages Recomendados

```json
{
    "require-dev": {
        "laravel/telescope": "^5.0",
        "barryvdh/laravel-debugbar": "^3.9",
        "phpstan/phpstan": "^1.10"
    }
}
```

---

**Happy Coding!** 🚀

*Esta guía se actualiza continuamente. Última actualización: 17 de Febrero, 2026*
