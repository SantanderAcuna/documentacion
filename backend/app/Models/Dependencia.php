<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo Dependencia
 * 
 * Representa la estructura organizacional jerárquica de la alcaldía
 * Tabla: dependencias
 */
class Dependencia extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'dependencias';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'padre_id',
        'telefono',
        'correo',
        'ubicacion',
        'esta_activo',
        'orden',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'esta_activo' => 'boolean',
        'orden' => 'integer',
        'eliminado_en' => 'datetime',
    ];

    /**
     * Relación con dependencia padre (jerárquica)
     *
     * @return BelongsTo
     */
    public function padre(): BelongsTo
    {
        return $this->belongsTo(Dependencia::class, 'padre_id');
    }

    /**
     * Relación con dependencias hijas (jerárquica)
     *
     * @return HasMany
     */
    public function hijos(): HasMany
    {
        return $this->hasMany(Dependencia::class, 'padre_id');
    }

    /**
     * Scope para obtener solo dependencias activas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivas($query)
    {
        return $query->where('esta_activo', true);
    }

    /**
     * Scope para obtener dependencias raíz (sin padre)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRaiz($query)
    {
        return $query->whereNull('padre_id');
    }

    /**
     * Obtener el árbol completo de dependencias hijas
     *
     * @return HasMany
     */
    public function hijosRecursivos(): HasMany
    {
        return $this->hijos()->with('hijosRecursivos');
    }
}
