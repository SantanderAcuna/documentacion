<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo Circular
 * 
 * Entidad para circulares administrativas
 * Tabla: circulares
 */
class Circular extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'circulares';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'numero',
        'titulo',
        'resumen',
        'contenido',
        'fecha_emision',
        'dependencia_id',
        'usuario_id',
        'ruta_archivo',
        'nombre_archivo',
        'estado',
        'destinatarios',
        'metadatos',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_emision' => 'date',
        'destinatarios' => 'array',
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
     * Relación polimórfica con categorías
     *
     * @return MorphToMany
     */
    public function categorias(): MorphToMany
    {
        return $this->morphToMany(Categoria::class, 'categorizable', 'categorizables');
    }

    /**
     * Relación polimórfica con etiquetas
     *
     * @return MorphToMany
     */
    public function etiquetas(): MorphToMany
    {
        return $this->morphToMany(Etiqueta::class, 'etiquetable', 'etiquetables');
    }

    /**
     * Relación polimórfica con medios (PDF principalmente)
     *
     * @return MorphMany
     */
    public function medios(): MorphMany
    {
        return $this->morphMany(Medio::class, 'mediable');
    }

    /**
     * Scope para circulares publicadas
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
        return $query->orderBy('fecha_emision', 'desc');
    }

    /**
     * Scope para buscar por número
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $numero
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorNumero($query, string $numero)
    {
        return $query->where('numero', $numero);
    }
}
