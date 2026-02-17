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
        Schema::create('tipos_contenido', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // posts, blogs, noticias, páginas, eventos
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->string('icono')->nullable();
            $table->boolean('esta_activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('slug');
            $table->index('esta_activo');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_contenido');
    }
};
