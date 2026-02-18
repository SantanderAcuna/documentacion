<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ContenidoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rutas API versión 1 del CMS Gubernamental
| Todas las rutas están bajo el prefijo /api/v1
|
*/

// API v1
Route::prefix('v1')->group(function () {
    
    // Rutas públicas (sin autenticación)
    Route::get('contenidos', [ContenidoController::class, 'index']);
    Route::get('contenidos/search', [ContenidoController::class, 'search']);
    Route::get('contenidos/{id}', [ContenidoController::class, 'show']);
    
    // Rutas protegidas (requieren autenticación)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Contenidos - CRUD
        Route::post('contenidos', [ContenidoController::class, 'store']);
        Route::put('contenidos/{id}', [ContenidoController::class, 'update']);
        Route::patch('contenidos/{id}', [ContenidoController::class, 'update']);
        Route::delete('contenidos/{id}', [ContenidoController::class, 'destroy']);
        
        // Contenidos - Acciones especiales
        Route::post('contenidos/{id}/publicar', [ContenidoController::class, 'publicar']);
        Route::post('contenidos/{id}/archivar', [ContenidoController::class, 'archivar']);
    });
});
