<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Tabla pivot polimórfica para categorías - permite que CUALQUIER entidad tenga categorías
     * (contenidos, noticias, decretos, gacetas, contratos, etc.)
     * Siguiendo normalización 4FN - reemplaza tablas específicas como contenido_categoria
     */
    public function up(): void
    {
        Schema::create('categorizables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->morphs('categorizable'); // categorizable_id, categorizable_tipo (auto-crea índice)
            $table->timestamps();
            
            // Prevenir asignaciones duplicadas de categorías
            $table->unique(['categoria_id', 'categorizable_id', 'categorizable_tipo'], 'categorizables_unique');
            
            // Índice para búsquedas por categoría (morphs ya crea índice en categorizable_tipo, categorizable_id)
            $table->index('categoria_id');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorizables');
    }
};
