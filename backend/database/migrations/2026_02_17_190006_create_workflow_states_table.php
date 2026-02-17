<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Sistema de Estados de Workflow (estilo Drupal)
     */
    public function up(): void
    {
        Schema::create('estados_workflow', function (Blueprint $table) {
            $table->id();
            
            // Identificación del estado
            $table->string('nombre'); // borrador, revision, aprobado, publicado, archivado
            $table->string('etiqueta'); // Borrador, En Revisión, etc.
            $table->text('descripcion')->nullable();
            
            // Color para UI (hex)
            $table->string('color', 7)->default('#6c757d'); // Bootstrap colors
            
            // Icono
            $table->string('icono')->nullable(); // FontAwesome class
            
            // Comportamiento
            $table->boolean('es_estado_inicial')->default(false); // Estado por defecto
            $table->boolean('es_estado_publicado')->default(false); // Es visible al público
            $table->boolean('es_estado_final')->default(false); // No permite más cambios
            
            // Orden
            $table->unsignedInteger('peso')->default(0);
            
            // Estado
            $table->boolean('esta_activo')->default(true);
            
            // Auditoría
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('nombre');
            $table->index('es_estado_inicial');
            $table->index('es_estado_publicado');
            $table->index('peso');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados_workflow');
    }
};
