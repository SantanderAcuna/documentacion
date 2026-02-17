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
        Schema::create('alcaldes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo');
            $table->string('foto')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->string('periodo');
            $table->text('biografia')->nullable();
            $table->text('logros')->nullable();
            $table->boolean('es_actual')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
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
        Schema::dropIfExists('alcaldes');
    }
};
