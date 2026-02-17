<?php

declare(strict_types=1);

namespace App\Services\Contracts;

/**
 * Interface SeoServiceInterface
 * 
 * Contrato para el servicio de generación de metadatos SEO.
 * Genera automáticamente meta tags optimizados para posicionamiento.
 */
interface SeoServiceInterface
{
    /**
     * Generar metadatos SEO completos para contenido
     *
     * @param array $data Datos del contenido (titulo, resumen, cuerpo, imagen_destacada, slug)
     * @return array Metadatos SEO generados
     */
    public function generarMetadatos(array $data): array;

    /**
     * Generar título SEO optimizado
     *
     * @param string $titulo
     * @return string
     */
    public function generarTituloSeo(string $titulo): string;

    /**
     * Generar descripción SEO optimizada
     *
     * @param string|null $resumen
     * @param string|null $cuerpo
     * @return string
     */
    public function generarDescripcionSeo(?string $resumen, ?string $cuerpo): string;

    /**
     * Generar palabras clave relevantes
     *
     * @param string $titulo
     * @param string|null $cuerpo
     * @return string
     */
    public function generarPalabrasClave(string $titulo, ?string $cuerpo): string;

    /**
     * Generar URL canónica
     *
     * @param string $slug
     * @return string
     */
    public function generarUrlCanonica(string $slug): string;
}
