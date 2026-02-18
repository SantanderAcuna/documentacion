<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Contenido\CreateContenidoDTO;
use App\DTOs\Contenido\UpdateContenidoDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contenido\StoreContenidoRequest;
use App\Http\Requests\Contenido\UpdateContenidoRequest;
use App\Http\Resources\ContenidoCollection;
use App\Http\Resources\ContenidoResource;
use App\Services\Contracts\ContenidoServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Controller de Contenidos API v1
 *
 * Maneja todas las peticiones HTTP para gestión de contenidos.
 * Siguiendo REST best practices y patrón Controller delgado.
 */
final class ContenidoController extends Controller
{
    public function __construct(
        private readonly ContenidoServiceInterface $contenidoService
    ) {
    }

    /**
     * Listar contenidos paginados
     *
     * @param Request $request
     * @return ContenidoCollection
     */
    public function index(Request $request): ContenidoCollection
    {
        $perPage = (int) $request->input('per_page', 15);
        $filters = $request->only([
            'estado',
            'tipo_contenido_id',
            'dependencia_id',
            'usuario_id',
            'es_destacado',
            'idioma',
            'fecha_desde',
            'fecha_hasta',
            'order_by',
            'order_direction',
        ]);

        $contenidos = $this->contenidoService->listarContenidos($perPage, $filters);

        return new ContenidoCollection($contenidos);
    }

    /**
     * Crear nuevo contenido
     *
     * @param StoreContenidoRequest $request
     * @return JsonResponse
     */
    public function store(StoreContenidoRequest $request): JsonResponse
    {
        try {
            $dto = CreateContenidoDTO::fromRequest($request->validated());
            $contenido = $this->contenidoService->crearContenido($dto);

            return response()->json([
                'success' => true,
                'message' => 'Contenido creado exitosamente',
                'data' => new ContenidoResource($contenido),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear contenido',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Mostrar contenido específico
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $contenido = $this->contenidoService->obtenerContenido($id, withRelations: true);

            if ($contenido === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contenido no encontrado',
                ], Response::HTTP_NOT_FOUND);
            }

            // Registrar vista
            $this->contenidoService->registrarVista($id);

            return response()->json([
                'success' => true,
                'data' => new ContenidoResource($contenido),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener contenido',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Actualizar contenido existente
     *
     * @param UpdateContenidoRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateContenidoRequest $request, int $id): JsonResponse
    {
        try {
            $dto = UpdateContenidoDTO::fromRequest($request->validated());

            if (!$dto->hasData()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se proporcionaron datos para actualizar',
                ], Response::HTTP_BAD_REQUEST);
            }

            $contenido = $this->contenidoService->actualizarContenido($id, $dto);

            return response()->json([
                'success' => true,
                'message' => 'Contenido actualizado exitosamente',
                'data' => new ContenidoResource($contenido),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contenido no encontrado',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar contenido',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Eliminar contenido (soft delete)
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->contenidoService->eliminarContenido($id);

            return response()->json([
                'success' => true,
                'message' => 'Contenido eliminado exitosamente',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contenido no encontrado',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar contenido',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Publicar contenido
     *
     * @param int $id
     * @return JsonResponse
     */
    public function publicar(int $id): JsonResponse
    {
        try {
            $contenido = $this->contenidoService->publicarContenido($id);

            return response()->json([
                'success' => true,
                'message' => 'Contenido publicado exitosamente',
                'data' => new ContenidoResource($contenido),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contenido no encontrado',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al publicar contenido',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Archivar contenido
     *
     * @param int $id
     * @return JsonResponse
     */
    public function archivar(int $id): JsonResponse
    {
        try {
            $contenido = $this->contenidoService->archivarContenido($id);

            return response()->json([
                'success' => true,
                'message' => 'Contenido archivado exitosamente',
                'data' => new ContenidoResource($contenido),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contenido no encontrado',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al archivar contenido',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Buscar contenidos
     *
     * @param Request $request
     * @return ContenidoCollection
     */
    public function search(Request $request): ContenidoCollection
    {
        $request->validate([
            'q' => 'required|string|min:3',
            'per_page' => 'nullable|integer|min:1|max:100',
        ], [
            'q.required' => 'El término de búsqueda es obligatorio',
            'q.string' => 'El término de búsqueda debe ser texto',
            'q.min' => 'El término de búsqueda debe tener al menos 3 caracteres',
            'per_page.integer' => 'El número de resultados por página debe ser un entero',
            'per_page.min' => 'Mínimo 1 resultado por página',
            'per_page.max' => 'Máximo 100 resultados por página',
        ]);

        $term = $request->input('q');
        $perPage = (int) $request->input('per_page', 15);
        $filters = $request->only(['estado', 'tipo_contenido_id']);

        $contenidos = $this->contenidoService->buscarContenidos($term, $filters, $perPage);

        return new ContenidoCollection($contenidos);
    }
}
