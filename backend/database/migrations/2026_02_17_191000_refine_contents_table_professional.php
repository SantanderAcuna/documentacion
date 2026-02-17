<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migración: Refinamiento Profesional de Tabla Contenidos
 * 
 * Esta migración agrega campos adicionales de nivel empresarial a la tabla contenidos
 * para alcanzar el nivel de CMS profesionales como Drupal.
 * 
 * Campos agregados:
 * - Versionado y control
 * - Multiidioma
 * - Autor flexible
 * - SEO avanzado (9 campos)
 * - Accesibilidad WCAG 2.1 (4 campos)
 * - Presentación y UX
 * - Engagement y métricas
 * - Jurídico/Legal
 * - Índices optimizados (26 total)
 * - Índice FULLTEXT para búsqueda avanzada
 */
return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        Schema::table('contenidos', function (Blueprint $table) {
            // ========================================
            // VERSIONADO Y CONTROL
            // ========================================
            $table->integer('version')->default(1)->after('tipo_contenido_id')
                ->comment('Número de versión actual del contenido');
            $table->boolean('permite_revisiones')->default(true)->after('version')
                ->comment('Habilitar/deshabilitar control de versiones');
            $table->timestamp('fecha_revision')->nullable()->after('fecha_expiracion')
                ->comment('Fecha programada para revisión del contenido');
            $table->timestamp('fecha_expiracion')->nullable()->after('publicado_en')
                ->comment('Fecha de expiración/archivo automático');

            // ========================================
            // MULTIIDIOMA
            // ========================================
            $table->string('idioma', 5)->default('es')->after('slug')
                ->comment('Código de idioma ISO 639-1 (es, en, fr, etc.)');
            $table->foreignId('contenido_traduccion_de')->nullable()->after('idioma')
                ->constrained('contenidos')->onDelete('cascade')
                ->comment('FK al contenido original del cual esta es una traducción');

            // ========================================
            // AUTOR FLEXIBLE (para autores no registrados)
            // ========================================
            $table->string('autor_nombre')->nullable()->after('usuario_id')
                ->comment('Nombre del autor (para contenido de ciudadanos/externos)');
            $table->string('autor_email')->nullable()->after('autor_nombre')
                ->comment('Email del autor (para notificaciones)');

            // ========================================
            // SEO AVANZADO (9 campos)
            // ========================================
            $table->string('meta_titulo')->nullable()->after('titulo')
                ->comment('Título SEO personalizado (diferente del título visible)');
            $table->text('meta_descripcion')->nullable()->after('meta_titulo')
                ->comment('Descripción SEO para resultados de búsqueda');
            $table->string('meta_palabras_clave')->nullable()->after('meta_descripcion')
                ->comment('Keywords SEO separadas por comas');
            $table->string('canonical_url')->nullable()->after('slug')
                ->comment('URL canónica para evitar contenido duplicado');
            $table->boolean('robots_index')->default(true)->after('meta_palabras_clave')
                ->comment('Permitir indexación por motores de búsqueda');
            $table->boolean('robots_follow')->default(true)->after('robots_index')
                ->comment('Permitir seguir enlaces en la página');

            // Open Graph (redes sociales)
            $table->string('og_image')->nullable()->after('imagen_destacada')
                ->comment('Imagen Open Graph para compartir en redes sociales');
            $table->string('og_titulo')->nullable()->after('og_image')
                ->comment('Título Open Graph (para redes sociales)');
            $table->text('og_descripcion')->nullable()->after('og_titulo')
                ->comment('Descripción Open Graph (para redes sociales)');

            // ========================================
            // ACCESIBILIDAD WCAG 2.1 (4 campos)
            // ========================================
            $table->enum('nivel_accesibilidad', ['A', 'AA', 'AAA'])->default('AA')->after('comentarios_habilitados')
                ->comment('Nivel de accesibilidad WCAG 2.1 (A, AA, AAA)');
            $table->boolean('requiere_transcripcion')->default(false)->after('nivel_accesibilidad')
                ->comment('Indica si el contenido multimedia requiere transcripción');
            $table->longText('transcripcion')->nullable()->after('requiere_transcripcion')
                ->comment('Transcripción de texto para contenido de video/audio');
            $table->text('descripcion_audio')->nullable()->after('transcripcion')
                ->comment('Descripción de audio para personas con discapacidad visual');

            // ========================================
            // PRESENTACIÓN Y UX
            // ========================================
            $table->string('plantilla')->nullable()->after('imagen_destacada')
                ->comment('Nombre de la plantilla/template a usar para renderizar');
            $table->integer('peso')->default(0)->after('es_destacado')
                ->comment('Peso para ordenamiento manual (menor = primero)');
            $table->string('formato_visualizacion')->default('completo')->after('plantilla')
                ->comment('Modo de visualización: completo, resumen, teaser, tarjeta');

            // ========================================
            // ENGAGEMENT Y MÉTRICAS (4 campos)
            // ========================================
            $table->integer('conteo_comentarios')->default(0)->after('conteo_vistas')
                ->comment('Total de comentarios aprobados');
            $table->integer('conteo_likes')->default(0)->after('conteo_comentarios')
                ->comment('Total de "me gusta" recibidos');
            $table->integer('conteo_compartidos')->default(0)->after('conteo_likes')
                ->comment('Total de veces compartido en redes sociales');
            $table->decimal('puntuacion_promedio', 3, 2)->nullable()->after('conteo_compartidos')
                ->comment('Puntuación promedio de valoraciones (0.00 - 5.00)');

            // ========================================
            // JURÍDICO/LEGAL (para documentos oficiales)
            // ========================================
            $table->boolean('requiere_firma_digital')->default(false)->after('nombre_archivo')
                ->comment('Indica si el documento requiere firma digital');
            $table->boolean('firmado_digitalmente')->default(false)->after('requiere_firma_digital')
                ->comment('Indica si el documento ha sido firmado digitalmente');
            $table->string('hash_documento')->nullable()->after('firmado_digitalmente')
                ->comment('Hash SHA256 del archivo para verificar integridad');
            $table->date('fecha_vigencia_desde')->nullable()->after('fecha_publicacion')
                ->comment('Fecha de inicio de vigencia del documento');
            $table->date('fecha_vigencia_hasta')->nullable()->after('fecha_vigencia_desde')
                ->comment('Fecha de fin de vigencia del documento');

            // ========================================
            // ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
            // ========================================
            
            // Índices simples
            $table->index('idioma', 'idx_contenidos_idioma');
            $table->index('peso', 'idx_contenidos_peso');
            $table->index('fecha_vigencia_desde', 'idx_contenidos_vigencia_desde');
            $table->index('fecha_vigencia_hasta', 'idx_contenidos_vigencia_hasta');
            $table->index('contenido_traduccion_de', 'idx_contenidos_traduccion_de');
            
            // Índices compuestos para queries comunes
            $table->index(['tipo_contenido_id', 'idioma'], 'idx_contenidos_tipo_idioma');
            $table->index(['idioma', 'tipo_contenido_id'], 'idx_contenidos_idioma_tipo');
            $table->index(['fecha_vigencia_desde', 'fecha_vigencia_hasta'], 'idx_contenidos_vigencia');
            $table->index(['peso', 'publicado_en'], 'idx_contenidos_peso_publicado');
            $table->index(['idioma', 'slug'], 'idx_contenidos_idioma_slug');
        });

        // ========================================
        // ÍNDICE FULLTEXT PARA BÚSQUEDA AVANZADA
        // ========================================
        // Solo para MySQL/MariaDB
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE contenidos ADD FULLTEXT INDEX idx_contenidos_fulltext (titulo, cuerpo)');
        }
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        // Eliminar índice FULLTEXT primero
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE contenidos DROP INDEX idx_contenidos_fulltext');
        }

        Schema::table('contenidos', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex('idx_contenidos_idioma');
            $table->dropIndex('idx_contenidos_peso');
            $table->dropIndex('idx_contenidos_vigencia_desde');
            $table->dropIndex('idx_contenidos_vigencia_hasta');
            $table->dropIndex('idx_contenidos_traduccion_de');
            $table->dropIndex('idx_contenidos_tipo_idioma');
            $table->dropIndex('idx_contenidos_idioma_tipo');
            $table->dropIndex('idx_contenidos_vigencia');
            $table->dropIndex('idx_contenidos_peso_publicado');
            $table->dropIndex('idx_contenidos_idioma_slug');
            
            // Eliminar foreign key
            $table->dropForeign(['contenido_traduccion_de']);
            
            // Eliminar columnas en orden inverso
            $table->dropColumn([
                // Jurídico/Legal
                'fecha_vigencia_hasta',
                'fecha_vigencia_desde',
                'hash_documento',
                'firmado_digitalmente',
                'requiere_firma_digital',
                
                // Engagement
                'puntuacion_promedio',
                'conteo_compartidos',
                'conteo_likes',
                'conteo_comentarios',
                
                // Presentación
                'formato_visualizacion',
                'peso',
                'plantilla',
                
                // Accesibilidad
                'descripcion_audio',
                'transcripcion',
                'requiere_transcripcion',
                'nivel_accesibilidad',
                
                // SEO
                'og_descripcion',
                'og_titulo',
                'og_image',
                'robots_follow',
                'robots_index',
                'meta_palabras_clave',
                'meta_descripcion',
                'meta_titulo',
                'canonical_url',
                
                // Autor flexible
                'autor_email',
                'autor_nombre',
                
                // Multiidioma
                'contenido_traduccion_de',
                'idioma',
                
                // Versionado
                'fecha_expiracion',
                'fecha_revision',
                'permite_revisiones',
                'version',
            ]);
        });
    }
};
