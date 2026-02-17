<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\DTOs\Contenido\CreateContenidoDTO;
use App\DTOs\Contenido\UpdateContenidoDTO;
use App\Models\Contenido;
use App\Repositories\Contracts\ContenidoRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

/**
 * Implementación del repositorio de Contenidos
 * 
 * Maneja todas las operaciones de acceso a datos para Contenidos.
 * Siguiendo Repository Pattern para abstraer la lógica de persistencia.
 */
final class ContenidoRepository implements ContenidoRepositoryInterface
{
    public function __construct(
        private readonly Contenido $model
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function findById(int $id, array $relations = []): ?Contenido
    {
        $query = $this->model->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findBySlug(string $slug, array $relations = []): ?Contenido
    {
        $query = $this->model->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->where('slug', $slug)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function all(array $filters = [], array $relations = []): Collection
    {
        $query = $this->model->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(int $perPage = 15, array $filters = [], array $relations = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        $this->applyFilters($query, $filters);

        // Ordenar por defecto (más recientes primero)
        if (!isset($filters['order_by'])) {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function create(CreateContenidoDTO $dto): Contenido
    {
        $data = $dto->toArray();

        // Generar slug si no se proporcionó
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['titulo']);
        }

        return $this->model->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function update(int $id, UpdateContenidoDTO $dto): Contenido
    {
        $contenido = $this->findById($id);

        if ($contenido === null) {
            throw new ModelNotFoundException("Contenido con ID {$id} no encontrado");
        }

        $data = $dto->toArray();

        // Regenerar slug si el título cambió
        if (isset($data['titulo']) && $data['titulo'] !== $contenido->titulo) {
            $data['slug'] = $this->generateUniqueSlug($data['titulo'], $id);
        }

        $contenido->update($data);

        return $contenido->fresh();
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id): bool
    {
        $contenido = $this->findById($id);

        if ($contenido === null) {
            throw new ModelNotFoundException("Contenido con ID {$id} no encontrado");
        }

        return $contenido->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function restore(int $id): bool
    {
        $contenido = $this->model->withTrashed()->find($id);

        if ($contenido === null) {
            throw new ModelNotFoundException("Contenido con ID {$id} no encontrado");
        }

        return $contenido->restore();
    }

    /**
     * {@inheritDoc}
     */
    public function forceDelete(int $id): bool
    {
        $contenido = $this->model->withTrashed()->find($id);

        if ($contenido === null) {
            throw new ModelNotFoundException("Contenido con ID {$id} no encontrado");
        }

        return $contenido->forceDelete();
    }

    /**
     * {@inheritDoc}
     */
    public function publicar(int $id): Contenido
    {
        $contenido = $this->findById($id);

        if ($contenido === null) {
            throw new ModelNotFoundException("Contenido con ID {$id} no encontrado");
        }

        $contenido->update([
            'estado' => 'publicado',
            'publicado_en' => Carbon::now(),
        ]);

        return $contenido->fresh();
    }

    /**
     * {@inheritDoc}
     */
    public function archivar(int $id): Contenido
    {
        $contenido = $this->findById($id);

        if ($contenido === null) {
            throw new ModelNotFoundException("Contenido con ID {$id} no encontrado");
        }

        $contenido->update([
            'estado' => 'archivado',
        ]);

        return $contenido->fresh();
    }

    /**
     * {@inheritDoc}
     */
    public function destacar(int $id, bool $destacado = true): Contenido
    {
        $contenido = $this->findById($id);

        if ($contenido === null) {
            throw new ModelNotFoundException("Contenido con ID {$id} no encontrado");
        }

        $contenido->update([
            'es_destacado' => $destacado,
        ]);

        return $contenido->fresh();
    }

    /**
     * {@inheritDoc}
     */
    public function search(string $term, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Búsqueda FULLTEXT si está disponible
        $query->whereRaw(
            'MATCH(titulo, cuerpo) AGAINST(? IN BOOLEAN MODE)',
            [$term]
        );

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function incrementViews(int $id): void
    {
        $this->model->where('id', $id)->increment('conteo_vistas');
    }

    /**
     * {@inheritDoc}
     */
    public function porTipo(int $tipoContenidoId, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        $query->where('tipo_contenido_id', $tipoContenidoId);

        $this->applyFilters($query, $filters);

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Aplicar filtros a la consulta
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    private function applyFilters($query, array $filters): void
    {
        if (isset($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (isset($filters['tipo_contenido_id'])) {
            $query->where('tipo_contenido_id', $filters['tipo_contenido_id']);
        }

        if (isset($filters['dependencia_id'])) {
            $query->where('dependencia_id', $filters['dependencia_id']);
        }

        if (isset($filters['usuario_id'])) {
            $query->where('usuario_id', $filters['usuario_id']);
        }

        if (isset($filters['es_destacado'])) {
            $query->where('es_destacado', (bool) $filters['es_destacado']);
        }

        if (isset($filters['idioma'])) {
            $query->where('idioma', $filters['idioma']);
        }

        if (isset($filters['fecha_desde'])) {
            $query->where('created_at', '>=', $filters['fecha_desde']);
        }

        if (isset($filters['fecha_hasta'])) {
            $query->where('created_at', '<=', $filters['fecha_hasta']);
        }

        if (isset($filters['order_by'])) {
            $direction = $filters['order_direction'] ?? 'asc';
            $query->orderBy($filters['order_by'], $direction);
        }
    }

    /**
     * Generar slug único
     *
     * @param string $titulo
     * @param int|null $excludeId
     * @return string
     */
    private function generateUniqueSlug(string $titulo, ?int $excludeId = null): string
    {
        $slug = Str::slug($titulo);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Verificar si el slug existe
     *
     * @param string $slug
     * @param int|null $excludeId
     * @return bool
     */
    private function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = $this->model->where('slug', $slug);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
