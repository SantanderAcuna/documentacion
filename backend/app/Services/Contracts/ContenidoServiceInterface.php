<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\DTOs\Contenido\ContenidoDTO;
use App\DTOs\Contenido\CreateContenidoDTO;
use App\DTOs\Contenido\UpdateContenidoDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Interface ContenidoServiceInterface
 * 
 * Contrato para el servicio de Contenidos.
 * Define la lógica de negocio sin exponer implementación.
 * Siguiendo el principio de Dependency Inversion (SOLID).
 */
interface ContenidoServiceInterface
{
    /**
     * Obtener contenido por ID
     *
     * @param int $id
     * @param bool $withRelations
     * @return ContenidoDTO|null
     */
    public function obtenerContenido(int $id, bool $withRelations = false): ?ContenidoDTO;

    /**
     * Obtener contenido por slug
     *
     * @param string $slug
     * @param bool $withRelations
     * @return ContenidoDTO|null
     */
    public function obtenerPorSlug(string $slug, bool $withRelations = false): ?ContenidoDTO;

    /**
     * Listar contenidos paginados
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function listarContenidos(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Crear nuevo contenido
     *
     * @param CreateContenidoDTO $dto
     * @param int|null $usuarioId
     * @return ContenidoDTO
     */
    public function crearContenido(CreateContenidoDTO $dto, ?int $usuarioId = null): ContenidoDTO;

    /**
     * Actualizar contenido existente
     *
     * @param int $id
     * @param UpdateContenidoDTO $dto
     * @return ContenidoDTO
     */
    public function actualizarContenido(int $id, UpdateContenidoDTO $dto): ContenidoDTO;

    /**
     * Eliminar contenido (soft delete)
     *
     * @param int $id
     * @return bool
     */
    public function eliminarContenido(int $id): bool;

    /**
     * Publicar contenido
     *
     * @param int $id
     * @return ContenidoDTO
     */
    public function publicarContenido(int $id): ContenidoDTO;

    /**
     * Archivar contenido
     *
     * @param int $id
     * @return ContenidoDTO
     */
    public function archivarContenido(int $id): ContenidoDTO;

    /**
     * Buscar contenidos
     *
     * @param string $term
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function buscarContenidos(string $term, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Incrementar contador de vistas
     *
     * @param int $id
     * @return void
     */
    public function registrarVista(int $id): void;
}
