<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Contenido\ContenidoDTO;
use App\DTOs\Contenido\CreateContenidoDTO;
use App\DTOs\Contenido\UpdateContenidoDTO;
use App\Repositories\Contracts\ContenidoRepositoryInterface;
use App\Services\Contracts\ContenidoServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicio de Contenidos
 * 
 * Implementa la lógica de negocio para gestión de contenidos.
 * Orquesta operaciones entre repositorios y aplica reglas de negocio.
 * Siguiendo Service Layer Pattern y Single Responsibility (SOLID).
 */
final class ContenidoService implements ContenidoServiceInterface
{
    public function __construct(
        private readonly ContenidoRepositoryInterface $contenidoRepository
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function obtenerContenido(int $id, bool $withRelations = false): ?ContenidoDTO
    {
        $relations = $withRelations ? ['tipoContenido', 'dependencia', 'usuario', 'categorias', 'etiquetas', 'medios'] : [];

        $contenido = $this->contenidoRepository->findById($id, $relations);

        if ($contenido === null) {
            return null;
        }

        return ContenidoDTO::fromArray($contenido->toArray());
    }

    /**
     * {@inheritDoc}
     */
    public function obtenerPorSlug(string $slug, bool $withRelations = false): ?ContenidoDTO
    {
        $relations = $withRelations ? ['tipoContenido', 'dependencia', 'usuario', 'categorias', 'etiquetas', 'medios'] : [];

        $contenido = $this->contenidoRepository->findBySlug($slug, $relations);

        if ($contenido === null) {
            return null;
        }

        return ContenidoDTO::fromArray($contenido->toArray());
    }

    /**
     * {@inheritDoc}
     */
    public function listarContenidos(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        // Validar perPage
        $perPage = min(max($perPage, 1), 100);

        $relations = ['tipoContenido', 'dependencia', 'usuario'];

        return $this->contenidoRepository->paginate($perPage, $filters, $relations);
    }

    /**
     * {@inheritDoc}
     */
    public function crearContenido(CreateContenidoDTO $dto, ?int $usuarioId = null): ContenidoDTO
    {
        try {
            DB::beginTransaction();

            // Asignar usuario actual si no se proporcionó
            if ($usuarioId === null && Auth::check()) {
                $usuarioId = Auth::id();
            }

            // Crear DTO con usuario
            $dtoConUsuario = new CreateContenidoDTO(
                tipoContenidoId: $dto->tipoContenidoId,
                titulo: $dto->titulo,
                slug: $dto->slug,
                dependenciaId: $dto->dependenciaId,
                usuarioId: $usuarioId,
                resumen: $dto->resumen,
                cuerpo: $dto->cuerpo,
                imagenDestacada: $dto->imagenDestacada,
                numero: $dto->numero,
                fechaEmision: $dto->fechaEmision,
                fechaPublicacion: $dto->fechaPublicacion,
                estado: $dto->estado,
                esDestacado: $dto->esDestacado,
                comentariosHabilitados: $dto->comentariosHabilitados,
                idioma: $dto->idioma,
                metadatos: $dto->metadatos,
            );

            $contenido = $this->contenidoRepository->create($dtoConUsuario);

            // Log de auditoría
            Log::info('Contenido creado', [
                'contenido_id' => $contenido->id,
                'usuario_id' => $usuarioId,
                'tipo' => $contenido->tipo_contenido_id,
            ]);

            DB::commit();

            return ContenidoDTO::fromArray($contenido->toArray());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear contenido', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function actualizarContenido(int $id, UpdateContenidoDTO $dto): ContenidoDTO
    {
        try {
            DB::beginTransaction();

            $contenido = $this->contenidoRepository->update($id, $dto);

            // Log de auditoría
            Log::info('Contenido actualizado', [
                'contenido_id' => $contenido->id,
                'usuario_id' => Auth::id(),
            ]);

            DB::commit();

            return ContenidoDTO::fromArray($contenido->toArray());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar contenido', [
                'contenido_id' => $id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function eliminarContenido(int $id): bool
    {
        try {
            DB::beginTransaction();

            $result = $this->contenidoRepository->delete($id);

            // Log de auditoría
            Log::info('Contenido eliminado (soft delete)', [
                'contenido_id' => $id,
                'usuario_id' => Auth::id(),
            ]);

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar contenido', [
                'contenido_id' => $id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function publicarContenido(int $id): ContenidoDTO
    {
        try {
            DB::beginTransaction();

            $contenido = $this->contenidoRepository->publicar($id);

            // Log de auditoría
            Log::info('Contenido publicado', [
                'contenido_id' => $contenido->id,
                'usuario_id' => Auth::id(),
            ]);

            DB::commit();

            return ContenidoDTO::fromArray($contenido->toArray());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al publicar contenido', [
                'contenido_id' => $id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function archivarContenido(int $id): ContenidoDTO
    {
        try {
            DB::beginTransaction();

            $contenido = $this->contenidoRepository->archivar($id);

            // Log de auditoría
            Log::info('Contenido archivado', [
                'contenido_id' => $contenido->id,
                'usuario_id' => Auth::id(),
            ]);

            DB::commit();

            return ContenidoDTO::fromArray($contenido->toArray());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al archivar contenido', [
                'contenido_id' => $id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function buscarContenidos(string $term, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        // Validar perPage
        $perPage = min(max($perPage, 1), 100);

        return $this->contenidoRepository->search($term, $filters, $perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function registrarVista(int $id): void
    {
        $this->contenidoRepository->incrementViews($id);
    }
}
