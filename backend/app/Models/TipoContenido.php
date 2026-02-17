<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo TipoContenido
 * 
 * Representa los tipos de contenido disponibles (posts, blogs, news, pages, events)
 * Tabla: tipos_contenido
 */
class TipoContenido extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'tipos_contenido';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'icono',
        'esta_activo',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'esta_activo' => 'boolean',
        'eliminado_en' => 'datetime',
    ];

    /**
     * Relación con contenidos de este tipo
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contenidos()
    {
        return $this->hasMany(Contenido::class, 'tipo_contenido_id');
    }

    /**
     * Scope para obtener solo tipos activos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('esta_activo', true);
    }

    /**
     * Scope para buscar por slug
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorSlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }
}
