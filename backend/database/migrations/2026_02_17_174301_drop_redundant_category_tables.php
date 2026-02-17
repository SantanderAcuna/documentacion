<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Eliminar tablas redundantes de categorías ya que tenemos una tabla polimórfica centralizada
     * que maneja TODAS las categorías del sistema (contenidos, noticias, decretos, etc.)
     */
    public function up(): void
    {
        // Eliminar tabla categorias_noticias - usaremos tabla categorizables polimórfica
        Schema::dropIfExists('categorias_noticias');
        
        // Eliminar tabla contenido_categoria - usaremos tabla categorizables polimórfica
        Schema::dropIfExists('contenido_categoria');
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        // Recrear categorias_noticias si es necesario para rollback
        Schema::create('categorias_noticias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->string('color')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('esta_activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Recrear contenido_categoria si es necesario para rollback
        Schema::create('contenido_categoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contenido_id')->constrained('contenidos')->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['contenido_id', 'categoria_id']);
        });
    }
};
