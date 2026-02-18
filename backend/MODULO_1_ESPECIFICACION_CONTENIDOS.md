# 📋 MÓDULO 1: Sistema de Gestión de Contenidos

## 🎯 Especificación Completa

### Objetivo
Implementar el módulo completo de gestión de contenidos siguiendo arquitectura Repository Pattern, Service Layer, DTOs y principios SOLID.

---

## 📐 Arquitectura

### Capas de la Aplicación

```
┌─────────────────────────────────────────────┐
│           Controller Layer                  │
│   (Validación, Transformación, HTTP)        │
└──────────────┬──────────────────────────────┘
               │
┌──────────────▼──────────────────────────────┐
│           Service Layer                     │
│   (Lógica de Negocio, Orquestación)        │
└──────────────┬──────────────────────────────┘
               │
┌──────────────▼──────────────────────────────┐
│         Repository Layer                    │
│   (Acceso a Datos, Abstracción DB)         │
└──────────────┬──────────────────────────────┘
               │
┌──────────────▼──────────────────────────────┐
│            Model Layer                      │
│   (Eloquent ORM, Relaciones)               │
└─────────────────────────────────────────────┘
```

---

## 📦 Componentes a Implementar

### 1. DTOs (Data Transfer Objects)

#### 1.1 ContenidoDTO
**Ubicación:** `app/DTOs/Contenido/ContenidoDTO.php`

```php
<?php

namespace App\DTOs\Contenido;

class ContenidoDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $tipo_contenido_id,
        public readonly int $dependencia_id,
        public readonly ?int $usuario_id,
        public readonly string $titulo,
        public readonly string $slug,
        public readonly ?string $resumen,
        public readonly ?string $cuerpo,
        public readonly string $estado,
        public readonly ?string $publicado_en,
        public readonly bool $es_destacado,
        // ... otros campos
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            tipo_contenido_id: $data['tipo_contenido_id'],
            // ... mapeo completo
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tipo_contenido_id' => $this->tipo_contenido_id,
            // ... mapeo completo
        ];
    }
}
```

#### 1.2 CreateContenidoDTO
**Ubicación:** `app/DTOs/Contenido/CreateContenidoDTO.php`

Solo campos necesarios para crear un contenido.

#### 1.3 UpdateContenidoDTO
**Ubicación:** `app/DTOs/Contenido/UpdateContenidoDTO.php`

Solo campos modificables.

---

### 2. Repository Pattern

#### 2.1 ContenidoRepositoryInterface
**Ubicación:** `app/Repositories/Contracts/ContenidoRepositoryInterface.php`

```php
<?php

namespace App\Repositories\Contracts;

use App\DTOs\Contenido\CreateContenidoDTO;
use App\DTOs\Contenido\UpdateContenidoDTO;
use App\Models\Contenido;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ContenidoRepositoryInterface
{
    public function findById(int $id): ?Contenido;
    
    public function findBySlug(string $slug): ?Contenido;
    
    public function all(array $filters = []): Collection;
    
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    public function create(CreateContenidoDTO $dto): Contenido;
    
    public function update(int $id, UpdateContenidoDTO $dto): Contenido;
    
    public function delete(int $id): bool;
    
    public function restore(int $id): bool;
    
    public function forceDelete(int $id): bool;
    
    public function publicar(int $id): Contenido;
    
    public function archivar(int $id): Contenido;
    
    public function destacar(int $id): Contenido;
    
    public function search(string $query, array $filters = []): Collection;
}
```

#### 2.2 ContenidoRepository
**Ubicación:** `app/Repositories/Eloquent/ContenidoRepository.php`

Implementación con Eloquent.

---

### 3. Service Layer

#### 3.1 ContenidoServiceInterface
**Ubicación:** `app/Services/Contracts/ContenidoServiceInterface.php`

```php
<?php

namespace App\Services\Contracts;

use App\DTOs\Contenido\CreateContenidoDTO;
use App\DTOs\Contenido\UpdateContenidoDTO;
use App\DTOs\Contenido\ContenidoDTO;

interface ContenidoServiceInterface
{
    public function obtenerContenido(int $id): ?ContenidoDTO;
    
    public function listarContenidos(array $filtros = [], int $porPagina = 15): array;
    
    public function crearContenido(CreateContenidoDTO $dto): ContenidoDTO;
    
    public function actualizarContenido(int $id, UpdateContenidoDTO $dto): ContenidoDTO;
    
    public function eliminarContenido(int $id): bool;
    
    public function publicarContenido(int $id): ContenidoDTO;
    
    public function archivarContenido(int $id): ContenidoDTO;
    
    public function buscarContenidos(string $termino, array $filtros = []): array;
}
```

