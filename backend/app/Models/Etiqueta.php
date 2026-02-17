<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo Etiqueta
 * 
 * Sistema centralizado de etiquetas polimórficas
 * Tabla: etiquetas
 */
class Etiqueta extends Model
{
    use OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'etiquetas';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
        'slug',
        'color',
    ];

    /**
     * Relación polimórfica con contenidos
     *
     * @return MorphToMany
     */
    public function contenidos(): MorphToMany
    {
        return $this->morphedByMany(Contenido::class, 'etiquetable', 'etiquetables');
    }

    /**
     * Relación polimórfica con noticias
     *
     * @return MorphToMany
     */
    public function noticias(): MorphToMany
    {
        return $this->morphedByMany(Noticia::class, 'etiquetable', 'etiquetables');
    }

    /**
     * Relación polimórfica con decretos
     *
     * @return MorphToMany
     */
    public function decretos(): MorphToMany
    {
        return $this->morphedByMany(Decreto::class, 'etiquetable', 'etiquetables');
    }

    /**
     * Relación polimórfica con contratos
     *
     * @return MorphToMany
     */
    public function contratos(): MorphToMany
    {
        return $this->morphedByMany(Contrato::class, 'etiquetable', 'etiquetables');
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
