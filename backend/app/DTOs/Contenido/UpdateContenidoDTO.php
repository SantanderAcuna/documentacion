<?php

declare(strict_types=1);

namespace App\DTOs\Contenido;

use Carbon\Carbon;

/**
 * DTO para actualización de Contenido
 * 
 * Contiene solo los datos necesarios para actualizar un contenido existente.
 * Todos los campos son opcionales para permitir actualizaciones parciales.
 */
final class UpdateContenidoDTO
{
    public function __construct(
        public readonly ?int $tipoContenidoId = null,
        public readonly ?string $titulo = null,
        public readonly ?string $slug = null,
        public readonly ?int $dependenciaId = null,
        public readonly ?int $usuarioId = null,
        public readonly ?string $resumen = null,
        public readonly ?string $cuerpo = null,
        public readonly ?string $imagenDestacada = null,
        public readonly ?string $numero = null,
        public readonly ?Carbon $fechaEmision = null,
        public readonly ?Carbon $fechaPublicacion = null,
        public readonly ?string $estado = null,
        public readonly ?bool $esDestacado = null,
        public readonly ?bool $comentariosHabilitados = null,
        public readonly ?string $idioma = null,
        public readonly ?array $metadatos = null,
    ) {
    }

    /**
     * Crear DTO desde request validado
     */
    public static function fromRequest(array $validated): self
    {
        return new self(
            tipoContenidoId: isset($validated['tipo_contenido_id']) ? (int) $validated['tipo_contenido_id'] : null,
            titulo: $validated['titulo'] ?? null,
            slug: $validated['slug'] ?? null,
            dependenciaId: isset($validated['dependencia_id']) ? (int) $validated['dependencia_id'] : null,
            usuarioId: isset($validated['usuario_id']) ? (int) $validated['usuario_id'] : null,
            resumen: $validated['resumen'] ?? null,
            cuerpo: $validated['cuerpo'] ?? null,
            imagenDestacada: $validated['imagen_destacada'] ?? null,
            numero: $validated['numero'] ?? null,
            fechaEmision: isset($validated['fecha_emision']) ? Carbon::parse($validated['fecha_emision']) : null,
            fechaPublicacion: isset($validated['fecha_publicacion']) ? Carbon::parse($validated['fecha_publicacion']) : null,
            estado: $validated['estado'] ?? null,
            esDestacado: isset($validated['es_destacado']) ? (bool) $validated['es_destacado'] : null,
            comentariosHabilitados: isset($validated['comentarios_habilitados']) ? (bool) $validated['comentarios_habilitados'] : null,
            idioma: $validated['idioma'] ?? null,
            metadatos: $validated['metadatos'] ?? null,
        );
    }

    /**
     * Convertir DTO a array para Eloquent
     * Solo incluye campos que tienen valor (no null)
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->tipoContenidoId !== null) {
            $data['tipo_contenido_id'] = $this->tipoContenidoId;
        }

        if ($this->titulo !== null) {
            $data['titulo'] = $this->titulo;
        }

        if ($this->slug !== null) {
            $data['slug'] = $this->slug;
        }

        if ($this->dependenciaId !== null) {
            $data['dependencia_id'] = $this->dependenciaId;
        }

        if ($this->usuarioId !== null) {
            $data['usuario_id'] = $this->usuarioId;
        }

        if ($this->resumen !== null) {
            $data['resumen'] = $this->resumen;
        }

        if ($this->cuerpo !== null) {
            $data['cuerpo'] = $this->cuerpo;
        }

        if ($this->imagenDestacada !== null) {
            $data['imagen_destacada'] = $this->imagenDestacada;
        }

        if ($this->numero !== null) {
            $data['numero'] = $this->numero;
        }

        if ($this->fechaEmision !== null) {
            $data['fecha_emision'] = $this->fechaEmision->toDateString();
        }

        if ($this->fechaPublicacion !== null) {
            $data['fecha_publicacion'] = $this->fechaPublicacion->toDateString();
        }

        if ($this->estado !== null) {
            $data['estado'] = $this->estado;
        }

        if ($this->esDestacado !== null) {
            $data['es_destacado'] = $this->esDestacado;
        }

        if ($this->comentariosHabilitados !== null) {
            $data['comentarios_habilitados'] = $this->comentariosHabilitados;
        }

        if ($this->idioma !== null) {
            $data['idioma'] = $this->idioma;
        }

        if ($this->metadatos !== null) {
            $data['metadatos'] = $this->metadatos;
        }

        return $data;
    }

    /**
     * Verificar si el DTO tiene algún dato para actualizar
     */
    public function hasData(): bool
    {
        return !empty($this->toArray());
    }
}
