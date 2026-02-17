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
        Schema::create('metadatos_cumplimiento', function (Blueprint $table) {
            $table->id();
            $table->morphs('cumplidor');
            $table->string('referencia_ley');
            $table->enum('estado_cumplimiento', ['cumple', 'no_cumple', 'pendiente'])->default('pendiente');
            $table->date('fecha_validacion')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            
            $table->index('referencia_ley');
            $table->index('estado_cumplimiento');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('metadatos_cumplimiento');
    }
};
