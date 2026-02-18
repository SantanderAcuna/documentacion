# 📋 Arquitectura de Contenidos Centralizada

## 🎯 Concepto

La tabla `contenidos` es la **tabla universal centralizada** que maneja **TODOS** los tipos de contenido del sistema CMS.

## ❌ Arquitectura Anterior (Descartada)

```
contenidos (solo posts, blogs, páginas)
decretos (tabla separada)
gacetas (tabla separada)
circulares (tabla separada)
actas (tabla separada)
noticias (tabla separada)
```

**Problemas:**
- ❌ Duplicación de código
- ❌ Queries fragmentadas
- ❌ Difícil mantener consistencia
- ❌ Permisos complicados
- ❌ Búsqueda global compleja

## ✅ Nueva Arquitectura Centralizada

```
contenidos (UNIVERSAL - maneja TODO)
  ├─ Posts
  ├─ Blogs
  ├─ Noticias
  ├─ Páginas
  ├─ Eventos
  ├─ Decretos
  ├─ Gacetas
  ├─ Circulares
  ├─ Actas
  ├─ Contratos
  ├─ Resoluciones
  ├─ Acuerdos
  └─ Cualquier tipo futuro
```

**Beneficios:**
- ✅ DRY (Don't Repeat Yourself)
- ✅ Queries unificadas
- ✅ Búsqueda global simple
- ✅ Permisos centralizados
- ✅ Fácil agregar nuevos tipos
- ✅ Escalable a 1M+ registros

## 📊 Estructura de la Tabla `contenidos`

### Campos Universales (para todos los tipos)

```sql
-- Relaciones
tipo_contenido_id    → FK a tipos_contenido (determina el tipo)
dependencia_id       → FK a dependencias
usuario_id           → FK a usuarios (autor)

-- Contenido básico
titulo               → Título del contenido
slug                 → URL amigable (único)
resumen              → Resumen corto
cuerpo               → Contenido principal (texto largo)
imagen_destacada     → Imagen principal

-- Estado y publicación
estado               → borrador | publicado | archivado | revision
publicado_en         → Fecha/hora de publicación
conteo_vistas        → Contador de vistas
es_destacado         → Destacar en portada
comentarios_habilitados → Permitir comentarios

-- Metadatos
metadatos            → JSON (campos personalizados por tipo)

-- Auditoría
creado_por           → FK a usuarios
actualizado_por      → FK a usuarios
created_at, updated_at, deleted_at
```

### Campos Específicos por Tipo

#### Para Documentos Oficiales (Decretos, Gacetas, Circulares, Actas)

```sql
numero               → Ej: DECRETO-001-2026
fecha_emision        → Fecha de emisión
fecha_publicacion    → Fecha de publicación oficial
ruta_archivo         → storage/decretos/2026/decreto-001.pdf
nombre_archivo       → decreto-001-2026.pdf
```

#### Para Eventos

```sql
fecha_inicio         → Inicio del evento
fecha_fin            → Fin del evento
ubicacion            → Lugar del evento
```

#### Para Actas

```sql
tipo_reunion         → Tipo de reunión
asistentes           → JSON array con lista de asistentes
```

#### Para Contratos

```sql
nombre_contratista   → Nombre del contratista
identificacion_contratista → NIT/CC
tipo_contrato        → obra | servicios | suministro | consultoria
monto                → Valor del contrato (decimal)
url_secop            → URL en SECOP
```

## 🔍 Uso con Scopes

### Obtener Decretos

```php
use App\Models\Contenido;

// Obtener todos los decretos publicados
$decretos = Contenido::decretos()
    ->publicados()
    ->recientes()
    ->paginate(20);

// Buscar decreto por número
$decreto = Contenido::decretos()
    ->porNumero('DECRETO-001-2026')
    ->first();
```

### Obtener Noticias

```php
// Noticias destacadas
$noticias = Contenido::noticias()
    ->publicados()
    ->destacados()
    ->take(5)
    ->get();
```

### Obtener Eventos Próximos

```php
// Eventos futuros
$eventos = Contenido::eventos()
    ->publicados()
    ->where('fecha_inicio', '>=', now())
    ->orderBy('fecha_inicio', 'asc')
    ->get();
```

### Crear Contenido por Tipo

```php
// Crear un Decreto
$decreto = Contenido::create([
    'tipo_contenido_id' => TipoContenido::where('slug', 'decreto')->first()->id,
    'titulo' => 'Decreto 001 de 2026',
    'slug' => 'decreto-001-2026',
    'numero' => 'DECRETO-001-2026',
    'fecha_emision' => '2026-01-15',
    'cuerpo' => 'Por medio del cual...',
    'ruta_archivo' => 'storage/decretos/2026/decreto-001.pdf',
    'estado' => 'publicado',
    'usuario_id' => auth()->id(),
    'dependencia_id' => 1,
]);

// Crear una Noticia
$noticia = Contenido::create([
    'tipo_contenido_id' => TipoContenido::where('slug', 'noticia')->first()->id,
    'titulo' => 'Inauguración del nuevo parque',
    'slug' => 'inauguracion-nuevo-parque',
    'resumen' => 'El alcalde inauguró...',
    'cuerpo' => 'En ceremonia especial...',
    'imagen_destacada' => 'storage/noticias/2026/parque.jpg',
    'es_destacado' => true,
    'estado' => 'publicado',
    'publicado_en' => now(),
    'usuario_id' => auth()->id(),
]);
```

## 📚 Tipos de Contenido Disponibles

| Slug | Nombre | Categoría | Campos Específicos |
|------|--------|-----------|-------------------|
| `post` | Post | Editorial | - |
| `blog` | Blog | Editorial | - |
| `noticia` | Noticia | Editorial | - |
| `pagina` | Página | Editorial | - |
| `evento` | Evento | Editorial | fecha_inicio, fecha_fin, ubicacion |
| `anuncio` | Anuncio | Editorial | - |
| `decreto` | Decreto | Oficial | numero, fecha_emision, ruta_archivo |
| `gaceta` | Gaceta | Oficial | numero, fecha_emision, ruta_archivo |
| `circular` | Circular | Oficial | numero, fecha_emision, ruta_archivo |
| `acta` | Acta | Oficial | numero, tipo_reunion, asistentes, ruta_archivo |
| `contrato` | Contrato | Transparencia | numero, monto, nombre_contratista, url_secop |
| `resolucion` | Resolución | Oficial | numero, fecha_emision, ruta_archivo |
| `acuerdo` | Acuerdo | Oficial | numero, fecha_emision, ruta_archivo |
| `publicacion` | Publicación | General | - |
| `documento` | Documento | General | ruta_archivo |

## 🔗 Relaciones Polimórficas

Todos los tipos de contenido comparten las mismas relaciones polimórficas:

### Medios (Archivos Multimedia)

```php
// Adjuntar imagen a un decreto
$decreto->medios()->create([
    'tipo_medio' => 'imagen',
    'ruta' => 'storage/decretos/2026/foto-firma.jpg',
    'nombre_archivo' => 'foto-firma.jpg',
]);

// Adjuntar PDF a una noticia
$noticia->medios()->create([
    'tipo_medio' => 'documento',
    'ruta' => 'storage/noticias/2026/informe.pdf',
    'nombre_archivo' => 'informe-completo.pdf',
]);
```

### Categorías

```php
// Asignar categorías a un decreto
$decreto->categorias()->sync([1, 2, 3]); // IDs de categorías

// Obtener decretos de una categoría
$categoria = Categoria::find(1);
$decretos = $categoria->contenidos()
    ->decretos()
    ->publicados()
    ->get();
```

### Etiquetas

```php
// Asignar etiquetas
$noticia->etiquetas()->attach(['urgente', 'destacado']);

// Buscar por etiqueta
$contenidos = Contenido::whereHas('etiquetas', function($q) {
    $q->where('slug', 'urgente');
})->get();
```

## 🎨 Factory - Generar Datos de Prueba

```php
use App\Models\Contenido;
use App\Models\TipoContenido;

// Generar 50 decretos
Contenido::factory()
    ->count(50)
    ->create([
        'tipo_contenido_id' => TipoContenido::where('slug', 'decreto')->first()->id,
    ]);

// Generar 100 noticias publicadas
Contenido::factory()
    ->publicado()
    ->count(100)
    ->create([
        'tipo_contenido_id' => TipoContenido::where('slug', 'noticia')->first()->id,
    ]);
```

## 🔐 Permisos Centralizados

Con una sola tabla, los permisos son más simples:

```php
// Un solo permiso por acción
'ver-contenidos'
'crear-contenidos'
'editar-contenidos'
'eliminar-contenidos'
'publicar-contenidos'

// En lugar de duplicar por cada tipo:
'ver-decretos', 'crear-decretos', 'editar-decretos'...
'ver-gacetas', 'crear-gacetas', 'editar-gacetas'...
```

## 🔍 Búsqueda Global

```php
// Buscar en TODOS los tipos de contenido
$resultados = Contenido::where('titulo', 'like', "%{$termino}%")
    ->orWhere('cuerpo', 'like', "%{$termino}%")
    ->publicados()
    ->recientes()
    ->paginate(20);

// Filtrar por tipo si es necesario
$resultados = Contenido::decretos()
    ->where('titulo', 'like', "%{$termino}%")
    ->get();
```

## 📈 Escalabilidad

La tabla `contenidos` está optimizada para manejar 1M+ registros:

- ✅ Índices estratégicos por tipo y estado
- ✅ Índices compuestos para queries frecuentes
- ✅ Paginación obligatoria
- ✅ Soft deletes para preservar datos
- ✅ Traits de optimización (OptimizableQuery, Cacheable)

## 🚀 Ventajas del Enfoque Centralizado

1. **Mantenibilidad**: Un solo código base
2. **Consistencia**: Misma estructura para todos
3. **Flexibilidad**: Fácil agregar nuevos tipos
4. **Performance**: Queries optimizadas
5. **Simplicidad**: API unificada
6. **Escalabilidad**: Preparado para millones de registros

## 📝 Conclusión

La tabla `contenidos` es el **corazón del CMS**, manejando todos los tipos de contenido de manera eficiente, escalable y mantenible. El campo `tipo_contenido_id` determina el comportamiento, mientras que los campos específicos se usan solo cuando el tipo lo requiere.
