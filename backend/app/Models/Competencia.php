<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo Competencia
 * 
 * Representa las competencias asignadas a cada dependencia
 * Tabla: competencias
 */
class Competencia extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'competencias';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'dependencia_id',
        'nombre',
        'descripcion',
        'marco_legal',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
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
     * Scope para buscar por nombre
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $termino
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre', 'like', "%{$termino}%")
            ->orWhere('descripcion', 'like', "%{$termino}%");
    }

    /**
     * Scope para competencias de una dependencia
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $dependenciaId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorDependencia($query, $dependenciaId)
    {
        return $query->where('dependencia_id', $dependenciaId);
    }
}
