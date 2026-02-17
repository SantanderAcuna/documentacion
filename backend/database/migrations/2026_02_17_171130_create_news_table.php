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
        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_noticia_id')->constrained('categorias_noticias')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('resumen')->nullable();
            $table->longText('contenido');
            $table->string('imagen_destacada')->nullable();
            $table->enum('estado', ['borrador', 'publicado', 'archivado'])->default('borrador');
            $table->timestamp('publicado_en')->nullable();
            $table->unsignedBigInteger('conteo_vistas')->default(0);
            $table->boolean('es_destacado')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('categoria_noticia_id');
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
        Schema::dropIfExists('noticias');
    }
};