#### 3.2 ContenidoService
**Ubicación:** `app/Services/ContenidoService.php`

Lógica de negocio, orquestación, validaciones business logic.

---

### 4. HTTP Layer

#### 4.1 Form Requests

##### StoreContenidoRequest
**Ubicación:** `app/Http/Requests/Contenido/StoreContenidoRequest.php`

```php
<?php

namespace App\Http\Requests\Contenido;

use Illuminate\Foundation\Http\FormRequest;

class StoreContenidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // O verificar permisos
    }

    public function rules(): array
    {
        return [
            'tipo_contenido_id' => ['required', 'integer', 'exists:tipos_contenido,id'],
            'dependencia_id' => ['required', 'integer', 'exists:dependencias,id'],
            'titulo' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:contenidos,slug'],
            'resumen' => ['nullable', 'string', 'max:500'],
            'cuerpo' => ['nullable', 'string'],
            'estado' => ['required', 'string', 'in:borrador,publicado,archivado'],
            'es_destacado' => ['boolean'],
            // ... más reglas
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_contenido_id.required' => 'El tipo de contenido es obligatorio.',
            'tipo_contenido_id.exists' => 'El tipo de contenido seleccionado no existe.',
            'titulo.required' => 'El título es obligatorio.',
            'titulo.max' => 'El título no puede exceder 255 caracteres.',
            // ... más mensajes
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo_contenido_id' => 'tipo de contenido',
            'dependencia_id' => 'dependencia',
            // ... más atributos
        ];
    }
}
```

##### UpdateContenidoRequest
Similar pero con reglas para actualización.

---

#### 4.2 API Resources

##### ContenidoResource
**Ubicación:** `app/Http/Resources/ContenidoResource.php`

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContenidoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo_contenido' => [
                'id' => $this->tipoContenido->id,
                'nombre' => $this->tipoContenido->nombre,
                'slug' => $this->tipoContenido->slug,
            ],
            'dependencia' => [
                'id' => $this->dependencia->id,
                'nombre' => $this->dependencia->nombre,
            ],
            'titulo' => $this->titulo,
            'slug' => $this->slug,
            'resumen' => $this->resumen,
            'cuerpo' => $this->cuerpo,
            'estado' => $this->estado,
            'publicado_en' => $this->publicado_en,
            'es_destacado' => (bool) $this->es_destacado,
            'conteo_vistas' => $this->conteo_vistas,
            'categorias' => CategoriaResource::collection($this->whenLoaded('categorias')),
            'etiquetas' => EtiquetaResource::collection($this->whenLoaded('etiquetas')),
            'medios' => MedioResource::collection($this->whenLoaded('medios')),
            'creado_en' => $this->created_at?->toIso8601String(),
            'actualizado_en' => $this->updated_at?->toIso8601String(),
        ];
    }
}
```

##### ContenidoCollection
**Ubicación:** `app/Http/Resources/ContenidoCollection.php`

---

#### 4.3 Controller

##### ContenidoController
**Ubicación:** `app/Http/Controllers/Api/V1/ContenidoController.php`

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contenido\StoreContenidoRequest;
use App\Http\Requests\Contenido\UpdateContenidoRequest;
use App\Http\Resources\ContenidoResource;
use App\Http\Resources\ContenidoCollection;
use App\Services\Contracts\ContenidoServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContenidoController extends Controller
{
    public function __construct(
        private readonly ContenidoServiceInterface $contenidoService
    ) {}

    public function index(Request $request): ContenidoCollection
    {
        // GET /api/v1/contenidos
    }

    public function store(StoreContenidoRequest $request): JsonResponse
    {
        // POST /api/v1/contenidos
    }

    public function show(int $id): JsonResponse
    {
        // GET /api/v1/contenidos/{id}
    }

    public function update(UpdateContenidoRequest $request, int $id): JsonResponse
    {
        // PUT/PATCH /api/v1/contenidos/{id}
    }

    public function destroy(int $id): JsonResponse
    {
        // DELETE /api/v1/contenidos/{id}
    }

    public function publicar(int $id): JsonResponse
    {
        // POST /api/v1/contenidos/{id}/publicar
    }

    public function archivar(int $id): JsonResponse
    {
        // POST /api/v1/contenidos/{id}/archivar
    }

    public function search(Request $request): ContenidoCollection
    {
        // GET /api/v1/contenidos/search?q=termino
    }
}
```

