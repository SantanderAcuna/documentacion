<?php

declare(strict_types=1);

namespace App\DTOs\Contenido;

use Carbon\Carbon;

/**
 * Data Transfer Object para Contenido
 * 
 * Representa un contenido completo del sistema con todos sus atributos.
 * Siguiendo el patrón DTO para transferencia de datos entre capas.
 */
final class ContenidoDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $tipoContenidoId,
        public readonly ?int $dependenciaId,
        public readonly ?int $usuarioId,
        public readonly string $titulo,
        public readonly string $slug,
        public readonly ?string $resumen,
        public readonly ?string $cuerpo,
        public readonly ?string $imagenDestacada,
        public readonly ?string $numero,
        public readonly ?Carbon $fechaEmision,
        public readonly ?Carbon $fechaPublicacion,
        public readonly string $estado,
        public readonly ?Carbon $publicadoEn,
        public readonly bool $esDestacado,
        public readonly bool $comentariosHabilitados,
        public readonly int $conteoVistas,
        public readonly ?string $idioma,
        public readonly ?array $metadatos,
        public readonly ?Carbon $createdAt,
        public readonly ?Carbon $updatedAt,
        public readonly ?Carbon $deletedAt,
    ) {
    }

    /**
     * Crear DTO desde array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            tipoContenidoId: (int) $data['tipo_contenido_id'],
            dependenciaId: isset($data['dependencia_id']) ? (int) $data['dependencia_id'] : null,
            usuarioId: isset($data['usuario_id']) ? (int) $data['usuario_id'] : null,
            titulo: $data['titulo'],
            slug: $data['slug'],
            resumen: $data['resumen'] ?? null,
            cuerpo: $data['cuerpo'] ?? null,
            imagenDestacada: $data['imagen_destacada'] ?? null,
            numero: $data['numero'] ?? null,
            fechaEmision: isset($data['fecha_emision']) ? Carbon::parse($data['fecha_emision']) : null,
            fechaPublicacion: isset($data['fecha_publicacion']) ? Carbon::parse($data['fecha_publicacion']) : null,
            estado: $data['estado'] ?? 'borrador',
            publicadoEn: isset($data['publicado_en']) ? Carbon::parse($data['publicado_en']) : null,
            esDestacado: (bool) ($data['es_destacado'] ?? false),
            comentariosHabilitados: (bool) ($data['comentarios_habilitados'] ?? true),
            conteoVistas: (int) ($data['conteo_vistas'] ?? 0),
            idioma: $data['idioma'] ?? 'es',
            metadatos: $data['metadatos'] ?? null,
            createdAt: isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            deletedAt: isset($data['deleted_at']) ? Carbon::parse($data['deleted_at']) : null,
        );
    }

    /**
     * Convertir DTO a array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tipo_contenido_id' => $this->tipoContenidoId,
            'dependencia_id' => $this->dependenciaId,
            'usuario_id' => $this->usuarioId,
            'titulo' => $this->titulo,
            'slug' => $this->slug,
            'resumen' => $this->resumen,
            'cuerpo' => $this->cuerpo,
            'imagen_destacada' => $this->imagenDestacada,
            'numero' => $this->numero,
            'fecha_emision' => $this->fechaEmision?->toDateString(),
            'fecha_publicacion' => $this->fechaPublicacion?->toDateString(),
            'estado' => $this->estado,
            'publicado_en' => $this->publicadoEn?->toDateTimeString(),
            'es_destacado' => $this->esDestacado,
            'comentarios_habilitados' => $this->comentariosHabilitados,
            'conteo_vistas' => $this->conteoVistas,
            'idioma' => $this->idioma,
            'metadatos' => $this->metadatos,
            'created_at' => $this->createdAt?->toDateTimeString(),
            'updated_at' => $this->updatedAt?->toDateTimeString(),
            'deleted_at' => $this->deletedAt?->toDateTimeString(),
        ];
    }
}
