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
        Schema::create('plan_de_desarrollos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alcalde_id')->constrained('alcaldes')->onDelete('cascade');
            $table->string('name');
            $table->string('period');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['draft', 'active', 'completed', 'archived'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('alcalde_id');
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_de_desarrollos');
    }
};
