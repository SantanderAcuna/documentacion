<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Sistema de Revisiones estilo Drupal
     * Guarda el historial completo de cambios de cada contenido
     */
    public function up(): void
    {
        Schema::create('revisiones_contenido', function (Blueprint $table) {
            $table->id();
            
            // Relación con el contenido original
            $table->foreignId('contenido_id')->constrained('contenidos')->onDelete('cascade');
            
            // Número de revisión (incremental por contenido)
            $table->unsignedInteger('numero_revision');
            
            // Snapshot completo del contenido en este momento
            $table->foreignId('tipo_contenido_id')->constrained('tipos_contenido');
            $table->foreignId('dependencia_id')->nullable()->constrained('dependencias')->onDelete('set null');
            $table->foreignId('usuario_id')->constrained('usuarios');
            
            // Campos comunes
            $table->string('titulo');
            $table->string('slug');
            $table->text('resumen')->nullable();
            $table->longText('cuerpo')->nullable();
            $table->string('imagen_destacada')->nullable();
            
            // Campos específicos (snapshot completo)
            $table->string('numero')->nullable();
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_publicacion')->nullable();
            $table->string('ruta_archivo')->nullable();
            $table->string('nombre_archivo')->nullable();
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->string('ubicacion')->nullable();
            $table->string('tipo_reunion')->nullable();
            $table->json('asistentes')->nullable();
            $table->string('nombre_contratista')->nullable();
            $table->string('identificacion_contratista')->nullable();
            $table->string('tipo_contrato')->nullable();
            $table->decimal('monto', 15, 2)->nullable();
            $table->string('url_secop')->nullable();
            
            // Estado en esta revisión
            $table->enum('estado', ['borrador', 'publicado', 'archivado', 'revision'])->default('borrador');
            $table->timestamp('publicado_en')->nullable();
            $table->integer('conteo_vistas')->default(0);
            $table->boolean('es_destacado')->default(false);
            $table->boolean('comentarios_habilitados')->default(true);
            
            // Metadatos
            $table->json('metadatos')->nullable();
            
            // Información de la revisión
            $table->text('mensaje_revision')->nullable(); // Mensaje del cambio (como git commit)
            $table->boolean('es_revision_actual')->default(false); // Marca la versión actual/publicada
            $table->foreignId('creado_por')->constrained('usuarios')->onDelete('cascade');
            $table->timestamp('creado_en');
            
            // Índices
            $table->index('contenido_id');
            $table->index('numero_revision');
            $table->index('es_revision_actual');
            $table->index(['contenido_id', 'numero_revision']);
            $table->unique(['contenido_id', 'numero_revision']); // Solo una revisión X por contenido
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('revisiones_contenido');
    }
};
