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
        Schema::create('decrees', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // decreto-001-2026
            $table->string('title');
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->date('issue_date');
            $table->date('publication_date')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('file_path'); // storage/decretos/2026/decreto-001-2026.pdf
            $table->string('file_name');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('number');
            $table->index('issue_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decrees');
    }
};
