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
        Schema::create('trabajos', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('creado_en');
        });

        Schema::create('lotes_trabajos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nombre');
            $table->integer('total_trabajos');
            $table->integer('trabajos_pendientes');
            $table->integer('trabajos_fallidos');
            $table->longText('ids_trabajos_fallidos');
            $table->mediumText('opciones')->nullable();
            $table->integer('cancelado_en')->nullable();
            $table->integer('creado_en');
            $table->integer('finalizado_en')->nullable();
        });

        Schema::create('trabajos_fallidos', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('conexion');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('excepcion');
            $table->timestamp('fallo_en')->useCurrent();
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajos');
        Schema::dropIfExists('lotes_trabajos');
        Schema::dropIfExists('trabajos_fallidos');
    }
};
