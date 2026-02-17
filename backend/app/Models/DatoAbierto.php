<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo DatoAbierto
 * 
 * Entidad para datos abiertos
 * Tabla: datos_abiertos
 */
class DatoAbierto extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'datos_abiertos';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
        'titulo',
        'descripcion',
        'formato',
        'categoria',
        'ruta_archivo',
        'url_archivo',
        'conteo_registros',
        'ultima_actualizacion',
        'dependencia_id',
        'usuario_id',
        'estado',
        'metadatos',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'conteo_registros' => 'integer',
        'ultima_actualizacion' => 'datetime',
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
     * Relación con usuario
     *
     * @return BelongsTo
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para datos abiertos publicados
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublicados($query)
    {
        return $query->where('estado', 'publicado');
    }

    /**
     * Scope para ordenar por más recientes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecientes($query)
    {
        return $query->orderBy('ultima_actualizacion', 'desc');
    }

    /**
     * Scope para filtrar por formato
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $formato
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorFormato($query, string $formato)
    {
        return $query->where('formato', $formato);
    }
}
