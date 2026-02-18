<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo MacroProceso
 * 
 * Representa los macro procesos de la estructura organizacional
 * Tabla: macro_procesos
 */
class MacroProceso extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'macro_procesos';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'color',
        'icono',
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
     * Relación con procesos
     *
     * @return HasMany
     */
    public function procesos(): HasMany
    {
        return $this->hasMany(Proceso::class);
    }

    /**
     * Scope para macro procesos activos
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
}
