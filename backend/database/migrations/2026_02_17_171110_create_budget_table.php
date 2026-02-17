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
        Schema::create('budget', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->string('category');
            $table->string('subcategory')->nullable();
            $table->text('description')->nullable();
            $table->decimal('assigned_amount', 15, 2)->default(0);
            $table->decimal('executed_amount', 15, 2)->default(0);
            $table->decimal('available_amount', 15, 2)->default(0);
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['planned', 'executing', 'completed'])->default('planned');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('year');
            $table->index('category');
            $table->index('subcategory');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget');
    }
};
