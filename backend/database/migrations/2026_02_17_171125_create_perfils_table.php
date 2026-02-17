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
        Schema::create('perfiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')->unique()->constrained('funcionarios')->onDelete('cascade');
            $table->string('foto')->nullable();
            $table->text('biografia')->nullable();
            $table->json('educacion')->nullable();
            $table->json('certificaciones')->nullable();
            $table->string('correo_contacto')->nullable();
            $table->string('telefono_contacto')->nullable();
            $table->timestamps();
            
            $table->index('funcionario_id');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfiles');
    }
};
