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
            $table->foreignId('tipo_contenido_id')->constrained('tipos_contenido')->onDelete('cascade');
            $table->foreignId('dependencia_id')->nullable()->constrained('dependencias')->onDelete('set null');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('resumen')->nullable();
            $table->longText('cuerpo');
            $table->string('imagen_destacada')->nullable();
            $table->enum('estado', ['borrador', 'publicado', 'archivado'])->default('borrador');
            $table->timestamp('publicado_en')->nullable();
            $table->integer('conteo_vistas')->default(0);
            $table->boolean('es_destacado')->default(false);
            $table->boolean('comentarios_habilitados')->default(true);
            $table->json('metadatos')->nullable();
            $table->foreignId('creado_por')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('actualizado_por')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('tipo_contenido_id');
            $table->index('dependencia_id');
            $table->index('usuario_id');
            $table->index('slug');
            $table->index('estado');
            $table->index('publicado_en');
            $table->index('es_destacado');
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
