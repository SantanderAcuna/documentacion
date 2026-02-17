# 🎬 Guía: Múltiples Archivos Multimedia por Contenido

## 📋 Concepto

Cada contenido (decreto, noticia, blog, etc.) puede tener **MÚLTIPLES** archivos multimedia de cualquier tipo:
- 📄 Documentos (PDF, DOCX, XLSX)
- 🖼️ Imágenes (JPG, PNG, GIF, SVG)
- 🎥 Videos (MP4, AVI, MOV)
- 🎵 Audio (MP3, WAV, OGG)

## 🔗 Relación Polimórfica

La tabla `medios` usa relación polimórfica `morphMany` con `contenidos`:

```php
// En el modelo Contenido
public function medios(): MorphMany
{
    return $this->morphMany(Medio::class, 'mediable');
}
```

Esto permite que un contenido tenga **múltiples** archivos relacionados.

## 📝 Ejemplos Prácticos

### Ejemplo 1: Decreto con Múltiples Archivos

```php
use App\Models\Contenido;
use App\Models\TipoContenido;

// Crear el decreto
$decreto = Contenido::create([
    'tipo_contenido_id' => TipoContenido::where('slug', 'decreto')->first()->id,
    'titulo' => 'Decreto 001 de 2026 - Reforma Tributaria',
    'slug' => 'decreto-001-2026-reforma-tributaria',
    'numero' => 'DECRETO-001-2026',
    'fecha_emision' => '2026-01-15',
    'cuerpo' => 'Por medio del cual se establece...',
    'estado' => 'publicado',
    'usuario_id' => auth()->id(),
    'dependencia_id' => 1,
]);

// Archivo 1: PDF del decreto (principal)
$decreto->medios()->create([
    'tipo_medio' => 'documento',
    'nombre' => 'Decreto 001-2026 PDF Oficial',
    'nombre_archivo' => 'decreto-001-2026.pdf',
    'tipo_mime' => 'application/pdf',
    'disco' => 'public',
    'ruta' => 'decretos/2026/decreto-001-2026.pdf',
    'tamano' => 524288, // 512KB
    'texto_alternativo' => 'Decreto 001 de 2026 sobre reforma tributaria',
    'coleccion' => 'documentos_principales',
    'orden' => 1,
    'es_destacado' => true,
]);

// Archivo 2: Versión en Word para edición
$decreto->medios()->create([
    'tipo_medio' => 'documento',
    'nombre' => 'Decreto 001-2026 DOCX',
    'nombre_archivo' => 'decreto-001-2026.docx',
    'tipo_mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'disco' => 'public',
    'ruta' => 'decretos/2026/decreto-001-2026.docx',
    'tamano' => 102400, // 100KB
    'coleccion' => 'documentos_adjuntos',
    'orden' => 2,
]);

// Archivo 3: Imagen de la firma del alcalde
$decreto->medios()->create([
    'tipo_medio' => 'imagen',
    'nombre' => 'Firma del Alcalde',
    'nombre_archivo' => 'firma-alcalde.jpg',
    'tipo_mime' => 'image/jpeg',
    'disco' => 'public',
    'ruta' => 'decretos/2026/firma-alcalde.jpg',
    'tamano' => 51200, // 50KB
    'ancho' => 800,
    'alto' => 600,
    'texto_alternativo' => 'Firma del alcalde en el decreto 001 de 2026',
    'coleccion' => 'imagenes',
    'orden' => 3,
]);

// Archivo 4: Video explicativo del decreto
$decreto->medios()->create([
    'tipo_medio' => 'video',
    'nombre' => 'Explicación del Decreto 001-2026',
    'nombre_archivo' => 'explicacion-decreto.mp4',
    'tipo_mime' => 'video/mp4',
    'disco' => 'public',
    'ruta' => 'decretos/2026/explicacion-decreto.mp4',
    'tamano' => 52428800, // 50MB
    'ancho' => 1920,
    'alto' => 1080,
    'duracion' => 180, // 3 minutos en segundos
    'texto_alternativo' => 'Video explicativo del decreto de reforma tributaria',
    'ruta_miniatura' => 'decretos/2026/explicacion-decreto-thumb.jpg',
    'coleccion' => 'videos',
    'orden' => 4,
]);

// Archivo 5: Audio de lectura del decreto
$decreto->medios()->create([
    'tipo_medio' => 'audio',
    'nombre' => 'Lectura del Decreto 001-2026',
    'nombre_archivo' => 'lectura-decreto.mp3',
    'tipo_mime' => 'audio/mpeg',
    'disco' => 'public',
    'ruta' => 'decretos/2026/lectura-decreto.mp3',
    'tamano' => 5242880, // 5MB
    'duracion' => 300, // 5 minutos en segundos
    'texto_alternativo' => 'Audio de lectura del decreto 001 de 2026',
    'coleccion' => 'audios',
    'orden' => 5,
]);

// Archivo 6: Anexo - Tabla de tarifas (Excel)
$decreto->medios()->create([
    'tipo_medio' => 'documento',
    'nombre' => 'Anexo - Tabla de Tarifas',
    'nombre_archivo' => 'anexo-tarifas.xlsx',
    'tipo_mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'disco' => 'public',
    'ruta' => 'decretos/2026/anexo-tarifas.xlsx',
    'tamano' => 204800, // 200KB
    'descripcion' => 'Tabla de tarifas tributarias aplicables',
    'coleccion' => 'anexos',
    'orden' => 6,
]);
```

