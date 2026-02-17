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
        Schema::create('datos_abiertos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('formato', ['csv', 'json', 'xml', 'xlsx', 'pdf'])->default('csv');
            $table->string('categoria');
            $table->string('ruta_archivo')->nullable();
            $table->string('url_archivo')->nullable();
            $table->integer('conteo_registros')->default(0);
            $table->timestamp('actualizado_ultima_vez_en')->nullable();
            $table->foreignId('dependencia_id')->nullable()->constrained('dependencias')->onDelete('set null');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->enum('estado', ['borrador', 'publicado', 'archivado'])->default('borrador');
            $table->json('metadatos')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('nombre');
            $table->index('formato');
            $table->index('categoria');
            $table->index('estado');
            $table->index('actualizado_ultima_vez_en');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_abiertos');
    }
};
