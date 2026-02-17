<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plan_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_de_desarrollo_id')->constrained('plan_de_desarrollos')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('document_type')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('plan_de_desarrollo_id');
            $table->index('document_type');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_documentos');
    }
};
