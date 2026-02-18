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
        Schema::create('solicitudes_pqrs', function (Blueprint $table) {
            $table->id();
            $table->string('numero_radicado')->unique();
            $table->enum('tipo_solicitud', ['peticion', 'queja', 'reclamo', 'sugerencia']);
            $table->string('nombre_ciudadano');
            $table->string('correo_ciudadano');
            $table->string('telefono_ciudadano')->nullable();
            $table->string('numero_id_ciudadano')->nullable();
            $table->string('asunto');
            $table->text('descripcion');
            $table->enum('estado', ['recibido', 'en_proceso', 'respondido', 'cerrado'])->default('recibido');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->foreignId('dependencia_id')->nullable()->constrained('dependencias')->onDelete('set null');
            $table->foreignId('asignado_a')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamp('radicado_en')->useCurrent();
            $table->timestamp('respondido_en')->nullable();
            $table->text('texto_respuesta')->nullable();
            $table->json('metadatos')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('numero_radicado');
            $table->index('tipo_solicitud');
            $table->index('estado');
            $table->index('prioridad');
            $table->index('radicado_en');
            $table->index('correo_ciudadano');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_pqrs');
    }
};
