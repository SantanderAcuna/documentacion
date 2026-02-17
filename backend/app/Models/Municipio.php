<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo Municipio
 * 
 * Representa los municipios de Colombia
 * Tabla: municipios
 */
class Municipio extends Model
{
    use OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'municipios';

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
        'departamento_id',
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
     * Relación con departamento
     *
     * @return BelongsTo
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
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

    /**
     * Scope para municipios de un departamento
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $departamentoId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorDepartamento($query, $departamentoId)
    {
        return $query->where('departamento_id', $departamentoId);
    }
}
