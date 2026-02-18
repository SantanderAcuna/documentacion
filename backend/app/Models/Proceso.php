<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo Proceso
 * 
 * Representa los procesos asociados a macro procesos
 * Tabla: procesos
 */
class Proceso extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'procesos';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'macro_proceso_id',
        'nombre',
        'codigo',
        'descripcion',
        'dependencia_responsable_id',
        'orden',
        'esta_activo',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'orden' => 'integer',
        'esta_activo' => 'boolean',
        'eliminado_en' => 'datetime',
    ];

    /**
     * Relación con macro proceso
     *
     * @return BelongsTo
     */
    public function macroProceso(): BelongsTo
    {
        return $this->belongsTo(MacroProceso::class);
    }

    /**
     * Relación con dependencia responsable
     *
     * @return BelongsTo
     */
    public function dependencia(): BelongsTo
    {
        return $this->belongsTo(Dependencia::class, 'dependencia_responsable_id');
    }

    /**
     * Scope para procesos activos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('esta_activo', true);
    }

    /**
     * Scope para ordenar por orden establecido
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden', 'asc');
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
     * Scope para procesos de un macro proceso
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $macroprocesoId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorMacroProceso($query, $macroprocesoId)
    {
        return $query->where('macro_proceso_id', $macroprocesoId);
    }
}
