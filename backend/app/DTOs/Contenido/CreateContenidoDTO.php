<?php

declare(strict_types=1);

namespace App\DTOs\Contenido;

use Carbon\Carbon;

/**
 * DTO para creación de Contenido
 * 
 * Contiene solo los datos necesarios para crear un nuevo contenido.
 * Siguiendo el principio de Interface Segregation (SOLID).
 */
final class CreateContenidoDTO
{
    public function __construct(
        public readonly int $tipoContenidoId,
        public readonly string $titulo,
        public readonly ?string $slug = null,
        public readonly ?int $dependenciaId = null,
        public readonly ?int $usuarioId = null,
        public readonly ?string $resumen = null,
        public readonly ?string $cuerpo = null,
        public readonly ?string $imagenDestacada = null,
        public readonly ?string $numero = null,
        public readonly ?Carbon $fechaEmision = null,
        public readonly ?Carbon $fechaPublicacion = null,
        public readonly string $estado = 'borrador',
        public readonly bool $esDestacado = false,
        public readonly bool $comentariosHabilitados = true,
        public readonly string $idioma = 'es',
        public readonly ?array $metadatos = null,
    ) {
    }

    /**
     * Crear DTO desde request validado
     */
    public static function fromRequest(array $validated): self
    {
        return new self(
            tipoContenidoId: (int) $validated['tipo_contenido_id'],
            titulo: $validated['titulo'],
            slug: $validated['slug'] ?? null,
            dependenciaId: isset($validated['dependencia_id']) ? (int) $validated['dependencia_id'] : null,
            usuarioId: isset($validated['usuario_id']) ? (int) $validated['usuario_id'] : null,
            resumen: $validated['resumen'] ?? null,
            cuerpo: $validated['cuerpo'] ?? null,
            imagenDestacada: $validated['imagen_destacada'] ?? null,
            numero: $validated['numero'] ?? null,
            fechaEmision: isset($validated['fecha_emision']) ? Carbon::parse($validated['fecha_emision']) : null,
            fechaPublicacion: isset($validated['fecha_publicacion']) ? Carbon::parse($validated['fecha_publicacion']) : null,
            estado: $validated['estado'] ?? 'borrador',
            esDestacado: (bool) ($validated['es_destacado'] ?? false),
            comentariosHabilitados: (bool) ($validated['comentarios_habilitados'] ?? true),
            idioma: $validated['idioma'] ?? 'es',
            metadatos: $validated['metadatos'] ?? null,
        );
    }

    /**
     * Convertir DTO a array para Eloquent
     */
    public function toArray(): array
    {
        $data = [
            'tipo_contenido_id' => $this->tipoContenidoId,
            'titulo' => $this->titulo,
            'resumen' => $this->resumen,
            'cuerpo' => $this->cuerpo,
            'imagen_destacada' => $this->imagenDestacada,
            'numero' => $this->numero,
            'fecha_emision' => $this->fechaEmision?->toDateString(),
            'fecha_publicacion' => $this->fechaPublicacion?->toDateString(),
            'estado' => $this->estado,
            'es_destacado' => $this->esDestacado,
            'comentarios_habilitados' => $this->comentariosHabilitados,
            'idioma' => $this->idioma,
            'metadatos' => $this->metadatos,
        ];

        // Agregar campos opcionales solo si tienen valor
        if ($this->slug !== null) {
            $data['slug'] = $this->slug;
        }

        if ($this->dependenciaId !== null) {
            $data['dependencia_id'] = $this->dependenciaId;
        }

        if ($this->usuarioId !== null) {
            $data['usuario_id'] = $this->usuarioId;
        }

        return $data;
    }
}
