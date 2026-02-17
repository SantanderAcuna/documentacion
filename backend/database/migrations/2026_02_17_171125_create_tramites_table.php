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
        Schema::create('tramites', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->text('descripcion')->nullable();
            $table->text('requisitos')->nullable();
            $table->foreignId('dependencia_id')->nullable()->constrained('dependencias')->onDelete('set null');
            $table->integer('dias_duracion')->nullable();
            $table->decimal('costo', 10, 2)->default(0);
            $table->boolean('tiene_costo')->default(false);
            $table->json('pasos')->nullable();
            $table->json('info_contacto')->nullable();
            $table->boolean('esta_activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('codigo');
            $table->index('dependencia_id');
            $table->index('esta_activo');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('tramites');
    }
};
