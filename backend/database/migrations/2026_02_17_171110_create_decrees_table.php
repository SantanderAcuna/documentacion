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
        Schema::create('decretos', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique(); // decreto-001-2026
            $table->string('titulo');
            $table->text('resumen')->nullable();
            $table->longText('contenido');
            $table->date('fecha_emision');
            $table->date('fecha_publicacion')->nullable();
            $table->foreignId('dependencia_id')->nullable()->constrained('dependencias')->onDelete('set null');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('ruta_archivo'); // storage/decretos/2026/decreto-001-2026.pdf
            $table->string('nombre_archivo');
            $table->enum('estado', ['borrador', 'publicado', 'archivado'])->default('borrador');
            $table->json('metadatos')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('numero');
            $table->index('fecha_emision');
            $table->index('estado');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('decretos');
    }
};
