<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo Categoria
 * 
 * Sistema centralizado de categorías polimórficas y jerárquicas
 * Tabla: categorias
 */
class Categoria extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'categorias';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'padre_id',
        'color',
        'icono',
        'orden',
        'esta_activo',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'esta_activo' => 'boolean',
        'orden' => 'integer',
        'eliminado_en' => 'datetime',
    ];

    /**
     * Relación con categoría padre (jerárquica)
     *
     * @return BelongsTo
     */
    public function padre(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'padre_id');
    }

    /**
     * Relación con categorías hijas (jerárquica)
     *
     * @return HasMany
     */
    public function hijos(): HasMany
    {
        return $this->hasMany(Categoria::class, 'padre_id');
    }

    /**
     * Relación polimórfica con contenidos
     *
     * @return MorphToMany
     */
    public function contenidos(): MorphToMany
    {
        return $this->morphedByMany(Contenido::class, 'categorizable', 'categorizables');
    }

    /**
     * Relación polimórfica con noticias
     *
     * @return MorphToMany
     */
    public function noticias(): MorphToMany
    {
        return $this->morphedByMany(Noticia::class, 'categorizable', 'categorizables');
    }

    /**
     * Relación polimórfica con decretos
     *
     * @return MorphToMany
     */
    public function decretos(): MorphToMany
    {
        return $this->morphedByMany(Decreto::class, 'categorizable', 'categorizables');
    }

    /**
     * Scope para obtener solo categorías activas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivas($query)
    {
        return $query->where('esta_activo', true);
    }

    /**
     * Scope para obtener categorías raíz (sin padre)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRaiz($query)
    {
        return $query->whereNull('padre_id');
    }

    /**
     * Obtener el árbol completo de categorías hijas
     *
     * @return HasMany
     */
    public function hijosRecursivos(): HasMany
    {
        return $this->hijos()->with('hijosRecursivos');
    }
}
