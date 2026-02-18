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
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->string('nombre_completo');
            $table->string('numero_identificacion');
            $table->foreignId('cargo_id')->nullable()->constrained('cargos')->onDelete('set null');
            $table->foreignId('dependencia_id')->nullable()->constrained('dependencias')->onDelete('set null');
            $table->date('fecha_contratacion');
            $table->enum('tipo_contrato', ['planta', 'contrato', 'provisional']);
            $table->enum('estado', ['activo', 'inactivo', 'vacaciones', 'licencia'])->default('activo');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('usuario_id');
            $table->index('numero_identificacion');
            $table->index('cargo_id');
            $table->index('dependencia_id');
            $table->index('tipo_contrato');
            $table->index('estado');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionarios');
    }
};
