# 🏷️ Arquitectura de Tablas Centralizadas: Categorías y Etiquetas

## 🎯 Propósito

Las tablas `categorias` y `etiquetas` son **tablas centralizadas y polimórficas** que manejan **TODA** la taxonomía del sistema a través de tablas pivot polimórficas:

- 📂 **Categorías** → Tabla `categorizables` (polimórfica)
- 🏷️ **Etiquetas** → Tabla `etiquetables` (polimórfica)

## ✅ Cumplimiento de Estándares

### 1. Normalización 4FN (Cuarta Forma Normal)
- ✅ **Sin redundancia**: Una sola tabla de categorías, una sola de etiquetas
- ✅ **Relaciones polimórficas**: Se asocian a CUALQUIER entidad
- ✅ **Valores atómicos**: Cada campo contiene un solo valor
- ✅ **Sin dependencias multivaluadas**: Estructura normalizada

### 2. Principios SOLID
- ✅ **Single Responsibility**: Cada tabla tiene una sola responsabilidad
- ✅ **Open/Closed**: Extensible sin modificar estructura
- ✅ **Dependency Inversion**: Relaciones mediante abstracciones polimórficas

### 3. Clean Code
- ✅ Nombres en español consistentes
- ✅ Estructura lógica y clara
- ✅ Comentarios útiles

## 📊 Estructura de las Tablas

### Tabla: `categorias`

```sql
CREATE TABLE categorias (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255),                    -- Nombre de la categoría
    slug VARCHAR(255) UNIQUE,               -- URL-friendly
    descripcion TEXT NULL,                  -- Descripción
    padre_id BIGINT NULL,                   -- Categoría padre (jerarquía)
    color VARCHAR(255) NULL,                -- Color para UI (#FF5733)
    icono VARCHAR(255) NULL,                -- Icono (fa-folder, etc.)
    orden INT DEFAULT 0,                    -- Orden de visualización
    esta_activo BOOLEAN DEFAULT true,       -- Activo/Inactivo
    creado_en TIMESTAMP,
    actualizado_en TIMESTAMP,
    eliminado_en TIMESTAMP NULL,
    
    FOREIGN KEY (padre_id) REFERENCES categorias(id) ON DELETE CASCADE,
    INDEX categorias_padre_id_index (padre_id),
    INDEX categorias_slug_index (slug),
    INDEX categorias_esta_activo_index (esta_activo)
);
```

### Tabla: `etiquetas`

```sql
CREATE TABLE etiquetas (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255),                    -- Nombre de la etiqueta
    slug VARCHAR(255) UNIQUE,               -- URL-friendly
    color VARCHAR(255) NULL,                -- Color para UI (#00A8E8)
    creado_en TIMESTAMP,
    actualizado_en TIMESTAMP,
    
    INDEX etiquetas_slug_index (slug)
);
```

### Tabla Pivot: `categorizables` (Polimórfica)

```sql
CREATE TABLE categorizables (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    categoria_id BIGINT,                            -- FK a categorias
    categorizable_id BIGINT,                        -- ID de la entidad
    categorizable_tipo VARCHAR(255),                -- Tipo de entidad (App\Models\Contenido)
    creado_en TIMESTAMP,
    actualizado_en TIMESTAMP,
    
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE,
    INDEX categorizables_categorizable_type_categorizable_id_index (categorizable_tipo, categorizable_id),
    INDEX categorizables_categoria_id_index (categoria_id),
    UNIQUE categorias_unique (categoria_id, categorizable_id, categorizable_tipo)
);
```

### Tabla Pivot: `etiquetables` (Polimórfica)

```sql
CREATE TABLE etiquetables (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    etiqueta_id BIGINT,                             -- FK a etiquetas
    etiquetable_id BIGINT,                          -- ID de la entidad
    etiquetable_tipo VARCHAR(255),                  -- Tipo de entidad (App\Models\Noticia)
    creado_en TIMESTAMP,
    actualizado_en TIMESTAMP,
    
    FOREIGN KEY (etiqueta_id) REFERENCES etiquetas(id) ON DELETE CASCADE,
    INDEX etiquetables_etiquetable_type_etiquetable_id_index (etiquetable_tipo, etiquetable_id),
    INDEX etiquetables_etiqueta_id_index (etiqueta_id),
    UNIQUE etiquetas_unique (etiqueta_id, etiquetable_id, etiquetable_tipo)
);
```

## 🔗 Relaciones Polimórficas

### Entidades que usan Categorías y Etiquetas:

