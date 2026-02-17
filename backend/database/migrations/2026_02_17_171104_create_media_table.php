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
        Schema::create('medios', function (Blueprint $table) {
            $table->id();
            
            // Relación polimórfica - puede adjuntarse a CUALQUIER entidad
            $table->morphs('medio');
            
            // Información básica del archivo
            $table->string('nombre'); // Nombre amigable
            $table->string('nombre_archivo'); // Nombre de archivo original
            $table->string('tipo_mime'); // image/jpeg, video/mp4, audio/mpeg, application/pdf
            $table->string('disco')->default('public'); // disco de almacenamiento
            $table->string('ruta'); // Ruta completa: storage/{componente}/{año}/{nombre_archivo}
            $table->unsignedBigInteger('tamano'); // Tamaño del archivo en bytes
            
            // Clasificación del tipo de medio
            $table->enum('tipo_medio', [
                'imagen',      // jpg, png, gif, webp, svg
                'video',       // mp4, avi, mov, webm
                'audio',       // mp3, wav, ogg
                'documento',   // pdf, doc, docx, xls, xlsx
                'archivo',     // zip, rar
                'otro'
            ]);
            
            // Campos específicos para imágenes/videos
            $table->string('texto_alternativo')->nullable(); // Para accesibilidad (WCAG 2.1 AA)
            $table->unsignedInteger('ancho')->nullable(); // Ancho de imagen/video
            $table->unsignedInteger('alto')->nullable(); // Alto de imagen/video
            $table->unsignedInteger('duracion')->nullable(); // Duración de video/audio en segundos
            
            // Miniaturas y conversiones
            $table->string('ruta_miniatura')->nullable(); // Miniatura autogenerada
            $table->json('conversiones')->nullable(); // Diferentes tamaños/formatos
            
            // Metadatos y descripciones
            $table->text('descripcion')->nullable();
            $table->text('leyenda')->nullable(); // Para imágenes/videos
            $table->string('derechos_autor')->nullable(); // Información de derechos de autor
            
            // Organización
            $table->string('coleccion')->nullable(); // Agrupar medios relacionados
            $table->integer('orden')->default(0); // Orden de clasificación
            $table->boolean('es_destacado')->default(false);
            
            // Metadatos extendidos (para videos: resolución, codec; para audio: bitrate, etc.)
            $table->json('metadatos')->nullable();
            
            // Campos de auditoría
            $table->foreignId('subido_por')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para rendimiento (morphs() ya crea índice en medio_tipo, medio_id)
            $table->index('tipo_medio');
            $table->index('tipo_mime');
            $table->index('coleccion');
            $table->index('es_destacado');
            $table->index('subido_por');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('medios');
    }
};
