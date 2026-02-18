<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\ContenidoRepositoryInterface;
use App\Repositories\Eloquent\ContenidoRepository;
use App\Services\ContenidoService;
use App\Services\Contracts\ContenidoServiceInterface;
use App\Services\Contracts\SeoServiceInterface;
use App\Services\SeoService;
use Illuminate\Support\ServiceProvider;

/**
 * Repository Service Provider
 * 
 * Registra los bindings de Dependency Injection para:
 * - Repositories (Data Layer)
 * - Services (Business Logic Layer)
 * 
 * Siguiendo el principio de Dependency Inversion (SOLID).
 */
final class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Registrar servicios
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(
            ContenidoRepositoryInterface::class,
            ContenidoRepository::class
        );

        // Services
        $this->app->bind(
            ContenidoServiceInterface::class,
            ContenidoService::class
        );

        $this->app->bind(
            SeoServiceInterface::class,
            SeoService::class
        );
    }

    /**
     * Bootstrap servicios
     */
    public function boot(): void
    {
        //
    }
}
