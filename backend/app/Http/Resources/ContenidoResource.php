<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource para Contenido
 * 
 * Transforma el modelo Contenido en un formato JSON apropiado para la API.
 * Incluye relaciones y datos formateados.
 */
final class ContenidoResource extends JsonResource
{
    /**
     * Transformar el recurso en un array
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo_contenido_id' => $this->tipo_contenido_id,
            'dependencia_id' => $this->dependencia_id,
            'usuario_id' => $this->usuario_id,
            'titulo' => $this->titulo,
            'slug' => $this->slug,
            'resumen' => $this->resumen,
            'cuerpo' => $this->cuerpo,
            'imagen_destacada' => $this->imagen_destacada,
            'numero' => $this->numero,
            'fecha_emision' => $this->fecha_emision?->format('Y-m-d'),
            'fecha_publicacion' => $this->fecha_publicacion?->format('Y-m-d'),
            'estado' => $this->estado,
            'publicado_en' => $this->publicado_en?->toDateTimeString(),
            'es_destacado' => (bool) $this->es_destacado,
            'comentarios_habilitados' => (bool) $this->comentarios_habilitados,
            'conteo_vistas' => (int) $this->conteo_vistas,
            'idioma' => $this->idioma,
            'metadatos' => $this->metadatos,
            
            // Relaciones (solo si están cargadas)
            'tipo_contenido' => $this->whenLoaded('tipoContenido'),
            'dependencia' => $this->whenLoaded('dependencia'),
            'usuario' => $this->whenLoaded('usuario'),
            'categorias' => $this->whenLoaded('categorias'),
            'etiquetas' => $this->whenLoaded('etiquetas'),
            'medios' => $this->whenLoaded('medios'),
            
            // Timestamps
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
        ];
    }
}
