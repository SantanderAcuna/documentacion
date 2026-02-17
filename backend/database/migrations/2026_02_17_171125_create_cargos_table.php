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
        Schema::create('cargos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->enum('nivel', ['directivo', 'profesional', 'tecnico', 'asistencial']);
            $table->foreignId('dependencia_id')->nullable()->constrained('dependencias')->onDelete('set null');
            $table->text('descripcion')->nullable();
            $table->text('requisitos')->nullable();
            $table->boolean('esta_activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('codigo');
            $table->index('nivel');
            $table->index('dependencia_id');
            $table->index('esta_activo');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('cargos');
    }
};
