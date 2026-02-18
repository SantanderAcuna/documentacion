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
        Schema::create('presupuesto', function (Blueprint $table) {
            $table->id();
            $table->year('ano');
            $table->string('categoria');
            $table->string('subcategoria')->nullable();
            $table->text('descripcion')->nullable();
            $table->decimal('monto_asignado', 15, 2)->default(0);
            $table->decimal('monto_ejecutado', 15, 2)->default(0);
            $table->decimal('monto_disponible', 15, 2)->default(0);
            $table->foreignId('dependencia_id')->nullable()->constrained('dependencias')->onDelete('set null');
            $table->enum('estado', ['planificado', 'ejecutando', 'completado'])->default('planificado');
            $table->json('metadatos')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('ano');
            $table->index('categoria');
            $table->index('subcategoria');
            $table->index('estado');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('presupuesto');
    }
};
