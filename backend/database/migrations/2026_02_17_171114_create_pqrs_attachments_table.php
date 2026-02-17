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
        Schema::create('adjuntos_pqrs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_pqrs_id')->constrained('solicitudes_pqrs')->onDelete('cascade');
            $table->string('nombre_archivo');
            $table->string('ruta_archivo');
            $table->string('tipo_mime');
            $table->unsignedBigInteger('tamano');
            $table->timestamps();
            
            $table->index('solicitud_pqrs_id');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('adjuntos_pqrs');
    }
};
