<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

/**
 * Trait para caché de modelos y contadores
 * 
 * Implementa estrategias de caché para mejorar performance en consultas frecuentes
 * 
 * @package App\Traits
 */
trait Cacheable
{
    /**
     * Invalidar caché del modelo cuando se actualiza
     */
    protected static function bootCacheable()
    {
        static::saved(function ($model) {
            $model->invalidarCache();
        });

        static::deleted(function ($model) {
            $model->invalidarCache();
        });
    }

    /**
     * Obtener clave de caché del modelo
     * 
     * @return string
     */
    public function getCacheKey(): string
    {
        return sprintf(
            '%s.%s',
            $this->getTable(),
            $this->getKey()
        );
    }

    /**
     * Obtener modelo con caché
     * 
     * @param mixed $id
     * @param int $ttl Tiempo en segundos
     * @return static|null
     */
    public static function findCached($id, int $ttl = 3600)
    {
        $cacheKey = sprintf('%s.%s', (new static)->getTable(), $id);
        
        return Cache::remember($cacheKey, $ttl, function () use ($id) {
            return static::find($id);
        });
    }

    /**
     * Invalidar caché del modelo
     * 
     * @return void
     */
    public function invalidarCache(): void
    {
        Cache::forget($this->getCacheKey());
        
        // Invalidar también cachés de listados si existen
        Cache::forget($this->getTable() . '.all');
        Cache::forget($this->getTable() . '.activos');
    }

    /**
     * Obtener todos los registros con caché
     * 
     * @param int $ttl
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function allCached(int $ttl = 3600)
    {
        $cacheKey = (new static)->getTable() . '.all';
        
        return Cache::remember($cacheKey, $ttl, function () {
            return static::all();
        });
    }

    /**
     * Cachear contador de registros
     * 
     * @param string $scope
     * @param int $ttl
     * @return int
     */
    public static function countCached(string $scope = 'all', int $ttl = 600)
    {
        $cacheKey = sprintf('%s.count.%s', (new static)->getTable(), $scope);
        
        return Cache::remember($cacheKey, $ttl, function () use ($scope) {
            return static::count();
        });
    }
}
