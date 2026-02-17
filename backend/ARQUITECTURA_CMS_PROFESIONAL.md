# 🏗️ Arquitectura CMS Profesional - Estilo Drupal

## 📋 Tabla de Contenidos
1. [Introducción](#introducción)
2. [Componentes Principales](#componentes-principales)
3. [Sistema de Nodos (Node System)](#sistema-de-nodos)
4. [Sistema de Campos (Field API)](#sistema-de-campos)
5. [Sistema de Revisiones](#sistema-de-revisiones)
6. [URLs Amigables](#urls-amigables)
7. [Sistema de Workflow](#sistema-de-workflow)
8. [Sistema de Comentarios](#sistema-de-comentarios)
9. [Ejemplos de Uso](#ejemplos-de-uso)

---

## Introducción

Esta arquitectura implementa un **CMS profesional de nivel empresarial** inspirado en **Drupal**, **WordPress** y **Joomla**, con las siguientes características:

✅ **Tabla `contenidos` 100% centralizada** - Todo el contenido en una sola tabla  
✅ **Tipos de contenido configurables** - Agregar tipos sin tocar código  
✅ **Campos personalizables (Field API)** - Campos dinámicos tipo Drupal  
✅ **Sistema de revisiones** - Control completo de versiones  
✅ **URLs amigables** - SEO optimizado con aliases  
✅ **Workflow configurable** - Estados y transiciones personalizables  
✅ **Sistema polimórfico de medios** - Múltiples archivos por contenido  
✅ **Taxonomía jerárquica** - Categorías y etiquetas polimórficas  
✅ **Sistema de comentarios** - Threading y moderación  

---

## Componentes Principales

### 🎯 Core Components

```
CORE NODE SYSTEM (Sistema de Nodos):
├─ contenidos (nodes - entidad central)
├─ tipos_contenido (content types configurables)
├─ revisiones_contenido (version control completo)
├─ definiciones_campos (field definitions - Field API)
└─ valores_campos (field values - EAV pattern)

TAXONOMY (Taxonomía):
├─ categorias (vocabularies jerárquicas)
├─ etiquetas (flat taxonomy - tags)
├─ categorizables (pivot polimórfica)
└─ etiquetables (pivot polimórfica)

MEDIA LIBRARY (Librería de Medios):
└─ medios (polimórfico - todos los archivos multimedia)

URL & SEO:
├─ aliases_url (friendly URLs)
└─ redirects (301/302 redirects automáticos)

WORKFLOW:
├─ estados_workflow (workflow states)
└─ transiciones_workflow (state transitions)

INTERACTION (Interacción):
└─ comentarios (comments - threading polimórfico)
```

---

## Sistema de Nodos

### Tabla `contenidos` (Central)

La tabla `contenidos` es el **corazón del CMS**. Maneja TODOS los tipos de contenido:

**Tipos Editoriales:**
- Posts, Blogs, Noticias, Páginas, Eventos, Anuncios

**Documentos Oficiales:**
- Decretos, Gacetas, Circulares, Actas, Resoluciones, Acuerdos

**Transparencia:**
- Contratos SECOP, Presupuestos, Datos Abiertos

**Y cualquier tipo futuro...**

### Campos Universales

```sql
-- Campos que TODO contenido tiene
tipo_contenido_id (FK)
dependencia_id (FK - opcional)
usuario_id (FK - autor)
titulo, slug, resumen, cuerpo
imagen_destacada
estado (borrador, publicado, archivado, revision)
publicado_en, conteo_vistas, es_destacado
```

### Campos Específicos por Tipo

```sql
-- Para documentos oficiales (decretos, gacetas, etc.)
numero, fecha_emision, fecha_publicacion
ruta_archivo, nombre_archivo

-- Para eventos
fecha_inicio, fecha_fin, ubicacion

-- Para actas
tipo_reunion, asistentes (JSON)

-- Para contratos
nombre_contratista, tipo_contrato, monto, url_secop

-- Metadatos flexibles (JSON)
metadatos (para campos adicionales específicos)
```

### Uso de Scopes

```php
// Decretos publicados
Contenido::decretos()->publicados()->get();

// Noticias destacadas
Contenido::noticias()->destacados()->recientes()->get();

// Eventos futuros
Contenido::eventos()->where('fecha_inicio', '>=', now())->get();

// Contratos por monto
Contenido::contratos()->where('monto', '>', 100000000)->get();

// Buscar por número
Contenido::porNumero('DECRETO-001-2026')->first();
```

---

## Sistema de Campos (Field API)

### Concepto

Inspirado en **Drupal Field API**, permite agregar **campos personalizados** a cualquier tipo de contenido **sin modificar la estructura de la base de datos**.

### Arquitectura EAV (Entity-Attribute-Value)

**Tabla `definiciones_campos`:**
- Define qué campos existen
- Tipo de campo (texto, número, fecha, archivo, etc.)
- Validaciones y configuraciones
- A qué tipos de contenido se aplica

**Tabla `valores_campos`:**
- Almacena los valores reales
- Relación polimórfica con cualquier entidad
- Soporte para campos múltiples (delta)

### Tipos de Campo Soportados

```php
'texto'              // Input text corto
'texto_largo'        // Textarea
'texto_formato'      // HTML/Markdown editor
'numero_entero'      // Integer
'numero_decimal'     // Decimal
'booleano'           // Checkbox
'fecha'              // Date picker
'fecha_hora'         // DateTime picker
'email'              // Email
'url'                // URL
'telefono'           // Teléfono
'archivo'            // File upload (usa medios)
'seleccion'          // Select/Radio
'seleccion_multiple' // Checkboxes
'referencia'         // Referencia a otra entidad
'json'               // JSON data
```

### Ejemplo: Agregar Campo "Presupuesto Aprobado" a Contratos

```php
// 1. Crear definición del campo
$campo = DefinicionCampo::create([
    'nombre' => 'presupuesto_aprobado',
    'etiqueta' => 'Presupuesto Aprobado',
    'tipo_campo' => 'numero_decimal',
    'configuracion' => [
        'min' => 0,
        'max' => 999999999999.99,
        'decimales' => 2
    ],
    'es_requerido' => true,
    'tipos_contenido_ids' => [9], // ID del tipo "contrato"
]);

// 2. Guardar valor para un contrato
$contrato = Contenido::contratos()->first();

ValorCampo::create([
    'definicion_campo_id' => $campo->id,
    'entidad_tipo' => 'contenido',
    'entidad_id' => $contrato->id,
    'valor_decimal' => 50000000.00
]);

// 3. Recuperar valor
$valor = ValorCampo::where('definicion_campo_id', $campo->id)
    ->where('entidad_tipo', 'contenido')
    ->where('entidad_id', $contrato->id)
    ->first();

echo $valor->valor_decimal; // 50000000.00
```

### Campos Múltiples (Repetibles)

```php
// Campo "Documentos Anexos" (múltiple)
$campo = DefinicionCampo::create([
    'nombre' => 'documentos_anexos',
    'tipo_campo' => 'archivo',
    'es_multiple' => true,
    'cardinalidad' => 5, // Máximo 5 archivos
]);

// Agregar múltiples valores
foreach ($archivos as $index => $archivo) {
    ValorCampo::create([
        'definicion_campo_id' => $campo->id,
        'entidad_tipo' => 'contenido',
        'entidad_id' => $contenido->id,
        'delta' => $index, // 0, 1, 2, 3, 4
        'medio_id' => $archivo->id
    ]);
}
```

---

## Sistema de Revisiones

### Concepto

**Control de versiones completo** similar a Git:
- Guarda snapshot completo de cada versión
- Mensaje de revisión (como commit message)
- Comparación entre versiones
- Restauración de versiones anteriores

### Tabla `revisiones_contenido`

Almacena el **estado completo** del contenido en cada revisión:

```sql
- contenido_id (FK al contenido original)
- numero_revision (1, 2, 3, ...)
- todos_los_campos (snapshot completo)
- mensaje_revision (descripción del cambio)
- es_revision_actual (true/false)
- creado_por, creado_en
```

### Ejemplo de Uso

```php
// Crear contenido (revisión 1 automática)
$decreto = Contenido::create([
    'tipo_contenido_id' => 1,
    'titulo' => 'Decreto de Reforma',
    'estado' => 'borrador',
    // ...
]);

// Editar y crear revisión 2
$decreto->update([
    'titulo' => 'Decreto de Reforma Tributaria',
    'estado' => 'revision',
]);

// Crear revisión con mensaje
RevisionContenido::crearRevision($decreto, 'Agregado título completo y enviado a revisión');

// Publicar (revisión 3)
$decreto->update(['estado' => 'publicado']);
RevisionContenido::crearRevision($decreto, 'Aprobado y publicado');

// Ver historial
$revisiones = RevisionContenido::where('contenido_id', $decreto->id)
    ->orderBy('numero_revision')
    ->get();

foreach ($revisiones as $rev) {
    echo "#{$rev->numero_revision}: {$rev->mensaje_revision} - {$rev->creado_en}\n";
}

// Restaurar versión anterior
RevisionContenido::restaurar($decreto, 2);
```

---

## URLs Amigables

### Tabla `aliases_url`

Permite URLs personalizadas tipo WordPress:

```
Sistema:  /contenidos/123
Alias:    /noticias/2026/02/reforma-tributaria-aprobada

Sistema:  /contenidos/456
Alias:    /decretos/decreto-001-2026-horario-laboral
```

### Auto-generación de Aliases

```php
// Configurar patrón por tipo de contenido
$tipoContenido->update([
    'patron_url' => '/[tipo]/[año]/[titulo]'
]);

// Al crear contenido, se genera automáticamente
$noticia = Contenido::create([
    'tipo_contenido_id' => 5, // Noticia
    'titulo' => 'Reforma Tributaria Aprobada',
    // ...
]);

// Alias generado automáticamente:
// /noticias/2026/reforma-tributaria-aprobada

// Personalizar manualmente
AliasUrl::create([
    'entidad_tipo' => 'contenido',
    'entidad_id' => $noticia->id,
    'ruta_sistema' => '/contenidos/123',
    'alias' => '/noticias/reforma-tributaria',
    'auto_generado' => false
]);
```

### Sistema de Redirects

```php
// Al cambiar URL, crear redirect automático
$aliasAntiguo = '/noticias/reforma';
$aliasNuevo = '/noticias/2026/reforma-tributaria';

Redirect::create([
    'ruta_origen' => $aliasAntiguo,
    'ruta_destino' => $aliasNuevo,
    'tipo_redirect' => '301', // Permanent
]);
```

---

## Sistema de Workflow

### Estados Configurables

```php
EstadoWorkflow::create([
    'nombre' => 'borrador',
    'etiqueta' => 'Borrador',
    'color' => '#6c757d',
    'icono' => 'fa-pencil',
    'es_estado_inicial' => true,
]);

EstadoWorkflow::create([
    'nombre' => 'revision',
    'etiqueta' => 'En Revisión',
    'color' => '#ffc107',
    'icono' => 'fa-eye',
]);

EstadoWorkflow::create([
    'nombre' => 'publicado',
    'etiqueta' => 'Publicado',
    'color' => '#28a745',
    'icono' => 'fa-check-circle',
    'es_estado_publicado' => true,
]);
```

### Transiciones

```php
// Definir transición: Borrador → En Revisión
TransicionWorkflow::create([
    'estado_origen_id' => 1, // borrador
    'estado_destino_id' => 2, // revision
    'nombre' => 'enviar_a_revision',
    'etiqueta' => 'Enviar a Revisión',
    'permisos_requeridos' => ['editar contenidos'],
    'roles_permitidos' => ['autor', 'editor'],
    'notificar_roles' => ['editor', 'administrador'],
]);

// Definir transición: En Revisión → Publicado
TransicionWorkflow::create([
    'estado_origen_id' => 2,
    'estado_destino_id' => 3,
    'nombre' => 'publicar',
    'etiqueta' => 'Publicar',
    'permisos_requeridos' => ['publicar contenidos'],
    'roles_permitidos' => ['editor', 'administrador'],
    'notificar_autor' => true,
]);
```

### Ejecutar Transición

```php
// Autor envía a revisión
$contenido->ejecutarTransicion('enviar_a_revision', auth()->user());

// Editor publica
$contenido->ejecutarTransicion('publicar', auth()->user());
```

---

## Sistema de Comentarios

### Threading (Respuestas Anidadas)

```php
// Comentario raíz
$comentario1 = Comentario::create([
    'comentable_type' => 'contenido',
    'comentable_id' => $noticia->id,
    'usuario_id' => auth()->id(),
    'cuerpo' => 'Excelente noticia!',
    'nivel' => 0,
]);

// Respuesta nivel 1
$comentario2 = Comentario::create([
    'comentable_type' => 'contenido',
    'comentable_id' => $noticia->id,
    'padre_id' => $comentario1->id,
    'usuario_id' => auth()->id(),
    'cuerpo' => 'Estoy de acuerdo',
    'nivel' => 1,
    'hilo' => $comentario1->id,
]);

// Respuesta nivel 2
$comentario3 = Comentario::create([
    'padre_id' => $comentario2->id,
    'cuerpo' => 'Yo también',
    'nivel' => 2,
    'hilo' => "{$comentario1->id}/{$comentario2->id}",
]);
```

### Moderación

```php
// Aprobar comentario
$comentario->update([
    'estado' => 'aprobado',
    'moderado_por' => auth()->id(),
    'moderado_en' => now()
]);

// Marcar como spam
$comentario->update(['estado' => 'spam']);

// Obtener comentarios aprobados
$comentarios = Comentario::where('comentable_id', $noticia->id)
    ->where('estado', 'aprobado')
    ->orderBy('created_at', 'desc')
    ->get();
```

---

## Ejemplos de Uso

### Ejemplo 1: Crear Decreto con Múltiples Archivos

```php
// 1. Crear el decreto
$decreto = Contenido::create([
    'tipo_contenido_id' => TipoContenido::where('slug', 'decreto')->first()->id,
    'numero' => 'DECRETO-001-2026',
    'titulo' => 'Decreto de Reforma Tributaria',
    'fecha_emision' => '2026-02-15',
    'estado' => 'publicado',
    'usuario_id' => auth()->id(),
]);

// 2. Adjuntar archivos multimedia
$decreto->medios()->create([
    'nombre' => 'PDF Principal',
    'tipo_medio' => 'documento',
    'nombre_archivo' => 'decreto-001.pdf',
    'ruta' => 'storage/decretos/2026/decreto-001.pdf',
]);

$decreto->medios()->create([
    'nombre' => 'Firma Digital',
    'tipo_medio' => 'imagen',
    'nombre_archivo' => 'firma.jpg',
    'ruta' => 'storage/decretos/2026/firma.jpg',
]);

$decreto->medios()->create([
    'nombre' => 'Video Explicativo',
    'tipo_medio' => 'video',
    'nombre_archivo' => 'explicacion.mp4',
    'ruta' => 'storage/decretos/2026/video.mp4',
]);

// 3. Agregar categorías
$decreto->categorias()->attach([1, 2, 3]); // Normatividad, Decretos, Tributaria

// 4. Agregar etiquetas
$decreto->etiquetas()->attach([1, 5]); // Urgente, Vigente

// 5. Generar URL amigable
AliasUrl::create([
    'entidad_type' => 'contenido',
    'entidad_id' => $decreto->id,
    'ruta_sistema' => "/contenidos/{$decreto->id}",
    'alias' => '/decretos/2026/reforma-tributaria',
]);
```

### Ejemplo 2: Workflow Completo

```php
// Autor crea borrador
$noticia = Contenido::create([
    'tipo_contenido_id' => 5, // Noticia
    'titulo' => 'Nueva Inversión en Infraestructura',
    'estado' => 'borrador',
    'usuario_id' => $autor->id,
]);

// Autor envía a revisión
$noticia->ejecutarTransicion('enviar_a_revision', $autor);
// Estado: borrador → revision
// Notifica a editores

// Editor revisa y solicita cambios
RevisionContenido::crearRevision($noticia, 'Solicitar mejoras en redacción');

// Autor hace cambios
$noticia->update(['cuerpo' => 'Nuevo texto mejorado...']);
RevisionContenido::crearRevision($noticia, 'Aplicados cambios solicitados');

// Editor aprueba y publica
$noticia->ejecutarTransicion('publicar', $editor);
// Estado: revision → publicado
// Notifica al autor
```

### Ejemplo 3: Campos Personalizados

```php
// Agregar campo "Presupuesto Estimado" a tipo "Evento"
$campo = DefinicionCampo::create([
    'nombre' => 'presupuesto_estimado',
    'etiqueta' => 'Presupuesto Estimado',
    'tipo_campo' => 'numero_decimal',
    'es_requerido' => false,
    'tipos_contenido_ids' => [5], // Eventos
]);

// Crear evento con valor
$evento = Contenido::create([
    'tipo_contenido_id' => 5,
    'titulo' => 'Festival de Verano 2026',
    'fecha_inicio' => '2026-07-01 10:00:00',
    'ubicacion' => 'Plaza Central',
]);

// Guardar valor del campo personalizado
ValorCampo::create([
    'definicion_campo_id' => $campo->id,
    'entidad_tipo' => 'contenido',
    'entidad_id' => $evento->id,
    'valor_decimal' => 50000000.00
]);

// Recuperar en vista
$presupuesto = ValorCampo::obtenerValor($evento, 'presupuesto_estimado');
echo "Presupuesto: $" . number_format($presupuesto, 2);
```

---

## Ventajas de Esta Arquitectura

### ✅ Profesional
- Igual que Drupal, WordPress, Joomla
- Probado en millones de sitios web

### ✅ Flexible
- Agregar tipos de contenido sin código
- Campos personalizables dinámicamente
- Workflow configurable

### ✅ Escalable
- Ready para millones de registros
- Índices optimizados
- Caché integrado

### ✅ DRY (Don't Repeat Yourself)
- Una sola tabla para todo el contenido
- Zero duplicación de código

### ✅ SEO-Friendly
- URLs personalizables
- Redirects automáticos
- Meta tags por contenido

### ✅ Versionado
- Control completo de cambios
- Restauración de versiones
- Auditoría completa

### ✅ Multimedia
- Múltiples archivos por contenido
- Todos los formatos soportados
- Accesibilidad WCAG 2.1 AA

---

## Comparación con Drupal

| Feature | Drupal | Este CMS |
|---------|--------|----------|
| Node System | ✅ | ✅ |
| Content Types | ✅ | ✅ |
| Field API (EAV) | ✅ | ✅ |
| Revisions | ✅ | ✅ |
| Taxonomy | ✅ | ✅ |
| Media Library | ✅ | ✅ |
| URL Aliases | ✅ | ✅ |
| Workflow | ✅ | ✅ |
| Comments | ✅ | ✅ |
| Multilingual | ✅ | 🔜 |
| View Modes | ✅ | 🔜 |
| Blocks | ✅ | 🔜 |

---

## Conclusión

Este CMS implementa una **arquitectura profesional de nivel empresarial** que:

✅ Centraliza TODO el contenido en una sola tabla  
✅ Soporta múltiples archivos multimedia  
✅ Permite campos personalizados dinámicos  
✅ Incluye control de versiones completo  
✅ Ofrece URLs amigables y SEO  
✅ Tiene workflow configurable  
✅ Es 100% escalable y mantenible  

**Estado:** ✅ Production-ready para CMS gubernamental profesional