| Entidad | Categorías | Etiquetas |
|---------|------------|-----------|
| **Contenidos** | ✅ | ✅ |
| **Noticias** | ✅ | ✅ |
| **Decretos** | ✅ | ✅ |
| **Gacetas** | ✅ | ✅ |
| **Circulares** | ✅ | ✅ |
| **Actas** | ✅ | ✅ |
| **Contratos** | ✅ | ✅ |
| **Presupuesto** | ✅ | ✅ |
| **Datos Abiertos** | ✅ | ✅ |
| **Trámites** | ✅ | ✅ |
| **Planes de Desarrollo** | ✅ | ✅ |
| **CUALQUIER entidad futura** | ✅ | ✅ |

## 💡 Ejemplos de Uso

### 1. Asignar Categorías a un Contenido

```php
// Crear categorías
$categoria = Categoria::create([
    'nombre' => 'Transparencia',
    'slug' => 'transparencia',
    'color' => '#2563EB',
    'esta_activo' => true,
]);

// Asignar a un contenido
$contenido = Contenido::find(1);
$contenido->categorias()->attach($categoria->id);

// O usando sync (reemplaza todas las categorías)
$contenido->categorias()->sync([1, 2, 3]);

// O usando relación polimórfica directa
$contenido->categorias()->create([
    'nombre' => 'Nueva Categoría',
    'slug' => 'nueva-categoria',
]);
```

### 2. Asignar Etiquetas a una Noticia

```php
// Crear etiquetas
$etiqueta = Etiqueta::create([
    'nombre' => 'Urgente',
    'slug' => 'urgente',
    'color' => '#DC2626',
]);

// Asignar a una noticia
$noticia = Noticia::find(1);
$noticia->etiquetas()->attach($etiqueta->id);

// Asignar múltiples etiquetas
$noticia->etiquetas()->sync([1, 2, 3, 4]);
```

### 3. Asignar Categorías a un Decreto

```php
// Decretos pueden tener categorías como "Normativo", "Administrativo", etc.
$decreto = Decreto::find(1);
$decreto->categorias()->attach([
    Categoria::where('slug', 'normativo')->first()->id,
    Categoria::where('slug', 'tributario')->first()->id,
]);
```

### 4. Asignar Etiquetas a un Contrato

```php
// Contratos pueden tener etiquetas como "SECOP", "Vigente", "Alto Valor"
$contrato = Contrato::find(1);
$contrato->etiquetas()->syncWithoutDetaching([
    Etiqueta::where('slug', 'secop')->first()->id,
    Etiqueta::where('slug', 'vigente')->first()->id,
]);
```

### 5. Buscar Contenidos por Categoría

```php
// Obtener todos los contenidos de una categoría
$categoria = Categoria::where('slug', 'transparencia')->first();
$contenidos = $categoria->contenidos;

// O usando la relación inversa
$contenidos = Contenido::whereHas('categorias', function($query) {
    $query->where('slug', 'transparencia');
})->get();
```

### 6. Buscar Noticias por Etiqueta

```php
// Obtener todas las noticias con etiqueta "urgente"
$etiqueta = Etiqueta::where('slug', 'urgente')->first();
$noticias = Noticia::whereHas('etiquetas', function($query) use ($etiqueta) {
    $query->where('etiquetas.id', $etiqueta->id);
})->get();
```

### 7. Categorías Jerárquicas (Árbol)

```php
// Crear categoría padre
$padre = Categoria::create([
    'nombre' => 'Documentos Oficiales',
    'slug' => 'documentos-oficiales',
]);

// Crear subcategorías
$padre->hijos()->createMany([
    ['nombre' => 'Decretos', 'slug' => 'decretos'],
    ['nombre' => 'Gacetas', 'slug' => 'gacetas'],
    ['nombre' => 'Circulares', 'slug' => 'circulares'],
]);

// Obtener árbol completo
$arbol = Categoria::with('hijos.hijos')->whereNull('padre_id')->get();
```

## 🚀 Ventajas de la Arquitectura Centralizada

### 1. Reutilización
- ✅ Una sola categoría "Transparencia" para TODAS las entidades
- ✅ Una sola etiqueta "Urgente" para TODAS las entidades
- ✅ Sin duplicación de datos

### 2. Consistencia
- ✅ Mismas categorías en todo el sistema
- ✅ Mismas etiquetas en todo el sistema
- ✅ Interfaz de usuario consistente

