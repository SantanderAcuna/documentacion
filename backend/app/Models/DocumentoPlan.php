<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo DocumentoPlan
 * 
 * Representa los documentos asociados a un plan de desarrollo
 * Tabla: documentos_plan
 */
class DocumentoPlan extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'documentos_plan';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'plan_desarrollo_id',
        'titulo',
        'descripcion',
        'ruta_archivo',
        'nombre_archivo',
        'tipo_documento',
        'orden',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'orden' => 'integer',
        'eliminado_en' => 'datetime',
    ];

    /**
     * Relación con plan de desarrollo
     *
     * @return BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(PlanDesarrollo::class, 'plan_desarrollo_id');
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
     * Scope para filtrar por tipo de documento
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tipo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_documento', $tipo);
    }
}
