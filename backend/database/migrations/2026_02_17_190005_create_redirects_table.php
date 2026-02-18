<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Sistema de Redirecciones automáticas
     */
    public function up(): void
    {
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            
            // URL origen (la antigua)
            $table->string('ruta_origen')->unique();
            
            // URL destino (la nueva)
            $table->string('ruta_destino');
            
            // Tipo de redirect
            $table->enum('tipo_redirect', ['301', '302', '307'])->default('301');
            // 301 = Permanent, 302 = Temporary, 307 = Temporary (mantiene método HTTP)
            
            // Contador de usos
            $table->unsignedInteger('conteo_accesos')->default(0);
            $table->timestamp('ultimo_acceso')->nullable();
            
            // Estado
            $table->boolean('esta_activo')->default(true);
            
            // Auditoría
            $table->foreignId('creado_por')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamps();
            
            // Índices
            $table->index('ruta_origen');
            $table->index('ruta_destino');
            $table->index('esta_activo');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
