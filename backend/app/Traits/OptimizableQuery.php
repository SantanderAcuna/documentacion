<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait para optimización de consultas y escalabilidad
 * 
 * Implementa best practices para queries eficientes y manejo de grandes volúmenes de datos
 * 
 * @package App\Traits
 */
trait OptimizableQuery
{
    /**
     * Scope para forzar paginación y evitar carga de todos los registros
     * 
     * @param Builder $query
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function scopePaginadoSeguro(Builder $query, int $perPage = 20)
    {
        return $query->paginate(min($perPage, 100)); // Máximo 100 registros
    }

    /**
     * Scope para cursor pagination (mejor para grandes datasets)
     * 
     * @param Builder $query
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\CursorPaginator
     */
    public function scopePaginadoCursor(Builder $query, int $perPage = 50)
    {
        return $query->cursorPaginate(min($perPage, 100));
    }

    /**
     * Scope para procesar en chunks sin cargar todo en memoria
     * 
     * @param Builder $query
     * @param int $count
     * @param callable $callback
     * @return bool
     */
    public function scopeProcesamientoChunk(Builder $query, int $count, callable $callback)
    {
        return $query->chunk($count, $callback);
    }

    /**
     * Scope para seleccionar solo columnas necesarias
     * 
     * @param Builder $query
     * @param array $columns
     * @return Builder
     */
    public function scopeColumnsBasicas(Builder $query, array $columns = ['id', 'nombre'])
    {
        return $query->select($columns);
    }

    /**
     * Scope para eager loading optimizado
     * 
     * @param Builder $query
     * @param array $relations
     * @return Builder
     */
    public function scopeConRelaciones(Builder $query, array $relations)
    {
        return $query->with($relations);
    }

    /**
     * Cachear resultados de consulta
     * 
     * @param string $cacheKey
     * @param int $minutes
     * @param \Closure $callback
     * @return mixed
     */
    public static function cacheado(string $cacheKey, int $minutes, \Closure $callback)
    {
        return cache()->remember($cacheKey, $minutes * 60, $callback);
    }
}
