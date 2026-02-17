<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use App\Traits\OptimizableQuery;

/**
 * Modelo Medio
 * 
 * Sistema centralizado polimórfico para TODOS los archivos multimedia
 * (imágenes, videos, audio, documentos)
 * Tabla: medios
 */
class Medio extends Model
{
    use SoftDeletes, OptimizableQuery;

    /**
     * Nombre de la tabla
     *
     * @var string
     */
    protected $table = 'medios';

    /**
     * Atributos asignables en masa
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
        'nombre_archivo',
        'tipo_mime',
        'disco',
        'ruta',
        'tamano',
        'tipo_medio',
        'texto_alternativo',
        'ancho',
        'alto',
        'duracion',
        'ruta_miniatura',
        'conversiones',
        'descripcion',
        'pie_foto',
        'derechos_autor',
        'coleccion',
        'orden',
        'es_destacado',
        'metadatos',
        'subido_por',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tamano' => 'integer',
        'ancho' => 'integer',
        'alto' => 'integer',
        'duracion' => 'integer',
        'orden' => 'integer',
        'es_destacado' => 'boolean',
        'conversiones' => 'array',
        'metadatos' => 'array',
        'eliminado_en' => 'datetime',
    ];

    /**
     * Relación polimórfica con cualquier entidad
     *
     * @return MorphTo
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relación con usuario que subió el archivo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    /**
     * Scope para filtrar por tipo de medio
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tipo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo_medio', $tipo);
    }

    /**
     * Scope para medios destacados
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDestacados($query)
    {
        return $query->where('es_destacado', true);
    }

    /**
     * Scope para filtrar por colección
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $coleccion
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeColeccion($query, string $coleccion)
    {
        return $query->where('coleccion', $coleccion);
    }

    /**
     * Obtener URL completa del archivo
     *
     * @return string|null
     */
    public function getUrlAttribute(): ?string
    {
        return $this->ruta ? Storage::disk($this->disco ?? 'public')->url($this->ruta) : null;
    }

    /**
     * Obtener URL de la miniatura
     *
     * @return string|null
     */
    public function getUrlMiniaturaAttribute(): ?string
    {
        return $this->ruta_miniatura 
            ? Storage::disk($this->disco ?? 'public')->url($this->ruta_miniatura)
            : null;
    }
}
