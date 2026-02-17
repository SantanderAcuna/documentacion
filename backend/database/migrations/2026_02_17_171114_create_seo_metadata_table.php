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
        Schema::create('metadatos_seo', function (Blueprint $table) {
            $table->id();
            $table->morphs('metadatable');
            $table->string('titulo_meta')->nullable();
            $table->text('descripcion_meta')->nullable();
            $table->text('palabras_clave_meta')->nullable();
            $table->string('titulo_og')->nullable();
            $table->text('descripcion_og')->nullable();
            $table->string('imagen_og')->nullable();
            $table->string('url_canonica')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('metadatos_seo');
    }
};
