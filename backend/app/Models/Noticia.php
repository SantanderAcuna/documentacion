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
 * Modelo Noticia
 * 
 * Entidad para noticias
 * Tabla: noticias
 */
class Noticia extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'noticias';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'usuario_id',
        'titulo',
        'slug',
        'resumen',
        'contenido',
        'imagen_destacada',
        'estado',
        'publicado_en',
        'conteo_vistas',
        'es_destacado',
        'metadatos',
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
        'metadatos' => 'array',
        'eliminado_en' => 'datetime',
    ];

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
     * Scope para noticias publicadas
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
     * Scope para noticias destacadas
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
     * Incrementar contador de vistas
     *
     * @return void
     */
    public function incrementarVistas(): void
    {
        $this->increment('conteo_vistas');
    }
}
