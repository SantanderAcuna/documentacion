<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Tabla pivote polimórfica para etiquetas - permite que CUALQUIER entidad tenga etiquetas
     * (contenidos, noticias, decretos, gacetas, contratos, etc.)
     * Siguiendo normalización 4FN - reemplaza tablas pivote específicas como contenido_etiqueta
     */
    public function up(): void
    {
        Schema::create('etiquetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etiqueta_id')->constrained('etiquetas')->onDelete('cascade');
            $table->morphs('etiquetable'); // etiquetable_id, etiquetable_tipo (auto-crea índice)
            $table->timestamps();
            
            // Prevenir asignaciones de etiquetas duplicadas
            $table->unique(['etiqueta_id', 'etiquetable_id', 'etiquetable_tipo'], 'etiquetables_unico');
            
            // Índice para búsquedas de etiquetas (morphs ya crea índice en etiquetable_tipo, etiquetable_id)
            $table->index('etiqueta_id');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('etiquetables');
    }
};
