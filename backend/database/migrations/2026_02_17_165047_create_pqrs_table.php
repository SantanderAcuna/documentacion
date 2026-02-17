<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pqrs', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->enum('tipo', ['peticion', 'queja', 'reclamo', 'sugerencia']);
            $table->string('nombre');
            $table->string('email');
            $table->string('telefono')->nullable();
            $table->string('documento')->nullable();
            $table->string('asunto');
            $table->longText('mensaje');
            $table->enum('estado', ['nuevo', 'en_proceso', 'resuelto', 'cerrado'])->default('nuevo');
            $table->text('respuesta')->nullable();
            $table->timestamp('respondido_at')->nullable();
            $table->foreignId('respondido_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['estado', 'created_at']);
            $table->index('tipo');
            $table->fullText(['asunto', 'mensaje']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pqrs');
    }
};
