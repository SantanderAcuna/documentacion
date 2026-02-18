<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo Alcalde
 * 
 * Representa los alcaldes del municipio con sus periodos de gestión
 * Tabla: alcaldes
 */
class Alcalde extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'alcaldes';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre_completo',
        'foto',
        'fecha_inicio',
        'fecha_fin',
        'periodo',
        'biografia',
        'logros',
        'es_actual',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'es_actual' => 'boolean',
        'eliminado_en' => 'datetime',
    ];

    /**
     * Relación con planes de desarrollo
     *
     * @return HasMany
     */
    public function planesDesarrollo(): HasMany
    {
        return $this->hasMany(PlanDesarrollo::class);
    }

    /**
     * Scope para obtener el alcalde actual
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActual($query)
    {
        return $query->where('es_actual', true);
    }

    /**
     * Scope para obtener alcaldes por periodo
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $periodo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorPeriodo($query, $periodo)
    {
        return $query->where('periodo', $periodo);
    }

    /**
     * Scope para ordenar por fecha de inicio
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecientes($query)
    {
        return $query->orderBy('fecha_inicio', 'desc');
    }
}
