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
        Schema::create('documentos_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_desarrollo_id')->constrained('planes_desarrollo')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('ruta_archivo');
            $table->string('nombre_archivo');
            $table->string('tipo_documento')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('plan_desarrollo_id');
            $table->index('tipo_documento');
            $table->index('orden');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_plan');
    }
};
