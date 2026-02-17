<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo Funcionario
 * 
 * Representa los empleados de la alcaldía
 * Tabla: funcionarios
 */
class Funcionario extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'funcionarios';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'usuario_id',
        'nombre_completo',
        'numero_identificacion',
        'cargo_id',
        'dependencia_id',
        'fecha_contratacion',
        'tipo_contrato',
        'estado',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_contratacion' => 'date',
        'numero_identificacion' => 'encrypted',
        'eliminado_en' => 'datetime',
    ];

    /**
     * Relación con usuario
     *
     * @return BelongsTo
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con cargo
     *
     * @return BelongsTo
     */
    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }

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
     * Relación con perfil de funcionario
     *
     * @return HasOne
     */
    public function perfil(): HasOne
    {
        return $this->hasOne(Perfil::class);
    }

    /**
     * Relación con asignaciones
     *
     * @return HasMany
     */
    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionFuncionario::class);
    }

    /**
     * Scope para funcionarios activos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para filtrar por tipo de contrato
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tipo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorTipoContrato($query, $tipo)
    {
        return $query->where('tipo_contrato', $tipo);
    }

    /**
     * Scope para funcionarios de una dependencia
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
