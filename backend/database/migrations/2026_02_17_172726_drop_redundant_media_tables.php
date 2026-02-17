<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Drop redundant media tables since we have a centralized polymorphic media table
     * that handles ALL multimedia types (images, videos, audio, documents).
     */
    public function up(): void
    {
        // Drop news_media table - will use polymorphic media table instead
        Schema::dropIfExists('news_media');
        
        // Drop news_tags table - will use the main tags table with polymorphic relation
        Schema::dropIfExists('news_tags');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate news_media if needed for rollback
        Schema::create('news_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type');
            $table->string('alt_text')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        
        // Recreate news_tags if needed for rollback
        Schema::create('news_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained()->onDelete('cascade');
            $table->string('tag_name');
            $table->timestamps();
        });
    }
};
