<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo Contenido - TABLA UNIVERSAL CENTRALIZADA
 * 
 * Esta tabla maneja TODOS los tipos de contenido del sistema:
 * - Posts, Blogs, Noticias, Páginas, Eventos (contenido editorial)
 * - Decretos, Gacetas, Circulares, Actas (documentos oficiales)
 * - Contratos, Presupuestos (transparencia)
 * - Y cualquier otro tipo de contenido que se agregue en el futuro
 * 
 * El campo tipo_contenido_id determina qué tipo de contenido es.
 * Los campos específicos se usan solo cuando el tipo lo requiere.
 * 
 * Tabla: contenidos
 */
class Contenido extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'contenidos';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        // Relaciones
        'tipo_contenido_id',
        'dependencia_id',
        'usuario_id',
        
        // Versionado y control
        'version',
        'permite_revisiones',
        'contenido_traduccion_de',
        
        // Multiidioma
        'idioma',
        
        // Autoría flexible
        'autor_nombre',
        'autor_email',
        
        // Campos comunes
        'titulo',
        'meta_titulo',
        'slug',
        'canonical_url',
        'resumen',
        'cuerpo',
        'imagen_destacada',
        'plantilla',
        'formato_visualizacion',
        
        // SEO avanzado
        'meta_descripcion',
        'meta_palabras_clave',
        'robots_index',
        'robots_follow',
        'og_image',
        'og_titulo',
        'og_descripcion',
        
        // Campos para documentos oficiales (decretos, gacetas, etc.)
        'numero',
        'fecha_emision',
        'fecha_publicacion',
        'ruta_archivo',
        'nombre_archivo',
        'requiere_firma_digital',
        'firmado_digitalmente',
        'hash_documento',
        'fecha_vigencia_desde',
        'fecha_vigencia_hasta',
        
        // Campos para eventos
        'fecha_inicio',
        'fecha_fin',
        'ubicacion',
        
        // Campos para actas
        'tipo_reunion',
        'asistentes',
        
        // Campos para contratos
        'nombre_contratista',
        'identificacion_contratista',
        'tipo_contrato',
        'monto',
        'url_secop',
        
        // Estado y publicación
        'estado',
        'publicado_en',
        'fecha_revision',
        'fecha_expiracion',
        'peso',
        'es_destacado',
        'comentarios_habilitados',
        
        // Métricas y engagement
        'conteo_vistas',
        'conteo_comentarios',
        'conteo_likes',
        'conteo_compartidos',
        'puntuacion_promedio',
        
        // Accesibilidad WCAG 2.1
        'nivel_accesibilidad',
        'requiere_transcripcion',
        'transcripcion',
        'descripcion_audio',
        
        // Metadatos y auditoría
        'metadatos',
        'creado_por',
        'actualizado_por',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Versionado
        'version' => 'integer',
        'permite_revisiones' => 'boolean',
        
        // Fechas
        'fecha_emision' => 'date',
        'fecha_publicacion' => 'date',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'publicado_en' => 'datetime',
        'fecha_revision' => 'datetime',
        'fecha_expiracion' => 'datetime',
        'fecha_vigencia_desde' => 'date',
        'fecha_vigencia_hasta' => 'date',
        
        // Booleanos
        'es_destacado' => 'boolean',
        'comentarios_habilitados' => 'boolean',
        'robots_index' => 'boolean',
        'robots_follow' => 'boolean',
        'requiere_firma_digital' => 'boolean',
        'firmado_digitalmente' => 'boolean',
        'requiere_transcripcion' => 'boolean',
        
        // Numéricos
        'conteo_vistas' => 'integer',
        'conteo_comentarios' => 'integer',
        'conteo_likes' => 'integer',
        'conteo_compartidos' => 'integer',
        'peso' => 'integer',
        'monto' => 'decimal:2',
        'puntuacion_promedio' => 'decimal:2',
        
        // Arrays/JSON
        'asistentes' => 'array',
        'metadatos' => 'array',
        
        // Timestamps
        'eliminado_en' => 'datetime',
    ];

    /**
     * Relación con tipo de contenido
     *
     * @return BelongsTo
     */
    public function tipoContenido(): BelongsTo
    {
        return $this->belongsTo(TipoContenido::class);
    }

    /**
     * Relación con dependencia
     *
     * @return BelongsTo
     */
    public function dependencia(): BelongsTo
    {
        return $this->belongsTo(Dependencia::class);
    }

    /**
     * Relación con usuario autor
     *
     * @return BelongsTo
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con usuario que creó el registro
     *
     * @return BelongsTo
     */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Relación con usuario que actualizó el registro
     *
     * @return BelongsTo
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    /**
     * Relación con contenido original (para traducciones)
     *
     * @return BelongsTo
     */
    public function traduccionOriginal(): BelongsTo
    {
        return $this->belongsTo(Contenido::class, 'contenido_traduccion_de');
    }

    /**
     * Relación con traducciones de este contenido
     *
     * @return HasMany
     */
    public function traducciones()
    {
        return $this->hasMany(Contenido::class, 'contenido_traduccion_de');
    }

    /**
     * Relación polimórfica con categorías
     *
     * @return MorphToMany
     */
    public function categorias(): MorphToMany
    {
        return $this->morphToMany(Categoria::class, 'categorizable', 'categorizables');
    }

    /**
     * Relación polimórfica con etiquetas
     *
     * @return MorphToMany
     */
    public function etiquetas(): MorphToMany
    {
        return $this->morphToMany(Etiqueta::class, 'etiquetable', 'etiquetables');
    }

    /**
     * Relación polimórfica con medios
     *
     * @return MorphMany
     */
    public function medios(): MorphMany
    {
        return $this->morphMany(Medio::class, 'mediable');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES POR TIPO DE CONTENIDO
    |--------------------------------------------------------------------------
    | Estos scopes permiten filtrar contenidos por tipo específico
    */

    /**
     * Scope para contenidos publicados
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublicados($query)
    {
        return $query->where('estado', 'publicado')
            ->whereNotNull('publicado_en')
            ->where('publicado_en', '<=', now());
    }

    /**
     * Scope para contenidos destacados
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDestacados($query)
    {
        return $query->where('es_destacado', true);
    }

    /**
     * Scope para ordenar por más recientes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecientes($query)
    {
        return $query->orderBy('publicado_en', 'desc');
    }

    /**
     * Scope para filtrar por tipo de contenido (usando slug)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tipoSlug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorTipo($query, string $tipoSlug)
    {
        return $query->whereHas('tipoContenido', function($q) use ($tipoSlug) {
            $q->where('slug', $tipoSlug);
        });
    }

    /**
     * Scope para decretos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDecretos($query)
    {
        return $query->porTipo('decreto');
    }

    /**
     * Scope para gacetas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGacetas($query)
    {
        return $query->porTipo('gaceta');
    }

    /**
     * Scope para circulares
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCirculares($query)
    {
        return $query->porTipo('circular');
    }

    /**
     * Scope para actas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActas($query)
    {
        return $query->porTipo('acta');
    }

    /**
     * Scope para noticias
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoticias($query)
    {
        return $query->porTipo('noticia');
    }

    /**
     * Scope para blogs
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlogs($query)
    {
        return $query->porTipo('blog');
    }

    /**
     * Scope para posts
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePosts($query)
    {
        return $query->porTipo('post');
    }

    /**
     * Scope para páginas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaginas($query)
    {
        return $query->porTipo('pagina');
    }

    /**
     * Scope para eventos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEventos($query)
    {
        return $query->porTipo('evento');
    }

    /**
     * Scope para contratos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeContratos($query)
    {
        return $query->porTipo('contrato');
    }

    /**
     * Scope para buscar por número (decretos, gacetas, contratos)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $numero
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorNumero($query, string $numero)
    {
        return $query->where('numero', $numero);
    }

    /**
     * Incrementar contador de vistas
     *
     * @return void
     */
    public function incrementarVistas(): void
    {
        $this->increment('conteo_vistas');
    }

    /**
     * Verificar si es un documento oficial (decreto, gaceta, circular, acta)
     *
     * @return bool
     */
    public function esDocumentoOficial(): bool
    {
        $tiposDocumentosOficiales = ['decreto', 'gaceta', 'circular', 'acta'];
        return in_array($this->tipoContenido->slug, $tiposDocumentosOficiales);
    }

    /**
     * Verificar si requiere archivo adjunto
     *
     * @return bool
     */
    public function requiereArchivo(): bool
    {
        return $this->esDocumentoOficial() || $this->tipoContenido->slug === 'contrato';
    }
}
