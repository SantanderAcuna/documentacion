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
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_contrato')->unique();
            $table->string('titulo');
            $table->string('nombre_contratista');
            $table->string('id_contratista');
            $table->string('tipo_contrato');
            $table->decimal('monto', 15, 2);
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['activo', 'completado', 'terminado'])->default('activo');
            $table->string('url_secop')->nullable();
            $table->string('ruta_archivo')->nullable();
            $table->foreignId('dependencia_id')->nullable()->constrained('dependencias')->onDelete('set null');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->json('metadatos')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('numero_contrato');
            $table->index('id_contratista');
            $table->index('tipo_contrato');
            $table->index('fecha_inicio');
            $table->index('fecha_fin');
            $table->index('estado');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
