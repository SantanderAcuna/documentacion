<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Eliminar tablas de medios redundantes ya que tenemos una tabla de medios polimórfica centralizada
     * que maneja TODOS los tipos multimedia (imágenes, videos, audio, documentos).
     */
    public function up(): void
    {
        // Eliminar tabla medios_noticias - usaremos la tabla de medios polimórfica en su lugar
        Schema::dropIfExists('medios_noticias');
        
        // Eliminar tabla etiquetas_noticias - usaremos la tabla principal de etiquetas con relación polimórfica
        Schema::dropIfExists('etiquetas_noticias');
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        // Recrear medios_noticias si es necesario para rollback
        Schema::create('medios_noticias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('noticia_id')->constrained('noticias')->onDelete('cascade');
            $table->string('ruta_archivo');
            $table->string('nombre_archivo');
            $table->string('tipo_mime');
            $table->string('texto_alternativo')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
        
        // Recrear etiquetas_noticias si es necesario para rollback
        Schema::create('etiquetas_noticias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('noticia_id')->constrained('noticias')->onDelete('cascade');
            $table->string('nombre_etiqueta');
            $table->timestamps();
        });
    }
};
