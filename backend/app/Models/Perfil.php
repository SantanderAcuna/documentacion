<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\OptimizableQuery;
use App\Traits\Cacheable;

/**
 * Modelo Perfil
 * 
 * Representa el perfil profesional de un funcionario
 * Tabla: perfiles
 */
class Perfil extends Model
{
    use SoftDeletes, OptimizableQuery, Cacheable;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'perfiles';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'funcionario_id',
        'foto',
        'biografia',
        'educacion',
        'certificaciones',
        'correo_contacto',
        'telefono_contacto',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'educacion' => 'array',
        'certificaciones' => 'array',
        'eliminado_en' => 'datetime',
    ];

    /**
     * Relación con funcionario
     *
     * @return BelongsTo
     */
    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }

    /**
     * Scope para perfiles completos (con foto y biografía)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompletos($query)
    {
        return $query->whereNotNull('foto')
            ->whereNotNull('biografia');
    }
}
