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
        Schema::create('items_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->foreignId('submenu_id')->nullable()->constrained('submenus')->onDelete('cascade');
            $table->string('nombre');
            $table->string('slug');
            $table->string('url');
            $table->string('icono')->nullable();
            $table->enum('destino', ['_blank', '_self'])->default('_self');
            $table->integer('orden')->default(0);
            $table->boolean('esta_activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('menu_id');
            $table->index('submenu_id');
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
        Schema::dropIfExists('items_menu');
    }
};
