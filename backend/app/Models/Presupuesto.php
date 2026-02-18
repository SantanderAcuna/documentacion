<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo Presupuesto
 * 
 * Entidad para presupuesto municipal
 * Tabla: presupuesto
 */
class Presupuesto extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'presupuesto';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'ano',
        'categoria',
        'subcategoria',
        'descripcion',
        'monto_asignado',
        'monto_ejecutado',
        'monto_disponible',
        'dependencia_id',
        'estado',
        'metadatos',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ano' => 'integer',
        'monto_asignado' => 'decimal:2',
        'monto_ejecutado' => 'decimal:2',
        'monto_disponible' => 'decimal:2',
        'metadatos' => 'array',
        'eliminado_en' => 'datetime',
    ];

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
     * Scope para presupuestos activos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['planeado', 'ejecutando']);
    }

    /**
     * Scope para ordenar por más recientes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecientes($query)
    {
        return $query->orderBy('ano', 'desc');
    }

    /**
     * Scope para filtrar por año
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $ano
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorAno($query, int $ano)
    {
        return $query->where('ano', $ano);
    }
}
