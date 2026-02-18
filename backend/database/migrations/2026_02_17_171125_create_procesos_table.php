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
        Schema::create('procesos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('macro_proceso_id')->constrained('macro_procesos')->onDelete('cascade');
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->text('descripcion')->nullable();
            $table->foreignId('dependencia_responsable_id')->nullable()->constrained('dependencias')->onDelete('set null');
            $table->integer('orden')->default(0);
            $table->boolean('esta_activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('macro_proceso_id');
            $table->index('codigo');
            $table->index('dependencia_responsable_id');
            $table->index('esta_activo');
            $table->index('orden');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesos');
    }
};
