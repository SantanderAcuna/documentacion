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
 * Modelo Contenido
 * 
 * Contenido editorial principal (posts, blogs, noticias, páginas, eventos)
 * NO genera otras entidades (decretos, gacetas, etc. son independientes)
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
        'tipo_contenido_id',
        'dependencia_id',
        'usuario_id',
        'titulo',
        'slug',
        'resumen',
        'cuerpo',
        'imagen_destacada',
        'estado',
        'publicado_en',
        'conteo_vistas',
        'es_destacado',
        'comentarios_habilitados',
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
        'publicado_en' => 'datetime',
        'conteo_vistas' => 'integer',
        'es_destacado' => 'boolean',
        'comentarios_habilitados' => 'boolean',
        'metadatos' => 'array',
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
     * Scope para filtrar por tipo
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $tipoId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorTipo($query, int $tipoId)
    {
        return $query->where('tipo_contenido_id', $tipoId);
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
}
