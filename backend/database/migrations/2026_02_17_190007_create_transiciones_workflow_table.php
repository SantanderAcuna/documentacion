<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Transiciones entre estados de workflow
     */
    public function up(): void
    {
        Schema::create('transiciones_workflow', function (Blueprint $table) {
            $table->id();
            
            // Estados de origen y destino
            $table->foreignId('estado_origen_id')->constrained('estados_workflow')->onDelete('cascade');
            $table->foreignId('estado_destino_id')->constrained('estados_workflow')->onDelete('cascade');
            
            // Identificación de la transición
            $table->string('nombre'); // enviar_a_revision, aprobar, publicar, archivar
            $table->string('etiqueta'); // Enviar a Revisión, Aprobar, etc.
            $table->text('descripcion')->nullable();
            
            // Permisos requeridos (JSON array de permission names)
            $table->json('permisos_requeridos')->nullable();
            // Ej: ["editar contenidos", "publicar contenidos"]
            
            // Roles que pueden ejecutar esta transición
            $table->json('roles_permitidos')->nullable();
            // Ej: ["editor", "administrador"]
            
            // Notificaciones
            $table->boolean('notificar_autor')->default(false);
            $table->boolean('notificar_roles')->default(false);
            $table->json('roles_notificar')->nullable(); // Roles a notificar
            
            // Estado
            $table->boolean('esta_activo')->default(true);
            
            // Auditoría
            $table->timestamps();
            
            // Índices
            $table->index('estado_origen_id');
            $table->index('estado_destino_id');
            $table->index('nombre');
            $table->unique(['estado_origen_id', 'estado_destino_id', 'nombre'], 'unique_transicion');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('transiciones_workflow');
    }
};
