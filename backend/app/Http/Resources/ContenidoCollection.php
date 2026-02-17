<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Collection para Contenidos
 * 
 * Transforma una colección de Contenidos con metadatos de paginación.
 */
final class ContenidoCollection extends ResourceCollection
{
    /**
     * Transformar la colección de recursos
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => ContenidoResource::collection($this->collection),
            'meta' => [
                'total' => $this->total() ?? null,
                'count' => $this->count(),
                'per_page' => $this->perPage() ?? null,
                'current_page' => $this->currentPage() ?? null,
                'total_pages' => $this->lastPage() ?? null,
            ],
            'links' => [
                'first' => $this->url(1) ?? null,
                'last' => $this->url($this->lastPage()) ?? null,
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
        ];
    }
}
