<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Contenido\ContenidoDTO;
use App\DTOs\Contenido\CreateContenidoDTO;
use App\DTOs\Contenido\UpdateContenidoDTO;
use App\Repositories\Contracts\ContenidoRepositoryInterface;
use App\Services\Contracts\ContenidoServiceInterface;
use App\Services\Contracts\SeoServiceInterface;
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
        private readonly ContenidoRepositoryInterface $contenidoRepository,
        private readonly SeoServiceInterface $seoService
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

            // Generar metadatos SEO automáticamente
            $metadatosSeo = $this->seoService->generarMetadatos([
                'titulo' => $dto->titulo,
                'resumen' => $dto->resumen,
                'cuerpo' => $dto->cuerpo,
                'imagen_destacada' => $dto->imagenDestacada,
                'slug' => $dto->slug,
            ]);

            // Mezclar metadatos generados con metadatos personalizados (si existen)
            $metadatosFinales = array_merge($metadatosSeo, $dto->metadatos ?? []);

            // Crear DTO con usuario y metadatos SEO
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
                metadatos: $metadatosFinales,
            );

            $contenido = $this->contenidoRepository->create($dtoConUsuario);

            // Log de auditoría
            Log::info('Contenido creado con metadatos SEO automáticos', [
                'contenido_id' => $contenido->id,
                'usuario_id' => $usuarioId,
                'tipo' => $contenido->tipo_contenido_id,
                'seo_generado' => true,
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

            // Obtener contenido actual para generar metadatos
            $contenidoActual = $this->contenidoRepository->findById($id);
            
            if ($contenidoActual === null) {
                throw new \Exception("Contenido no encontrado");
            }

            // Si se actualiza título, resumen o cuerpo, regenerar metadatos SEO
            if ($dto->titulo !== null || $dto->resumen !== null || $dto->cuerpo !== null) {
                $datosParaSeo = [
                    'titulo' => $dto->titulo ?? $contenidoActual->titulo,
                    'resumen' => $dto->resumen ?? $contenidoActual->resumen,
                    'cuerpo' => $dto->cuerpo ?? $contenidoActual->cuerpo,
                    'imagen_destacada' => $dto->imagenDestacada ?? $contenidoActual->imagen_destacada,
                    'slug' => $dto->slug ?? $contenidoActual->slug,
                ];

                $metadatosSeo = $this->seoService->generarMetadatos($datosParaSeo);
                
                // Mezclar con metadatos existentes y nuevos metadatos personalizados
                $metadatosActuales = $contenidoActual->metadatos ?? [];
                $metadatosNuevos = $dto->metadatos ?? [];
                $metadatosFinales = array_merge($metadatosActuales, $metadatosSeo, $metadatosNuevos);

                // Crear nuevo DTO con metadatos actualizados
                $dto = new UpdateContenidoDTO(
                    tipoContenidoId: $dto->tipoContenidoId,
                    titulo: $dto->titulo,
                    slug: $dto->slug,
                    dependenciaId: $dto->dependenciaId,
                    usuarioId: $dto->usuarioId,
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
                    metadatos: $metadatosFinales,
                );
            }

            $contenido = $this->contenidoRepository->update($id, $dto);

            // Log de auditoría
            Log::info('Contenido actualizado con metadatos SEO regenerados', [
                'contenido_id' => $contenido->id,
                'usuario_id' => Auth::id(),
                'seo_regenerado' => true,
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