---

### 5. Dependency Injection

#### RepositoryServiceProvider
**Ubicación:** `app/Providers/RepositoryServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\ContenidoRepositoryInterface;
use App\Repositories\Eloquent\ContenidoRepository;
use App\Services\Contracts\ContenidoServiceInterface;
use App\Services\ContenidoService;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repositories
        $this->app->bind(
            ContenidoRepositoryInterface::class,
            ContenidoRepository::class
        );

        // Services
        $this->app->bind(
            ContenidoServiceInterface::class,
            ContenidoService::class
        );
    }

    public function boot(): void
    {
        //
    }
}
```

Registrar en `config/app.php`:
```php
'providers' => [
    // ...
    App\Providers\RepositoryServiceProvider::class,
],
```

---

### 6. Routes

#### api.php
**Ubicación:** `routes/api.php`

```php
use App\Http\Controllers\Api\V1\ContenidoController;

Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        // Contenidos
        Route::apiResource('contenidos', ContenidoController::class);
        Route::post('contenidos/{id}/publicar', [ContenidoController::class, 'publicar']);
        Route::post('contenidos/{id}/archivar', [ContenidoController::class, 'archivar']);
        Route::get('contenidos/search', [ContenidoController::class, 'search']);
    });
});
```

---

### 7. Tests

#### 7.1 Unit Tests

##### ContenidoServiceTest
**Ubicación:** `tests/Unit/Services/ContenidoServiceTest.php`

#### 7.2 Feature Tests

##### ContenidoControllerTest
**Ubicación:** `tests/Feature/Api/V1/ContenidoControllerTest.php`

```php
<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Contenido;
use App\Models\TipoContenido;
use App\Models\Dependencia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContenidoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_puede_listar_contenidos(): void
    {
        Contenido::factory()->count(5)->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/contenidos');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'titulo', 'slug', 'estado']
                ],
                'meta',
                'links'
            ]);
    }

    public function test_puede_crear_contenido(): void
    {
        $tipoContenido = TipoContenido::factory()->create();
        $dependencia = Dependencia::factory()->create();

        $data = [
            'tipo_contenido_id' => $tipoContenido->id,
            'dependencia_id' => $dependencia->id,
            'titulo' => 'Nuevo Contenido',
            'estado' => 'borrador',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/contenidos', $data);

        $response->assertCreated()
            ->assertJsonFragment(['titulo' => 'Nuevo Contenido']);

        $this->assertDatabaseHas('contenidos', [
            'titulo' => 'Nuevo Contenido'
        ]);
    }

    // ... más tests
}
```

---

## 📝 Checklist de Implementación

### Backend
- [ ] DTOs (CreateContenidoDTO, UpdateContenidoDTO, ContenidoDTO)
- [ ] Repository Interface
- [ ] Repository Implementation
- [ ] Service Interface
- [ ] Service Implementation
- [ ] Form Requests (Store, Update)
- [ ] API Resources (Resource, Collection)
- [ ] Controller
- [ ] Service Provider
- [ ] Routes
- [ ] Unit Tests
- [ ] Feature Tests

### Frontend (Vue 3 + TypeScript)
- [ ] TypeScript Interfaces
- [ ] API Service
- [ ] Composables
- [ ] Components (List, Form, Show)
- [ ] Views
- [ ] Store Pinia
- [ ] Routes

### Documentación
- [ ] Especificación (este archivo)
- [ ] Implementación paso a paso
- [ ] Ejemplos de uso
- [ ] Tests coverage report

---

## ✅ Criterios de Aceptación

1. ✅ Cumple PSR-12
2. ✅ Cumple SOLID
3. ✅ Usa Repository Pattern
4. ✅ Usa Service Layer
5. ✅ Usa DTOs
6. ✅ Validación robusta con mensajes en español
7. ✅ API Resources para transformación
8. ✅ Tests con cobertura 80%+
9. ✅ Documentación completa
10. ✅ Inyección de dependencias
11. ✅ Código limpio y autoexplicativo
12. ✅ Sin código duplicado

---

## 🚀 Próximo Módulo

Después de la aprobación ("OK"):
- **Módulo 2:** Sistema de Categorías y Taxonomía
- **Módulo 3:** Sistema de Medios
- **Módulo 4:** Sistema de Workflow
- etc...
