<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * 
     * Valores de campos personalizables (EAV Pattern - Entity-Attribute-Value)
     * Almacena los valores reales de los campos definidos
     */
    public function up(): void
    {
        Schema::create('valores_campos', function (Blueprint $table) {
            $table->id();
            
            // Relación con la definición del campo
            $table->foreignId('definicion_campo_id')->constrained('definiciones_campos')->onDelete('cascade');
            
            // Relación polimórfica con la entidad (contenido, usuario, etc.)
            $table->morphs('entidad'); // entidad_tipo, entidad_id
            
            // Delta para campos múltiples (0, 1, 2, ...)
            $table->unsignedInteger('delta')->default(0);
            
            // Valores (solo uno se usa según el tipo de campo)
            $table->text('valor_texto')->nullable();
            $table->longText('valor_texto_largo')->nullable();
            $table->integer('valor_entero')->nullable();
            $table->decimal('valor_decimal', 15, 4)->nullable();
            $table->boolean('valor_booleano')->nullable();
            $table->date('valor_fecha')->nullable();
            $table->dateTime('valor_fecha_hora')->nullable();
            $table->json('valor_json')->nullable();
            
            // Referencia a otra entidad (para campos de tipo referencia)
            $table->string('referencia_tipo')->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            
            // Referencia a medio (para campos de tipo archivo)
            $table->foreignId('medio_id')->nullable()->constrained('medios')->onDelete('set null');
            
            // Auditoría
            $table->timestamps();
            
            // Índices para performance
            $table->index('definicion_campo_id');
            $table->index(['entidad_tipo', 'entidad_id']);
            $table->index('delta');
            $table->index('valor_entero');
            $table->index('valor_decimal');
            $table->index('valor_booleano');
            $table->index('valor_fecha');
            $table->index('valor_fecha_hora');
            $table->index(['referencia_tipo', 'referencia_id']);
            $table->index('medio_id');
            
            // Unique para evitar duplicados en campos no múltiples
            $table->unique(['definicion_campo_id', 'entidad_tipo', 'entidad_id', 'delta'], 'unique_campo_entidad_delta');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('valores_campos');
    }
};
