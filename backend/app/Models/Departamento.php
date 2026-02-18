<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo Departamento
 * 
 * Representa los departamentos de Colombia (división geográfica)
 * Tabla: departamentos
 */
class Departamento extends Model
{
    use OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'departamentos';

    /**
     * Indica si el modelo debe ser marcado con timestamps
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'codigo',
        'nombre',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [];

    /**
     * Relación con municipios
     *
     * @return HasMany
     */
    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class);
    }

    /**
     * Scope para buscar por nombre o código
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $termino
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre', 'like', "%{$termino}%")
            ->orWhere('codigo', 'like', "%{$termino}%");
    }

    /**
     * Scope para ordenar alfabéticamente
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('nombre', 'asc');
    }
}
