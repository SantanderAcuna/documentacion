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
        Schema::create('alcaldes', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('photo')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('period');
            $table->text('bio')->nullable();
            $table->text('achievements')->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('is_current');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alcaldes');
    }
};
