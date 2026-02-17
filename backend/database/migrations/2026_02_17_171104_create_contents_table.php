<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        Schema::create('contenidos', function (Blueprint $table) {
            $table->id();
            
            // Relaciones principales
            $table->foreignId('tipo_contenido_id')->constrained('tipos_contenido')->onDelete('cascade');
            $table->foreignId('dependencia_id')->nullable()->constrained('dependencias')->onDelete('set null');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            
            // Campos comunes para TODOS los tipos de contenido
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('resumen')->nullable();
            $table->longText('cuerpo')->nullable(); // Cuerpo/contenido principal
            $table->string('imagen_destacada')->nullable();
            
            // Campos específicos para documentos oficiales (decretos, gacetas, circulares, actas)
            $table->string('numero')->nullable()->unique(); // Ej: DECRETO-001-2026, GACETA-001-2026
            $table->date('fecha_emision')->nullable(); // Fecha de emisión del documento
            $table->date('fecha_publicacion')->nullable(); // Fecha de publicación oficial
            
            // Campos específicos para documentos/archivos
            $table->string('ruta_archivo')->nullable(); // storage/decretos/2026/decreto-001.pdf
            $table->string('nombre_archivo')->nullable(); // decreto-001-2026.pdf
            
            // Campos específicos para eventos
            $table->timestamp('fecha_inicio')->nullable(); // Inicio del evento
            $table->timestamp('fecha_fin')->nullable(); // Fin del evento
            $table->string('ubicacion')->nullable(); // Ubicación del evento
            
            // Campos específicos para actas
            $table->string('tipo_reunion')->nullable(); // Tipo de reunión
            $table->json('asistentes')->nullable(); // Lista de asistentes (JSON)
            
            // Campos específicos para contratos
            $table->string('nombre_contratista')->nullable();
            $table->string('identificacion_contratista')->nullable();
            $table->string('tipo_contrato')->nullable(); // obra, servicios, suministro, consultoria
            $table->decimal('monto', 15, 2)->nullable(); // Monto del contrato
            $table->string('url_secop')->nullable(); // URL en SECOP
            
            // Campos de estado y publicación
            $table->enum('estado', ['borrador', 'publicado', 'archivado', 'revision'])->default('borrador');
            $table->timestamp('publicado_en')->nullable();
            $table->integer('conteo_vistas')->default(0);
            $table->boolean('es_destacado')->default(false);
            $table->boolean('comentarios_habilitados')->default(true);
            
            // Metadatos flexibles (JSON para campos adicionales específicos por tipo)
            $table->json('metadatos')->nullable();
            
            // Auditoría
            $table->foreignId('creado_por')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('actualizado_por')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para optimización de consultas
            $table->index('tipo_contenido_id');
            $table->index('dependencia_id');
            $table->index('usuario_id');
            $table->index('slug');
            $table->index('numero');
            $table->index('estado');
            $table->index('publicado_en');
            $table->index('fecha_emision');
            $table->index('fecha_publicacion');
            $table->index('es_destacado');
            $table->index(['tipo_contenido_id', 'estado']); // Índice compuesto
            $table->index(['tipo_contenido_id', 'publicado_en']); // Índice compuesto
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('contenidos');
    }
};
