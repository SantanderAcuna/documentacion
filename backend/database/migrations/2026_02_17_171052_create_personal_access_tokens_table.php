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
        Schema::create('tokens_acceso_personal', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->text('nombre');
            $table->string('token', 64)->unique();
            $table->text('habilidades')->nullable();
            $table->timestamp('usado_ultima_vez_en')->nullable();
            $table->timestamp('expira_en')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('tokens_acceso_personal');
    }
};
