<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo Cargo
 * 
 * Representa los cargos o puestos de trabajo en la alcaldía
 * Tabla: cargos
 */
class Cargo extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'cargos';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
        'codigo',
        'nivel',
        'dependencia_id',
        'descripcion',
        'requisitos',
        'esta_activo',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'esta_activo' => 'boolean',
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
     * Relación con funcionarios
     *
     * @return HasMany
     */
    public function funcionarios(): HasMany
    {
        return $this->hasMany(Funcionario::class);
    }

    /**
     * Scope para cargos activos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('esta_activo', true);
    }

    /**
     * Scope para filtrar por nivel
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $nivel
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    /**
     * Scope para cargos directivos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDirectivos($query)
    {
        return $query->where('nivel', 'directivo');
    }
}
