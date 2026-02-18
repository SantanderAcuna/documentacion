<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Contenido\CreateContenidoDTO;
use App\DTOs\Contenido\UpdateContenidoDTO;
use App\Models\Contenido;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface ContenidoRepositoryInterface
 * 
 * Contrato para el repositorio de Contenidos.
 * Define todos los métodos de acceso a datos sin exponer implementación.
 * Siguiendo el principio de Dependency Inversion (SOLID).
 */
interface ContenidoRepositoryInterface
{
    /**
     * Obtener contenido por ID
     *
     * @param int $id
     * @param array $relations Relaciones a cargar (eager loading)
     * @return Contenido|null
     */
    public function findById(int $id, array $relations = []): ?Contenido;

    /**
     * Obtener contenido por slug
     *
     * @param string $slug
     * @param array $relations
     * @return Contenido|null
     */
    public function findBySlug(string $slug, array $relations = []): ?Contenido;

    /**
     * Obtener todos los contenidos
     *
     * @param array $filters Filtros opcionales
     * @param array $relations
     * @return Collection
     */
    public function all(array $filters = [], array $relations = []): Collection;

    /**
     * Obtener contenidos paginados
     *
     * @param int $perPage
     * @param array $filters
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $filters = [], array $relations = []): LengthAwarePaginator;

    /**
     * Crear nuevo contenido
     *
     * @param CreateContenidoDTO $dto
     * @return Contenido
     */
    public function create(CreateContenidoDTO $dto): Contenido;

    /**
     * Actualizar contenido existente
     *
     * @param int $id
     * @param UpdateContenidoDTO $dto
     * @return Contenido
     */
    public function update(int $id, UpdateContenidoDTO $dto): Contenido;

    /**
     * Eliminar contenido (soft delete)
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Restaurar contenido eliminado
     *
     * @param int $id
     * @return bool
     */
    public function restore(int $id): bool;

    /**
     * Eliminar permanentemente
     *
     * @param int $id
     * @return bool
     */
    public function forceDelete(int $id): bool;

    /**
     * Publicar contenido
     *
     * @param int $id
     * @return Contenido
     */
    public function publicar(int $id): Contenido;

    /**
     * Archivar contenido
     *
     * @param int $id
     * @return Contenido
     */
    public function archivar(int $id): Contenido;

    /**
     * Marcar/desmarcar como destacado
     *
     * @param int $id
     * @param bool $destacado
     * @return Contenido
     */
    public function destacar(int $id, bool $destacado = true): Contenido;

    /**
     * Buscar contenidos por término
     *
     * @param string $term
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $term, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Incrementar contador de vistas
     *
     * @param int $id
     * @return void
     */
    public function incrementViews(int $id): void;

    /**
     * Obtener contenidos por tipo
     *
     * @param int $tipoContenidoId
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function porTipo(int $tipoContenidoId, int $perPage = 15, array $filters = []): LengthAwarePaginator;
}
