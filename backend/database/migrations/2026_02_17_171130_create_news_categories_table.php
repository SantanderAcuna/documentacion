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
            
            $table->index('slug');
            $table->index('esta_activo');
            $table->index('orden');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_noticias');
    }
};
