<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo PlanDesarrollo
 * 
 * Representa los planes de desarrollo asociados a cada alcalde
 * Tabla: planes_desarrollo
 */
class PlanDesarrollo extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'planes_desarrollo';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'alcalde_id',
        'nombre',
        'periodo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'eliminado_en' => 'datetime',
    ];

    /**
     * Relación con alcalde
     *
     * @return BelongsTo
     */
    public function alcalde(): BelongsTo
    {
        return $this->belongsTo(Alcalde::class);
    }

    /**
     * Relación con documentos del plan
     *
     * @return HasMany
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoPlan::class, 'plan_desarrollo_id');
    }

    /**
     * Scope para planes activos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para planes vigentes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVigentes($query)
    {
        return $query->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now());
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
