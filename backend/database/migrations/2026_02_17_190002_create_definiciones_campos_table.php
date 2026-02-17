<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Sistema de Campos Personalizables (Field API estilo Drupal)
     * Define campos adicionales que se pueden adjuntar a tipos de contenido
     */
    public function up(): void
    {
        Schema::create('definiciones_campos', function (Blueprint $table) {
            $table->id();
            
            // Identificación del campo
            $table->string('nombre'); // nombre_campo (machine name)
            $table->string('etiqueta'); // Etiqueta visible
            $table->text('descripcion')->nullable();
            $table->text('ayuda')->nullable(); // Texto de ayuda
            
            // Tipo de campo (Field API)
            $table->enum('tipo_campo', [
                'texto',           // Input text corto
                'texto_largo',     // Textarea
                'texto_formato',   // HTML/Markdown editor
                'numero_entero',   // Integer
                'numero_decimal',  // Decimal
                'booleano',        // Checkbox
                'fecha',           // Date picker
                'fecha_hora',      // DateTime picker
                'email',           // Email
                'url',             // URL
                'telefono',        // Teléfono
                'archivo',         // File upload (usa tabla medios)
                'seleccion',       // Select/Radio
                'seleccion_multiple', // Checkboxes/Multi-select
                'referencia',      // Referencia a otra entidad
                'json',            // JSON data
            ]);
            
            // Configuración del campo
            $table->json('configuracion')->nullable(); // Opciones específicas del tipo
            // Ej: { max_length: 255, allowed_values: ['opcion1', 'opcion2'], required: true }
            
            // Validaciones
            $table->boolean('es_requerido')->default(false);
            $table->boolean('es_multiple')->default(false); // Permite múltiples valores
            $table->unsignedInteger('cardinalidad')->nullable(); // Número máximo de valores (null = ilimitado)
            
            // Valores por defecto
            $table->text('valor_defecto')->nullable();
            
            // A qué entidades se puede aplicar
            $table->string('entidad_tipo')->default('contenido'); // contenido, usuario, etc.
            $table->json('tipos_contenido_ids')->nullable(); // Array de IDs de tipos_contenido
            
            // Presentación
            $table->string('widget')->nullable(); // textfield, textarea, select, checkboxes, etc.
            $table->unsignedInteger('peso')->default(0); // Orden de presentación
            
            // Estado
            $table->boolean('esta_activo')->default(true);
            
            // Auditoría
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('nombre');
            $table->index('tipo_campo');
            $table->index('entidad_tipo');
            $table->index('esta_activo');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('definiciones_campos');
    }
};
