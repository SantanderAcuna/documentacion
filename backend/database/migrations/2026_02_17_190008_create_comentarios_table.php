<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Sistema de Comentarios polimórfico (estilo Drupal)
     */
    public function up(): void
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            
            // Relación polimórfica con la entidad comentada
            $table->morphs('comentable'); // comentable_tipo, comentable_id
            
            // Threading (comentarios anidados)
            $table->foreignId('padre_id')->nullable()->constrained('comentarios')->onDelete('cascade');
            $table->unsignedInteger('nivel')->default(0); // Nivel de anidación (0 = raíz)
            $table->string('hilo')->nullable(); // Ej: "1/3/5" para jerarquía
            
            // Autor (puede ser usuario registrado o anónimo)
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->onDelete('cascade');
            $table->string('nombre_autor')->nullable(); // Para anónimos
            $table->string('email_autor')->nullable(); // Para notificaciones
            $table->ipAddress('ip_autor')->nullable();
            
            // Contenido del comentario
            $table->string('asunto')->nullable();
            $table->text('cuerpo');
            
            // Moderación
            $table->enum('estado', ['pendiente', 'aprobado', 'spam', 'rechazado'])->default('aprobado');
            $table->foreignId('moderado_por')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamp('moderado_en')->nullable();
            
            // Interacciones
            $table->unsignedInteger('conteo_likes')->default(0);
            $table->unsignedInteger('conteo_dislikes')->default(0);
            
            // Auditoría
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['comentable_tipo', 'comentable_id']);
            $table->index('padre_id');
            $table->index('usuario_id');
            $table->index('estado');
            $table->index('nivel');
            $table->index('created_at');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
