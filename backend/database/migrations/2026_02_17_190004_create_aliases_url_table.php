<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Sistema de URLs Amigables (Path Aliases estilo Drupal)
     * Permite URLs personalizables para SEO
     */
    public function up(): void
    {
        Schema::create('aliases_url', function (Blueprint $table) {
            $table->id();
            
            // Relación polimórfica con la entidad
            $table->morphs('entidad'); // entidad_tipo, entidad_id
            
            // URL real del sistema (ej: /contenidos/123)
            $table->string('ruta_sistema');
            
            // URL amigable (alias) (ej: /noticias/reforma-tributaria-2026)
            $table->string('alias')->unique();
            
            // Idioma (para sitios multiidioma)
            $table->string('idioma', 10)->default('es');
            
            // Generación automática
            $table->boolean('auto_generado')->default(true);
            
            // Patrón de generación usado
            $table->string('patron')->nullable(); // Ej: /[tipo]/[año]/[titulo]
            
            // Estado
            $table->boolean('esta_activo')->default(true);
            
            // Auditoría
            $table->foreignId('creado_por')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamps();
            
            // Índices
            $table->index(['entidad_tipo', 'entidad_id']);
            $table->index('ruta_sistema');
            $table->index('alias');
            $table->index('idioma');
            $table->index('esta_activo');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('aliases_url');
    }
};
