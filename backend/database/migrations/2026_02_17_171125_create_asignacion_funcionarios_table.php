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
        Schema::create('asignaciones_funcionarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')->constrained('funcionarios')->onDelete('cascade');
            $table->foreignId('dependencia_id')->constrained('dependencias')->onDelete('cascade');
            $table->foreignId('cargo_id')->constrained('cargos')->onDelete('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->boolean('es_actual')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index('funcionario_id');
            $table->index('dependencia_id');
            $table->index('cargo_id');
            $table->index('es_actual');
            $table->index('fecha_inicio');
            $table->index('fecha_fin');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones_funcionarios');
    }
};
