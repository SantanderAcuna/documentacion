<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Upload a new media file.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'alt_text' => 'nullable|string',
            'caption' => 'nullable|string',
            'mediable_type' => 'nullable|string',
            'mediable_id' => 'nullable|integer',
        ]);

        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('media', $filename, 'public');

        $media = Media::create([
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'alt_text' => $request->alt_text,
            'caption' => $request->caption,
            'uploaded_by' => $request->user()->id,
            'mediable_type' => $request->mediable_type,
            'mediable_id' => $request->mediable_id,
        ]);

        return response()->json($media, 201);
    }

    /**
     * Remove the specified media.
     */
    public function destroy(Media $media): JsonResponse
    {
        // Delete file from storage
        Storage::disk('public')->delete($media->path);

        // Delete database record
        $media->delete();

        return response()->json([
            'message' => 'Archivo eliminado exitosamente',
        ]);
    }
}
