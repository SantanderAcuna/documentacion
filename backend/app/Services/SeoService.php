<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Contracts\SeoServiceInterface;
use Illuminate\Support\Str;

/**
 * Servicio de SEO
 * 
 * Genera automáticamente metadatos SEO optimizados para contenidos.
 * Incluye meta tags, Open Graph tags y palabras clave.
 */
final class SeoService implements SeoServiceInterface
{
    private const TITULO_MAX_LENGTH = 60;
    private const DESCRIPCION_MIN_LENGTH = 150;
    private const DESCRIPCION_MAX_LENGTH = 160;
    private const KEYWORDS_COUNT = 10;

    // Palabras a excluir de keywords (stop words en español)
    private const STOP_WORDS = [
        'el', 'la', 'de', 'que', 'y', 'a', 'en', 'un', 'ser', 'se', 'no', 'haber',
        'por', 'con', 'su', 'para', 'como', 'estar', 'tener', 'le', 'lo', 'todo',
        'pero', 'más', 'hacer', 'o', 'poder', 'decir', 'este', 'ir', 'otro', 'ese',
        'si', 'me', 'ya', 'ver', 'porque', 'dar', 'cuando', 'él', 'muy', 'sin',
        'vez', 'mucho', 'saber', 'qué', 'sobre', 'mi', 'alguno', 'mismo', 'yo',
        'también', 'hasta', 'año', 'dos', 'querer', 'entre', 'así', 'primero',
        'desde', 'grande', 'eso', 'ni', 'nos', 'llegar', 'pasar', 'tiempo', 'ella',
        'del', 'los', 'las', 'una', 'unos', 'unas', 'al', 'son', 'es', 'ha',
    ];

    /**
     * {@inheritDoc}
     */
    public function generarMetadatos(array $data): array
    {
        $titulo = $data['titulo'] ?? '';
        $resumen = $data['resumen'] ?? null;
        $cuerpo = $data['cuerpo'] ?? null;
        $imagenDestacada = $data['imagen_destacada'] ?? null;
        $slug = $data['slug'] ?? Str::slug($titulo);

        return [
            'meta_titulo' => $this->generarTituloSeo($titulo),
            'meta_descripcion' => $this->generarDescripcionSeo($resumen, $cuerpo),
            'meta_palabras_clave' => $this->generarPalabrasClave($titulo, $cuerpo),
            'canonical_url' => $this->generarUrlCanonica($slug),
            'robots_index' => true,
            'robots_follow' => true,
            'og_titulo' => $this->generarTituloSeo($titulo),
            'og_descripcion' => $this->generarDescripcionSeo($resumen, $cuerpo),
            'og_image' => $imagenDestacada,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function generarTituloSeo(string $titulo): string
    {
        // Limpiar HTML si existe
        $titulo = strip_tags($titulo);
        
        // Limitar a 60 caracteres (óptimo para Google)
        if (mb_strlen($titulo) > self::TITULO_MAX_LENGTH) {
            $titulo = mb_substr($titulo, 0, self::TITULO_MAX_LENGTH - 3) . '...';
        }

        return trim($titulo);
    }

    /**
     * {@inheritDoc}
     */
    public function generarDescripcionSeo(?string $resumen, ?string $cuerpo): string
    {
        // Priorizar resumen si existe
        $texto = $resumen ?? $cuerpo ?? '';

        // Limpiar HTML
        $texto = strip_tags($texto);
        
        // Limpiar espacios múltiples
        $texto = preg_replace('/\s+/', ' ', $texto);
        
        // Limitar a 150-160 caracteres (óptimo para Google)
        if (mb_strlen($texto) < self::DESCRIPCION_MIN_LENGTH && $cuerpo !== null) {
            // Si el resumen es muy corto, tomar del cuerpo
            $texto = strip_tags($cuerpo);
            $texto = preg_replace('/\s+/', ' ', $texto);
        }

        if (mb_strlen($texto) > self::DESCRIPCION_MAX_LENGTH) {
            // Cortar en palabra completa más cercana
            $texto = mb_substr($texto, 0, self::DESCRIPCION_MAX_LENGTH);
            $ultimoEspacio = mb_strrpos($texto, ' ');
            if ($ultimoEspacio !== false) {
                $texto = mb_substr($texto, 0, $ultimoEspacio);
            }
            $texto .= '...';
        }

        return trim($texto);
    }

    /**
     * {@inheritDoc}
     */
    public function generarPalabrasClave(string $titulo, ?string $cuerpo): string
    {
        // Combinar título y cuerpo
        $texto = $titulo . ' ' . ($cuerpo ?? '');
        
        // Limpiar HTML
        $texto = strip_tags($texto);
        
        // Convertir a minúsculas
        $texto = mb_strtolower($texto);
        
        // Eliminar caracteres especiales, mantener solo letras y espacios
        $texto = preg_replace('/[^a-záéíóúñü\s]/u', ' ', $texto);
        
        // Separar en palabras
        $palabras = preg_split('/\s+/', $texto, -1, PREG_SPLIT_NO_EMPTY);
        
        // Filtrar stop words y palabras muy cortas
        $palabrasRelevantes = array_filter($palabras, function ($palabra) {
            return mb_strlen($palabra) >= 4 && !in_array($palabra, self::STOP_WORDS, true);
        });

        // Contar frecuencia
        $frecuencias = array_count_values($palabrasRelevantes);
        
        // Ordenar por frecuencia descendente
        arsort($frecuencias);
        
        // Tomar las top N palabras
        $keywords = array_slice(array_keys($frecuencias), 0, self::KEYWORDS_COUNT);
        
        return implode(', ', $keywords);
    }

    /**
     * {@inheritDoc}
     */
    public function generarUrlCanonica(string $slug): string
    {
        $baseUrl = config('app.url', 'https://santamarta.gov.co');
        return rtrim($baseUrl, '/') . '/contenidos/' . $slug;
    }

    /**
     * Actualizar metadatos existentes con nuevos datos
     *
     * @param array $metadatosActuales
     * @param array $nuevosDatos
     * @return array
     */
    public function actualizarMetadatos(array $metadatosActuales, array $nuevosDatos): array
    {
        $metadatosGenerados = $this->generarMetadatos($nuevosDatos);
        
        // Mezclar manteniendo valores manuales si existen
        return array_merge($metadatosActuales, array_filter($metadatosGenerados, function ($value) {
            return $value !== null;
        }));
    }
}
