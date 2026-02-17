<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Eliminar tabla contenido_etiqueta - reemplazada por tabla etiquetables polimórfica
     */
    public function up(): void
    {
        Schema::dropIfExists('contenido_etiqueta');
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::create('contenido_etiqueta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contenido_id')->constrained('contenidos')->onDelete('cascade');
            $table->foreignId('etiqueta_id')->constrained('etiquetas')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['contenido_id', 'etiqueta_id']);
        });
    }
};
