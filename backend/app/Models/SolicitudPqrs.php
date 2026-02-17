<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo SolicitudPqrs
 * 
 * Entidad para solicitudes de PQRS (Peticiones, Quejas, Reclamos, Sugerencias)
 * Tabla: solicitudes_pqrs
 */
class SolicitudPqrs extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'solicitudes_pqrs';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'numero_radicado',
        'tipo_solicitud',
        'nombre_ciudadano',
        'correo_ciudadano',
        'telefono_ciudadano',
        'numero_identificacion_ciudadano',
        'asunto',
        'descripcion',
        'estado',
        'prioridad',
        'dependencia_id',
        'asignado_a',
        'radicado_en',
        'respondido_en',
        'texto_respuesta',
        'metadatos',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'radicado_en' => 'datetime',
        'respondido_en' => 'datetime',
        'metadatos' => 'array',
        'eliminado_en' => 'datetime',
    ];

    /**
     * Atributos que deben ser encriptados
     *
     * @var array<string>
     */
    protected $encrypted = [
        'numero_identificacion_ciudadano',
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
     * Relación con usuario asignado
     *
     * @return BelongsTo
     */
    public function asignadoA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    /**
     * Relación polimórfica con medios
     *
     * @return MorphMany
     */
    public function medios(): MorphMany
    {
        return $this->morphMany(Medio::class, 'mediable');
    }

    /**
     * Scope para solicitudes activas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['nueva', 'en_proceso', 'pendiente']);
    }

    /**
     * Scope para ordenar por más recientes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecientes($query)
    {
        return $query->orderBy('radicado_en', 'desc');
    }

    /**
     * Scope para filtrar por tipo de solicitud
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tipo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo_solicitud', $tipo);
    }

    /**
     * Scope para filtrar por prioridad
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $prioridad
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorPrioridad($query, string $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }
}
