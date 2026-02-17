<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Pqrs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PqrsController extends Controller
{
    /**
     * Display a listing of PQRS (admin only).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Pqrs::with('responder');

        // Filter by type
        if ($request->has('tipo')) {
            $query->ofType($request->tipo);
        }

        // Filter by status
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        // Search
        if ($request->has('search')) {
            $query->whereFullText(['asunto', 'mensaje'], $request->search);
        }

        $pqrs = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($pqrs);
    }

    /**
     * Store a newly created PQRS (public).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tipo' => 'required|in:peticion,queja,reclamo,sugerencia',
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'documento' => 'nullable|string|max:50',
            'asunto' => 'required|string|max:255',
            'mensaje' => 'required|string',
        ]);

        $pqrs = Pqrs::create([
            ...$validated,
            'folio' => Pqrs::generateFolio(),
            'estado' => 'nuevo',
        ]);

        return response()->json([
            'message' => 'PQRS creado exitosamente',
            'pqrs' => $pqrs,
            'folio' => $pqrs->folio,
        ], 201);
    }

    /**
     * Display the specified PQRS by folio.
     */
    public function show(string $folio): JsonResponse
    {
        $pqrs = Pqrs::where('folio', $folio)->firstOrFail();

        return response()->json($pqrs);
    }

    /**
     * Update the specified PQRS status.
     */
    public function update(Request $request, Pqrs $pqrs): JsonResponse
    {
        $validated = $request->validate([
            'estado' => 'required|in:nuevo,en_proceso,resuelto,cerrado',
        ]);

        $pqrs->update($validated);

        return response()->json($pqrs);
    }

    /**
     * Respond to a PQRS.
     */
    public function respond(Request $request, Pqrs $pqrs): JsonResponse
    {
        $validated = $request->validate([
            'respuesta' => 'required|string',
        ]);

        $pqrs->update([
            'respuesta' => $validated['respuesta'],
            'respondido_at' => now(),
            'respondido_por' => $request->user()->id,
            'estado' => 'resuelto',
        ]);

        return response()->json([
            'message' => 'Respuesta enviada exitosamente',
            'pqrs' => $pqrs->load('responder'),
        ]);
    }
}