### 3. Mantenibilidad
- ✅ Actualizar una categoría afecta a TODAS las entidades
- ✅ Eliminar una etiqueta obsoleta en un solo lugar
- ✅ Código DRY (Don't Repeat Yourself)

### 4. Escalabilidad
- ✅ Agregar nuevas entidades sin crear nuevas tablas de categorías
- ✅ Jerarquías ilimitadas de categorías
- ✅ Performance optimizada con índices

### 5. Flexibilidad
- ✅ Cualquier entidad puede usar categorías
- ✅ Cualquier entidad puede usar etiquetas
- ✅ Relaciones many-to-many automáticas

## 📝 Eliminación de Redundancias

### Tablas Eliminadas (violaban 4FN):
- ❌ `categorias_noticias` → Ahora usa `categorias` + `categorizables`
- ❌ `contenido_categoria` → Ahora usa `categorizables` polimórfica
- ❌ `news_categories` → Consolidado en `categorias`

### Nueva Arquitectura (4FN Compliant):
- ✅ `categorias` → Universal para TODAS las entidades
- ✅ `etiquetas` → Universal para TODAS las entidades
- ✅ `categorizables` → Pivot polimórfico para categorías
- ✅ `etiquetables` → Pivot polimórfico para etiquetas
- ✅ Cero redundancia
- ✅ Máxima normalización

## 🎨 Modelos Eloquent (Ejemplo)

### Modelo: Categoria

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use SoftDeletes;

    protected $table = 'categorias';
    
    protected $fillable = [
        'nombre', 'slug', 'descripcion', 'padre_id', 
        'color', 'icono', 'orden', 'esta_activo'
    ];
    
    protected $casts = [
        'esta_activo' => 'boolean',
        'orden' => 'integer',
    ];
    
    // Relación jerárquica - padre
    public function padre()
    {
        return $this->belongsTo(Categoria::class, 'padre_id');
    }
    
    // Relación jerárquica - hijos
    public function hijos()
    {
        return $this->hasMany(Categoria::class, 'padre_id');
    }
    
    // Relaciones polimórficas con entidades
    public function contenidos()
    {
        return $this->morphedByMany(Contenido::class, 'categorizable', 'categorizables');
    }
    
    public function noticias()
    {
        return $this->morphedByMany(Noticia::class, 'categorizable', 'categorizables');
    }
    
    public function decretos()
    {
        return $this->morphedByMany(Decreto::class, 'categorizable', 'categorizables');
    }
    
    // ... más relaciones según necesidad
}
```

### Modelo: Etiqueta

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etiqueta extends Model
{
    protected $table = 'etiquetas';
    
    protected $fillable = ['nombre', 'slug', 'color'];
    
    // Relaciones polimórficas
    public function contenidos()
    {
        return $this->morphedByMany(Contenido::class, 'etiquetable', 'etiquetables');
    }
    
    public function noticias()
    {
        return $this->morphedByMany(Noticia::class, 'etiquetable', 'etiquetables');
    }
    
    // ... más relaciones
}
```

### Modelo: Contenido (usando categorías y etiquetas)

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contenido extends Model
{
    protected $table = 'contenidos';
    
    // Relación polimórfica con categorías
    public function categorias()
    {
        return $this->morphToMany(Categoria::class, 'categorizable', 'categorizables');
    }
    
    // Relación polimórfica con etiquetas
    public function etiquetas()
    {
        return $this->morphToMany(Etiqueta::class, 'etiquetable', 'etiquetables');
    }
}
```

## 🔍 Consultas Avanzadas

### Filtrar por múltiples categorías (AND)
```php
$contenidos = Contenido::whereHas('categorias', function($q) {
    $q->where('slug', 'transparencia');
})->whereHas('categorias', function($q) {
    $q->where('slug', 'normativo');
})->get();
```

### Filtrar por múltiples etiquetas (OR)
```php
$noticias = Noticia::whereHas('etiquetas', function($q) {
    $q->whereIn('slug', ['urgente', 'importante']);
})->get();
```

### Contar uso de categorías
```php
$categorias = Categoria::withCount([
    'contenidos',
    'noticias',
    'decretos'
])->get();
```

### Categorías más utilizadas
```php
$topCategorias = Categoria::select('categorias.*')
    ->join('categorizables', 'categorias.id', '=', 'categorizables.categoria_id')
    ->groupBy('categorias.id')
    ->orderByRaw('COUNT(*) DESC')
    ->take(10)
    ->get();
```

## ✅ Conclusión

Las tablas `categorias` y `etiquetas` con sus respectivas tablas pivot polimórficas (`categorizables` y `etiquetables`) forman una **arquitectura profesional, escalable y normalizada (4FN)** que:

- ✅ Cumple con TODOS los estándares requeridos
- ✅ Centraliza la taxonomía del sistema
- ✅ Elimina redundancias completamente
- ✅ Facilita el mantenimiento y extensibilidad
- ✅ Permite reutilización total
- ✅ Optimiza el rendimiento con índices apropiados

**Esta arquitectura está lista para producción y sigue las mejores prácticas de Laravel y diseño de bases de datos.**