### Ejemplo 2: Noticia con Galería de Imágenes

```php
// Crear la noticia
$noticia = Contenido::create([
    'tipo_contenido_id' => TipoContenido::where('slug', 'noticia')->first()->id,
    'titulo' => 'Inauguración del Nuevo Parque Tayrona',
    'slug' => 'inauguracion-nuevo-parque-tayrona',
    'resumen' => 'El alcalde inauguró el renovado Parque Tayrona...',
    'cuerpo' => 'En ceremonia oficial, el alcalde...',
    'estado' => 'publicado',
    'publicado_en' => now(),
    'es_destacado' => true,
    'usuario_id' => auth()->id(),
]);

// Imagen destacada principal
$noticia->medios()->create([
    'tipo_medio' => 'imagen',
    'nombre' => 'Imagen Principal - Inauguración',
    'nombre_archivo' => 'inauguracion-principal.jpg',
    'tipo_mime' => 'image/jpeg',
    'ruta' => 'noticias/2026/inauguracion-principal.jpg',
    'tamano' => 512000,
    'ancho' => 1920,
    'alto' => 1080,
    'texto_alternativo' => 'Alcalde cortando la cinta en la inauguración del parque',
    'es_destacado' => true,
    'coleccion' => 'principal',
    'orden' => 1,
]);

// Galería de imágenes
for ($i = 2; $i <= 10; $i++) {
    $noticia->medios()->create([
        'tipo_medio' => 'imagen',
        'nombre' => "Galería Inauguración - Foto {$i}",
        'nombre_archivo' => "galeria-foto-{$i}.jpg",
        'tipo_mime' => 'image/jpeg',
        'ruta' => "noticias/2026/galeria-foto-{$i}.jpg",
        'tamano' => rand(200000, 800000),
        'ancho' => 1920,
        'alto' => 1080,
        'texto_alternativo' => "Imagen {$i} de la inauguración del parque",
        'coleccion' => 'galeria',
        'orden' => $i,
    ]);
}

// Video del evento
$noticia->medios()->create([
    'tipo_medio' => 'video',
    'nombre' => 'Video Completo - Inauguración',
    'nombre_archivo' => 'inauguracion-completa.mp4',
    'tipo_mime' => 'video/mp4',
    'ruta' => 'noticias/2026/inauguracion-completa.mp4',
    'tamano' => 104857600, // 100MB
    'ancho' => 1920,
    'alto' => 1080,
    'duracion' => 600, // 10 minutos
    'texto_alternativo' => 'Video completo de la inauguración del parque',
    'ruta_miniatura' => 'noticias/2026/inauguracion-thumb.jpg',
    'coleccion' => 'videos',
    'orden' => 11,
]);

// Documento del discurso
$noticia->medios()->create([
    'tipo_medio' => 'documento',
    'nombre' => 'Discurso del Alcalde',
    'nombre_archivo' => 'discurso-alcalde.pdf',
    'tipo_mime' => 'application/pdf',
    'ruta' => 'noticias/2026/discurso-alcalde.pdf',
    'tamano' => 102400,
    'texto_alternativo' => 'Texto completo del discurso del alcalde',
    'coleccion' => 'documentos',
    'orden' => 12,
]);
```

### Ejemplo 3: Blog con Recursos Variados

```php
$blog = Contenido::create([
    'tipo_contenido_id' => TipoContenido::where('slug', 'blog')->first()->id,
    'titulo' => 'Guía Completa de Trámites Municipales 2026',
    'slug' => 'guia-tramites-municipales-2026',
    'cuerpo' => 'En esta guía encontrarás...',
    'estado' => 'publicado',
    'usuario_id' => auth()->id(),
]);

// Infografía principal
$blog->medios()->create([
    'tipo_medio' => 'imagen',
    'nombre' => 'Infografía Trámites',
    'nombre_archivo' => 'infografia-tramites.png',
    'tipo_mime' => 'image/png',
    'ruta' => 'blogs/2026/infografia-tramites.png',
    'tamano' => 819200,
    'ancho' => 1200,
    'alto' => 3000,
    'texto_alternativo' => 'Infografía con los pasos para realizar trámites',
    'coleccion' => 'infografias',
]);

// PDF descargable
$blog->medios()->create([
    'tipo_medio' => 'documento',
    'nombre' => 'Guía Descargable PDF',
    'nombre_archivo' => 'guia-tramites-2026.pdf',
    'tipo_mime' => 'application/pdf',
    'ruta' => 'blogs/2026/guia-tramites-2026.pdf',
    'tamano' => 2097152, // 2MB
    'coleccion' => 'descargas',
]);

// Formularios de ejemplo
$blog->medios()->create([
    'tipo_medio' => 'documento',
    'nombre' => 'Formulario Tipo A',
    'nombre_archivo' => 'formulario-tipo-a.docx',
    'tipo_mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'ruta' => 'blogs/2026/formulario-tipo-a.docx',
    'tamano' => 51200,
    'coleccion' => 'formularios',
]);
```

