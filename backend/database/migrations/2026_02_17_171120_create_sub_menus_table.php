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
        Schema::create('submenus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->string('nombre');
            $table->string('slug');
            $table->string('icono')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('esta_activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('menu_id');
            $table->index('slug');
            $table->index('esta_activo');
            $table->index('orden');
            $table->unique(['menu_id', 'slug']);
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('submenus');
    }
};
