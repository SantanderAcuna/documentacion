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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relation - can attach to ANY entity
            $table->morphs('mediable');
            
            // Basic file information
            $table->string('name'); // User-friendly name
            $table->string('file_name'); // Original filename
            $table->string('mime_type'); // image/jpeg, video/mp4, audio/mpeg, application/pdf
            $table->string('disk')->default('public'); // storage disk
            $table->string('path'); // Full path: storage/{component}/{year}/{filename}
            $table->unsignedBigInteger('size'); // File size in bytes
            
            // Media type classification
            $table->enum('media_type', [
                'image',      // jpg, png, gif, webp, svg
                'video',      // mp4, avi, mov, webm
                'audio',      // mp3, wav, ogg
                'document',   // pdf, doc, docx, xls, xlsx
                'archive',    // zip, rar
                'other'
            ]);
            
            // Image/Video specific fields
            $table->string('alt_text')->nullable(); // For accessibility (WCAG 2.1 AA)
            $table->unsignedInteger('width')->nullable(); // Image/video width
            $table->unsignedInteger('height')->nullable(); // Image/video height
            $table->unsignedInteger('duration')->nullable(); // Video/audio duration in seconds
            
            // Thumbnails and conversions
            $table->string('thumbnail_path')->nullable(); // Auto-generated thumbnail
            $table->json('conversions')->nullable(); // Different sizes/formats
            
            // Metadata and descriptions
            $table->text('description')->nullable();
            $table->text('caption')->nullable(); // For images/videos
            $table->string('copyright')->nullable(); // Copyright info
            
            // Organization
            $table->string('collection')->nullable(); // Group related media
            $table->integer('order')->default(0); // Sort order
            $table->boolean('is_featured')->default(false);
            
            // Extended metadata (for videos: resolution, codec; for audio: bitrate, etc.)
            $table->json('metadata')->nullable();
            
            // Audit fields
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance (morphs() already creates index on mediable_type, mediable_id)
            $table->index('media_type');
            $table->index('mime_type');
            $table->index('collection');
            $table->index('is_featured');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
