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
        Schema::create('actas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('titulo');
            $table->date('fecha_reunion');
            $table->string('tipo_reunion');
            $table->string('ubicacion')->nullable();
            $table->json('asistentes')->nullable();
            $table->text('resumen')->nullable();
            $table->longText('contenido');
            $table->string('ruta_archivo');
            $table->string('nombre_archivo');
            $table->foreignId('dependencia_id')->nullable()->constrained('dependencias')->onDelete('set null');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->enum('estado', ['borrador', 'publicado', 'archivado'])->default('borrador');
            $table->json('metadatos')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('numero');
            $table->index('fecha_reunion');
            $table->index('tipo_reunion');
            $table->index('estado');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('actas');
    }
};
