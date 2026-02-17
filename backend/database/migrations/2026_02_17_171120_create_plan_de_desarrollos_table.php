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
        Schema::create('planes_desarrollo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alcalde_id')->constrained('alcaldes')->onDelete('cascade');
            $table->string('nombre');
            $table->string('periodo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->enum('estado', ['borrador', 'activo', 'completado', 'archivado'])->default('borrador');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('alcalde_id');
            $table->index('estado');
            $table->index('fecha_inicio');
            $table->index('fecha_fin');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('planes_desarrollo');
    }
};
