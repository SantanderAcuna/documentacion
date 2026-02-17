<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Eliminar tablas redundantes que ahora se manejan en la tabla centralizada 'contenidos'
 * 
 * Las siguientes entidades ahora son tipos de contenido en lugar de tablas separadas:
 * - Decretos
 * - Gacetas
 * - Circulares
 * - Actas (Minutes)
 * - Noticias (News)
 */
return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        // Eliminar tablas redundantes que ahora son tipos de contenido
        Schema::dropIfExists('decretos');
        Schema::dropIfExists('gacetas');
        Schema::dropIfExists('circulares');
        Schema::dropIfExists('actas');
        Schema::dropIfExists('noticias');
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        // No se puede revertir - las tablas se deben recrear manualmente si es necesario
        // La información ahora está en la tabla 'contenidos'
    }
};
