<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Polymorphic pivot table for tags - allows ANY entity to have tags
     * (contents, news, decrees, gazettes, contracts, etc.)
     * Following 4FN normalization - replaces specific pivot tables like content_tag
     */
    public function up(): void
    {
        Schema::create('taggables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->morphs('taggable'); // taggable_id, taggable_type (auto-creates index)
            $table->timestamps();
            
            // Prevent duplicate tag assignments
            $table->unique(['tag_id', 'taggable_id', 'taggable_type'], 'taggables_unique');
            
            // Index for tag lookups (morphs already creates index on taggable_type, taggable_id)
            $table->index('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taggables');
    }
};