## 🔍 Consultar Medios de un Contenido

### Obtener todos los medios

```php
$contenido = Contenido::find(1);
$medios = $contenido->medios;

// Ver todos
foreach ($medios as $medio) {
    echo $medio->nombre . " - " . $medio->tipo_medio . "\n";
}
```

### Filtrar por tipo de medio

```php
// Solo imágenes
$imagenes = $contenido->medios()->tipo('imagen')->get();

// Solo documentos PDF
$pdfs = $contenido->medios()
    ->where('tipo_medio', 'documento')
    ->where('tipo_mime', 'application/pdf')
    ->get();

// Solo videos
$videos = $contenido->medios()->tipo('video')->get();

// Solo archivos destacados
$destacados = $contenido->medios()->destacados()->get();
```

### Filtrar por colección

```php
// Galería de imágenes
$galeria = $contenido->medios()->coleccion('galeria')->get();

// Documentos principales
$principales = $contenido->medios()->coleccion('documentos_principales')->get();

// Anexos
$anexos = $contenido->medios()->coleccion('anexos')->get();
```

### Ordenar medios

```php
// Por orden definido
$mediosOrdenados = $contenido->medios()->orderBy('orden')->get();

// Los más recientes primero
$recientes = $contenido->medios()->latest()->get();
```

## 🎨 Mostrar en la Vista

### Blade Template - Mostrar Medios

```blade
{{-- Mostrar imagen destacada --}}
@php
    $imagenDestacada = $contenido->medios()->tipo('imagen')->destacados()->first();
@endphp

@if($imagenDestacada)
    <img src="{{ Storage::url($imagenDestacada->ruta) }}" 
         alt="{{ $imagenDestacada->texto_alternativo }}"
         class="img-fluid">
@endif

{{-- Mostrar galería de imágenes --}}
<div class="row">
    @foreach($contenido->medios()->tipo('imagen')->coleccion('galeria')->get() as $imagen)
        <div class="col-md-4 mb-3">
            <img src="{{ Storage::url($imagen->ruta) }}" 
                 alt="{{ $imagen->texto_alternativo }}"
                 class="img-thumbnail">
        </div>
    @endforeach
</div>

{{-- Mostrar videos --}}
@foreach($contenido->medios()->tipo('video')->get() as $video)
    <video controls class="w-100">
        <source src="{{ Storage::url($video->ruta) }}" type="{{ $video->tipo_mime }}">
        {{ $video->texto_alternativo }}
    </video>
@endforeach

{{-- Mostrar documentos descargables --}}
<h3>Documentos Adjuntos</h3>
<ul>
    @foreach($contenido->medios()->tipo('documento')->get() as $documento)
        <li>
            <a href="{{ Storage::url($documento->ruta) }}" 
               download="{{ $documento->nombre_archivo }}">
                📄 {{ $documento->nombre }} ({{ number_format($documento->tamano / 1024, 2) }} KB)
            </a>
        </li>
    @endforeach
</ul>
```

## 📊 Estadísticas

```php
// Contar medios por tipo
$totalImagenes = $contenido->medios()->tipo('imagen')->count();
$totalVideos = $contenido->medios()->tipo('video')->count();
$totalDocumentos = $contenido->medios()->tipo('documento')->count();
$totalAudios = $contenido->medios()->tipo('audio')->count();

// Tamaño total de archivos
$tamañoTotal = $contenido->medios()->sum('tamano');
$tamañoTotalMB = round($tamañoTotal / 1024 / 1024, 2);

echo "Este contenido tiene:\n";
echo "- {$totalImagenes} imágenes\n";
echo "- {$totalVideos} videos\n";
echo "- {$totalDocumentos} documentos\n";
echo "- {$totalAudios} audios\n";
echo "Tamaño total: {$tamañoTotalMB} MB\n";
```

## ✅ Ventajas

1. **Flexibilidad Total**: Un contenido puede tener todos los archivos que necesite
2. **Organización**: Colecciones para agrupar (galería, anexos, principales, etc.)
3. **Accesibilidad**: Campo `texto_alternativo` para WCAG 2.1 AA
4. **Orden**: Campo `orden` para secuencia personalizada
5. **Destacados**: Marcar archivos principales con `es_destacado`
6. **Metadatos Ricos**: Ancho, alto, duración, conversiones, etc.

## 🔐 Seguridad

```php
// Verificar permisos antes de mostrar archivos
if (auth()->user()->can('ver-contenidos')) {
    $medios = $contenido->medios;
}

// Generar URLs temporales para archivos privados
$urlTemporal = Storage::temporaryUrl(
    $medio->ruta,
    now()->addMinutes(30)
);
```

Esta arquitectura permite que **cualquier tipo de contenido** (decreto, noticia, blog, etc.) tenga **múltiples archivos multimedia** de forma organizada y eficiente. 🎉
