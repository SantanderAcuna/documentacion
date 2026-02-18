<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ContentController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\PqrsController;
use App\Http\Controllers\Api\V1\TagController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    
    // Public content endpoints
    Route::get('/contents', [ContentController::class, 'index']);
    Route::get('/contents/{slug}', [ContentController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);
    Route::get('/tags', [TagController::class, 'index']);
    
    // PQRS - Public can create
    Route::post('/pqrs', [PqrsController::class, 'store']);
    Route::get('/pqrs/{folio}', [PqrsController::class, 'show']);
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Contents - Protected CRUD
    Route::middleware('permission:crear-contenidos')->group(function () {
        Route::post('/contents', [ContentController::class, 'store']);
    });
    
    Route::middleware('permission:editar-contenidos')->group(function () {
        Route::put('/contents/{content}', [ContentController::class, 'update']);
    });
    
    Route::middleware('permission:eliminar-contenidos')->group(function () {
        Route::delete('/contents/{content}', [ContentController::class, 'destroy']);
    });
    
    // Categories - Protected CRUD
    Route::middleware('permission:crear-categorias')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
    });
    
    Route::middleware('permission:editar-categorias')->group(function () {
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
    });
    
    Route::middleware('permission:eliminar-categorias')->group(function () {
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    });
    
    // Tags - Protected CRUD
    Route::post('/tags', [TagController::class, 'store']);
    Route::put('/tags/{tag}', [TagController::class, 'update']);
    Route::delete('/tags/{tag}', [TagController::class, 'destroy']);
    
    // Media
    Route::post('/media', [MediaController::class, 'store']);
    Route::delete('/media/{media}', [MediaController::class, 'destroy']);
    
    // PQRS Management
    Route::middleware('permission:ver-pqrs')->group(function () {
        Route::get('/pqrs', [PqrsController::class, 'index']);
    });
    
    Route::middleware('permission:responder-pqrs')->group(function () {
        Route::put('/pqrs/{pqrs}', [PqrsController::class, 'update']);
        Route::post('/pqrs/{pqrs}/respond', [PqrsController::class, 'respond']);
    });
});
