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
        Schema::create('compliance_metadata', function (Blueprint $table) {
            $table->id();
            $table->morphs('compliant');
            $table->string('law_reference');
            $table->enum('compliance_status', ['compliant', 'non_compliant', 'pending'])->default('pending');
            $table->date('validation_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['compliant_type', 'compliant_id']);
            $table->index('law_reference');
            $table->index('compliance_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_metadata');
    }
};
