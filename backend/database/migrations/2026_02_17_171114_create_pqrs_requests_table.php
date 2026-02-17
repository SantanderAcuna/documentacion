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
        Schema::create('pqrs_requests', function (Blueprint $table) {
            $table->id();
            $table->string('filing_number')->unique();
            $table->enum('request_type', ['peticion', 'queja', 'reclamo', 'sugerencia']);
            $table->string('citizen_name');
            $table->string('citizen_email');
            $table->string('citizen_phone')->nullable();
            $table->string('citizen_id_number')->nullable();
            $table->string('subject');
            $table->text('description');
            $table->enum('status', ['received', 'in_process', 'responded', 'closed'])->default('received');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('filed_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();
            $table->text('response_text')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('filing_number');
            $table->index('request_type');
            $table->index('status');
            $table->index('priority');
            $table->index('filed_at');
            $table->index('citizen_email');
            $table->fullText(['subject', 'description', 'response_text']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pqrs_requests');
    }
};
